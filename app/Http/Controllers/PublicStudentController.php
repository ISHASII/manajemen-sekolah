<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
// Avoid importing QrCode facade directly to prevent issues if package isn't installed at runtime

class PublicStudentController extends Controller
{
    /**
     * Display public student profile by ID (no auth required)
     */
    public function show($id)
    {
        $request = request();
        $student = Student::with(['user','classRoom'])->findOrFail($id);
        // only show sensitive contact info if accessed via a valid signed URL or an authenticated user
        $isValid = $request->hasValidSignature() || auth()->check();
        return view('students.public', compact('student', 'isValid'));
    }

    /**
     * Generate a server-side QR code PNG that encodes a temporary signed URL
     */
    public function qrcode($id)
    {
        $student = Student::findOrFail($id);
        // generate a temporary signed URL valid for 365 days
        $signedUrl = URL::temporarySignedRoute('students.public', now()->addDays(365), ['id' => $student->id]);

        // Server-side QR generation using Simple QrCode package
        try {
            $png = QrCode::format('png')->size(300)->generate($signedUrl);
            return response($png, 200)
                ->header('Content-Type', 'image/png')
                ->header('Cache-Control', 'public, max-age=604800');
        } catch (\Throwable $e) {
            Log::error('Server QR generation failed for student ' . $student->id . ': ' . $e->getMessage());
            // fall back to external API below
        }

        // Fallback to Google Chart API (use HTTP client to fetch PNG)
        $chartUrl = 'https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=' . urlencode($signedUrl) . '&choe=UTF-8';
        try {
            $res = Http::get($chartUrl);
            if ($res->successful()) {
                return response($res->body(), 200)
                    ->header('Content-Type', 'image/png')
                    ->header('Cache-Control', 'public, max-age=604800');
            }
        } catch (\Throwable $e) {
            Log::warning('Fallback Google Chart fetch failed for student ' . $student->id . ': ' . $e->getMessage());
        }

        // As a final fallback, return a 204 transparent PNG
        $transparent = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR4nGNgYAAAAAMAASsJTYQAAAAASUVORK5CYII=');
        return response($transparent, 200)->header('Content-Type', 'image/png');
    }
}
