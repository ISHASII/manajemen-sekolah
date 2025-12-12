<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Announcement;
use App\Models\User;

class AnnouncementSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();

        // Pengumuman 1
        Announcement::create([
            'title' => 'Hari Libur Sekolah',
            'content' => 'Sekolah akan libur selama 1 minggu karena libur nasional',
            'type' => 'general',
            'target_audience' => 'all',
            'created_by' => $admin ? $admin->id : null,
            'is_active' => true,
            'publish_date' => now()->toDateString(),
            'expire_date' => now()->addWeek()->toDateString(),
        ]);

        // Pengumuman 2
        Announcement::create([
            'title' => 'Pembagian Rapor',
            'content' => 'Pembagian rapor semester akan dilaksanakan pada tanggal 20 Desember',
            'type' => 'event',
            'target_audience' => 'all',
            'created_by' => $admin ? $admin->id : null,
            'is_active' => true,
            'publish_date' => now()->addDays(3)->toDateString(),
            'expire_date' => now()->addWeeks(2)->toDateString(),
        ]);
    }
}