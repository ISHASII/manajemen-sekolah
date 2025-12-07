<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SchoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\School::create([
            'name' => 'SLB Sharing School',
            'description' => 'Sekolah Luar Biasa yang berfokus pada pendidikan inklusif untuk anak berkebutuhan khusus dengan pendekatan sharing dan kolaboratif.',
            'vision' => 'Menjadi lembaga pendidikan inklusif terdepan yang mengembangkan potensi anak berkebutuhan khusus melalui pendekatan sharing dan kolaboratif.',
            'mission' => 'Memberikan pendidikan berkualitas tinggi dengan pendekatan individual, mengembangkan kemandirian siswa, dan membangun jaringan kolaborasi dengan keluarga dan masyarakat.',
            'address' => 'Jl. Pendidikan Inklusif No. 123, Jakarta Selatan',
            'phone' => '(021) 1234-5678',
            'email' => 'info@slbsharingschool.edu',
            'website' => 'https://slbsharingschool.edu',
            'facilities' => [
                ['name' => 'Ruang Kelas Adaptif', 'description' => 'Ruang kelas yang disesuaikan untuk berbagai kebutuhan khusus'],
                ['name' => 'Ruang Terapi', 'description' => 'Ruang untuk terapi okupasi, fisik, dan wicara'],
                ['name' => 'Laboratorium Komputer', 'description' => 'Lab komputer dengan software pembelajaran khusus'],
                ['name' => 'Perpustakaan', 'description' => 'Perpustakaan dengan koleksi buku Braille dan audio'],
                ['name' => 'Ruang Seni dan Kreativitas', 'description' => 'Ruang untuk mengembangkan bakat seni siswa'],
                ['name' => 'Lapangan Olahraga', 'description' => 'Fasilitas olahraga yang aman dan accessible'],
                ['name' => 'Kantin Sehat', 'description' => 'Kantin dengan menu sehat dan bergizi'],
                ['name' => 'Ruang Konseling', 'description' => 'Ruang untuk bimbingan konseling siswa']
            ],
            'programs' => [
                ['name' => 'Pendidikan Tunanetra', 'description' => 'Program khusus untuk siswa dengan gangguan penglihatan'],
                ['name' => 'Pendidikan Tunarungu', 'description' => 'Program khusus untuk siswa dengan gangguan pendengaran'],
                ['name' => 'Pendidikan Tunagrahita', 'description' => 'Program khusus untuk siswa dengan hambatan intelektual'],
                ['name' => 'Pendidikan Tunadaksa', 'description' => 'Program khusus untuk siswa dengan hambatan fisik motorik'],
                ['name' => 'Program Autis', 'description' => 'Program khusus untuk siswa dengan spektrum autisme'],
                ['name' => 'Program Keterampilan Hidup', 'description' => 'Program pengembangan keterampilan hidup mandiri'],
                ['name' => 'Program Vokasi', 'description' => 'Program pelatihan keterampilan kerja dan wirausaha']
            ],
            'social_media' => [
                'facebook' => 'https://facebook.com/slbsharingschool',
                'instagram' => 'https://instagram.com/slbsharingschool',
                'twitter' => 'https://twitter.com/slbsharingschool',
                'youtube' => 'https://youtube.com/slbsharingschool'
            ]
        ]);
    }
}
