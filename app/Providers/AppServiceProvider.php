<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\School;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set default pagination view to Bootstrap 5
        \Illuminate\Pagination\Paginator::defaultView('pagination::bootstrap-5');
        \Illuminate\Pagination\Paginator::defaultSimpleView('pagination::simple-bootstrap-5');

        // Share the first School record with all views so footer/contact info is available everywhere
        try {
            $school = School::first();
        } catch (\Throwable $e) {
            // in case the database isn't available during certain commands, avoid failing
            $school = null;
        }

        if (! $school) {
            $school = new School([
                'name' => config('app.name', 'Sekolah Kami'),
                'description' => 'Memberikan pendidikan terbaik untuk masa depan yang cerah.',
                'address' => '',
                'phone' => '',
                'email' => '',
                'social_media' => []
            ]);
        }

        View::share('school', $school);

        // Share day name mapping for Indonesian translation
        $daysInIndonesian = [
            'monday' => 'Senin',
            'tuesday' => 'Selasa',
            'wednesday' => 'Rabu',
            'thursday' => 'Kamis',
            'friday' => 'Jumat',
            'saturday' => 'Sabtu',
            'sunday' => 'Minggu'
        ];
        View::share('daysInIndonesian', $daysInIndonesian);
    }
}
