<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\School;
use Carbon\Carbon;

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
        // Set Carbon locale to Indonesian
        try {
            // Try multiple Indonesian locale options
            $locales = ['id_ID', 'id', 'Indonesian', 'indonesia'];
            foreach ($locales as $locale) {
                if (Carbon::setLocale($locale)) {
                    break;
                }
            }

            // Set system locale as well
            setlocale(LC_TIME, 'id_ID', 'id', 'Indonesian', 'id_ID.UTF-8', 'C');

        } catch (\Exception $e) {
            // Fallback if locale setting fails
            Carbon::setLocale('id');
        }

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
    }
}
