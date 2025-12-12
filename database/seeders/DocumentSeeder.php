<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Document;
use App\Models\Student;
use App\Models\User;

class DocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $student = Student::first();
        $admin = User::where('role', 'admin')->first();

        if (!$student) {
            return;
        }

        Document::create([
            'documentable_type' => Student::class,
            'documentable_id' => $student->id,
            'document_type' => 'report_card',
            'document_name' => 'Rapor Semester 1',
            'file_path' => 'documents/report_card_std001.pdf',
            'file_size' => 102400,
            'mime_type' => 'application/pdf',
            'is_verified' => true,
            'verified_by' => $admin ? $admin->id : null,
            'verified_at' => now(),
            'notes' => 'Dokumen rapor
        ',
        ]);
    }
}
