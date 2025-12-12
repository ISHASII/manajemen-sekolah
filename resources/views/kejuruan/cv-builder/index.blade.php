@extends('layouts.app')

@section('title', 'CV Builder')

@section('content')
    <div class="container-fluid py-4 cv-builder">
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h2 class="mb-1">
                                    <i class="bi bi-file-earmark-person-fill me-2"></i>
                                    CV Builder
                                </h2>
                                <p class="mb-0 opacity-75">
                                    Buat CV profesional yang ATS-Friendly
                                </p>
                            </div>
                            <div class="col-md-4 text-end">
                                <a href="{{ route('kejuruan.dashboard') }}" class="btn btn-light">
                                    <i class="bi bi-arrow-left me-2"></i>Kembali
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('kejuruan.cv.print') }}" method="POST" id="cvForm" target="_blank">
            @csrf

            <!-- Section 1: Informasi Pribadi -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-person-fill me-2 text-primary"></i>Informasi Pribadi</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="full_name" class="form-label">Nama Lengkap <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="full_name" name="full_name"
                                value="{{ old('full_name', $user->name ?? '') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="city" class="form-label">Kota/Lokasi</label>
                            <input type="text" class="form-control" id="city" name="city"
                                value="{{ old('city', $student->city ?? '') }}" placeholder="contoh: Depok, Indonesia">
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Nomor HP <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" id="phone" name="phone"
                                value="{{ old('phone', $student->phone ?? '') }}" placeholder="+62 812-xxxx-xxxx" required>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email Profesional <span
                                    class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email"
                                value="{{ old('email', $user->email ?? '') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="linkedin" class="form-label">LinkedIn URL</label>
                            <input type="url" class="form-control" id="linkedin" name="linkedin"
                                value="{{ old('linkedin') }}" placeholder="https://linkedin.com/in/username">
                        </div>
                        <div class="col-md-6">
                            <label for="portfolio_link" class="form-label">Link Portofolio/Website</label>
                            <input type="url" class="form-control" id="portfolio_link" name="portfolio_link"
                                value="{{ old('portfolio_link') }}" placeholder="https://portfolio.com">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Ringkasan Profil -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-chat-left-text-fill me-2 text-primary"></i>Ringkasan Profil</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="profile_summary" class="form-label">Deskripsi Singkat tentang Diri Anda</label>
                            <textarea class="form-control" id="profile_summary" name="profile_summary" rows="4"
                                placeholder="Tuliskan ringkasan profil profesional Anda dalam 2-4 kalimat. Jelaskan keahlian utama, pengalaman, dan tujuan karir Anda.">{{ old('profile_summary') }}</textarea>
                            <small class="text-muted">Maksimal 1000 karakter. Jelaskan keahlian dan tujuan karir
                                Anda.</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 3: Pendidikan -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-mortarboard-fill me-2 text-primary"></i>Pendidikan</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="addEducation()">
                        <i class="bi bi-plus-lg"></i> Tambah
                    </button>
                </div>
                <div class="card-body" id="educationContainer">
                    <div class="education-item border rounded p-3 mb-3" data-index="0">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Sekolah/Kampus</label>
                                <input type="text" class="form-control" name="education[0][school]"
                                    placeholder="contoh: SMK Negeri 1 Jakarta">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Jurusan/Program Studi</label>
                                <input type="text" class="form-control" name="education[0][major]"
                                    placeholder="contoh: Teknik Komputer dan Jaringan">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tahun Masuk</label>
                                <input type="text" class="form-control" name="education[0][year_start]" placeholder="2020">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tahun Lulus</label>
                                <input type="text" class="form-control" name="education[0][year_end]"
                                    placeholder="2023 / Sekarang">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">IPK/Nilai (Opsional)</label>
                                <input type="text" class="form-control" name="education[0][gpa]" placeholder="3.50">
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="button" class="btn btn-outline-danger btn-sm w-100"
                                    onclick="removeItem(this, '.education-item')">
                                    <i class="bi bi-trash"></i> Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 4: Pengalaman Kerja -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-briefcase-fill me-2 text-primary"></i>Pengalaman Kerja (Opsional)</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="addExperience()">
                        <i class="bi bi-plus-lg"></i> Tambah
                    </button>
                </div>
                <div class="card-body" id="experienceContainer">
                    <div class="experience-item border rounded p-3 mb-3" data-index="0">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Perusahaan</label>
                                <input type="text" class="form-control" name="experience[0][company]"
                                    placeholder="contoh: PT ABC Indonesia">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Posisi/Jabatan</label>
                                <input type="text" class="form-control" name="experience[0][position]"
                                    placeholder="contoh: Magang IT Support">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tahun Mulai</label>
                                <input type="text" class="form-control" name="experience[0][year_start]" placeholder="2023">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tahun Selesai</label>
                                <input type="text" class="form-control" name="experience[0][year_end]"
                                    placeholder="Sekarang">
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <button type="button" class="btn btn-outline-danger btn-sm"
                                    onclick="removeItem(this, '.experience-item')">
                                    <i class="bi bi-trash"></i> Hapus
                                </button>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Deskripsi Pekerjaan</label>
                                <textarea class="form-control" name="experience[0][description]" rows="3"
                                    placeholder="Jelaskan tanggung jawab dan pencapaian Anda..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 5: Keahlian -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-tools me-2 text-primary"></i>Keahlian</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="addSkill()">
                        <i class="bi bi-plus-lg"></i> Tambah
                    </button>
                </div>
                <div class="card-body">
                    <div id="skillsContainer" class="row g-2">
                        @if($student && $student->skills && $student->skills->count() > 0)
                            @foreach($student->skills as $index => $skill)
                                <div class="col-md-3 skill-item">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="skills[]" value="{{ $skill->skill_name }}">
                                        <button type="button" class="btn btn-outline-danger"
                                            onclick="this.closest('.skill-item').remove()">
                                            <i class="bi bi-x"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="col-md-3 skill-item">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="skills[]"
                                        placeholder="contoh: Microsoft Excel">
                                    <button type="button" class="btn btn-outline-danger"
                                        onclick="this.closest('.skill-item').remove()">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                    <small class="text-muted mt-2 d-block">Masukkan keahlian teknis dan soft skill yang relevan.</small>
                </div>
            </div>

            <!-- Section 6: Portofolio -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-collection-fill me-2 text-primary"></i>Portofolio Proyek</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="addPortfolio()">
                        <i class="bi bi-plus-lg"></i> Tambah
                    </button>
                </div>
                <div class="card-body" id="portfolioContainer">
                    @if($portfolios && $portfolios->count() > 0)
                        @foreach($portfolios as $index => $portfolio)
                            <div class="portfolio-item border rounded p-3 mb-3" data-index="{{ $index }}">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Nama Proyek</label>
                                        <input type="text" class="form-control" name="portfolio[{{ $index }}][title]"
                                            value="{{ $portfolio->title }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Link Proyek</label>
                                        <input type="url" class="form-control" name="portfolio[{{ $index }}][link]"
                                            value="{{ $portfolio->link ?? '' }}" placeholder="https://github.com/...">
                                    </div>
                                    <div class="col-md-10">
                                        <label class="form-label">Deskripsi Singkat</label>
                                        <input type="text" class="form-control" name="portfolio[{{ $index }}][description]"
                                            value="{{ $portfolio->description ?? '' }}"
                                            placeholder="Jelaskan proyek secara singkat...">
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <button type="button" class="btn btn-outline-danger btn-sm w-100"
                                            onclick="removeItem(this, '.portfolio-item')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="portfolio-item border rounded p-3 mb-3" data-index="0">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nama Proyek</label>
                                    <input type="text" class="form-control" name="portfolio[0][title]"
                                        placeholder="contoh: Website E-Commerce">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Link Proyek</label>
                                    <input type="url" class="form-control" name="portfolio[0][link]"
                                        placeholder="https://github.com/...">
                                </div>
                                <div class="col-md-10">
                                    <label class="form-label">Deskripsi Singkat</label>
                                    <input type="text" class="form-control" name="portfolio[0][description]"
                                        placeholder="Jelaskan proyek secara singkat...">
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="button" class="btn btn-outline-danger btn-sm w-100"
                                        onclick="removeItem(this, '.portfolio-item')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Section 7: Sertifikat/Pelatihan -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-award-fill me-2 text-primary"></i>Sertifikat & Pelatihan</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="addCertification()">
                        <i class="bi bi-plus-lg"></i> Tambah
                    </button>
                </div>
                <div class="card-body" id="certificationContainer">
                    @if($trainings && $trainings->count() > 0)
                        @foreach($trainings as $index => $training)
                            <div class="certification-item border rounded p-3 mb-3" data-index="{{ $index }}">
                                <div class="row g-3">
                                    <div class="col-md-5">
                                        <label class="form-label">Nama Sertifikat/Pelatihan</label>
                                        <input type="text" class="form-control" name="certifications[{{ $index }}][name]"
                                            value="{{ $training->name }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Penerbit/Lembaga</label>
                                        <input type="text" class="form-control" name="certifications[{{ $index }}][issuer]"
                                            value="{{ $training->instructor ?? 'Sekolah' }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Tahun</label>
                                        <input type="text" class="form-control" name="certifications[{{ $index }}][year]"
                                            value="{{ $training->start_date ? \Carbon\Carbon::parse($training->start_date)->year : '' }}">
                                    </div>
                                    <div class="col-md-1 d-flex align-items-end">
                                        <button type="button" class="btn btn-outline-danger btn-sm w-100"
                                            onclick="removeItem(this, '.certification-item')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="certification-item border rounded p-3 mb-3" data-index="0">
                            <div class="row g-3">
                                <div class="col-md-5">
                                    <label class="form-label">Nama Sertifikat/Pelatihan</label>
                                    <input type="text" class="form-control" name="certifications[0][name]"
                                        placeholder="contoh: Sertifikasi Microsoft Office">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Penerbit/Lembaga</label>
                                    <input type="text" class="form-control" name="certifications[0][issuer]"
                                        placeholder="contoh: Microsoft">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Tahun</label>
                                    <input type="text" class="form-control" name="certifications[0][year]" placeholder="2023">
                                </div>
                                <div class="col-md-1 d-flex align-items-end">
                                    <button type="button" class="btn btn-outline-danger btn-sm w-100"
                                        onclick="removeItem(this, '.certification-item')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Section 8: Bahasa -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-translate me-2 text-primary"></i>Bahasa</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="addLanguage()">
                        <i class="bi bi-plus-lg"></i> Tambah
                    </button>
                </div>
                <div class="card-body" id="languageContainer">
                    <div class="language-item border rounded p-3 mb-3" data-index="0">
                        <div class="row g-3">
                            <div class="col-md-5">
                                <label class="form-label">Bahasa</label>
                                <input type="text" class="form-control" name="languages[0][name]" value="Indonesia"
                                    placeholder="contoh: Indonesia">
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">Tingkat Kemahiran</label>
                                <select class="form-select" name="languages[0][level]">
                                    <option value="Native">Native (Bahasa Ibu)</option>
                                    <option value="Fluent">Fluent (Lancar)</option>
                                    <option value="Intermediate">Intermediate (Menengah)</option>
                                    <option value="Basic">Basic (Dasar)</option>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button" class="btn btn-outline-danger btn-sm w-100"
                                    onclick="removeItem(this, '.language-item')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="language-item border rounded p-3 mb-3" data-index="1">
                        <div class="row g-3">
                            <div class="col-md-5">
                                <label class="form-label">Bahasa</label>
                                <input type="text" class="form-control" name="languages[1][name]" value="Inggris"
                                    placeholder="contoh: Inggris">
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">Tingkat Kemahiran</label>
                                <select class="form-select" name="languages[1][level]">
                                    <option value="Native">Native (Bahasa Ibu)</option>
                                    <option value="Fluent">Fluent (Lancar)</option>
                                    <option value="Intermediate" selected>Intermediate (Menengah)</option>
                                    <option value="Basic">Basic (Dasar)</option>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button" class="btn btn-outline-danger btn-sm w-100"
                                    onclick="removeItem(this, '.language-item')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="card shadow-sm">
                <div class="card-body d-flex justify-content-between">
                    <a href="{{ route('kejuruan.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Batal
                    </a>
                    <div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-eye me-2"></i>Preview CV
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('styles')
        <style>
            /* Make all header text black inside CV Builder views */
            .cv-builder h1,
            .cv-builder h2,
            .cv-builder h3,
            .cv-builder h4,
            .cv-builder h5,
            .cv-builder .card-header,
            .cv-builder .card-header * {
                color: #000 !important;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            let educationIndex = 1;
            let experienceIndex = 1;
            let portfolioIndex = {{ $portfolios && $portfolios->count() > 0 ? $portfolios->count() : 1 }};
            let certificationIndex = {{ $trainings && $trainings->count() > 0 ? $trainings->count() : 1 }};
            let languageIndex = 2;

            function addEducation() {
                const container = document.getElementById('educationContainer');
                const html = `
                                                    <div class="education-item border rounded p-3 mb-3" data-index="${educationIndex}">
                                                        <div class="row g-3">
                                                            <div class="col-md-6">
                                                                <label class="form-label">Nama Sekolah/Kampus</label>
                                                                <input type="text" class="form-control" name="education[${educationIndex}][school]">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label">Jurusan/Program Studi</label>
                                                                <input type="text" class="form-control" name="education[${educationIndex}][major]">
                                                            </div>
                                                            <div class="col-md-3">
                                                                <label class="form-label">Tahun Masuk</label>
                                                                <input type="text" class="form-control" name="education[${educationIndex}][year_start]">
                                                            </div>
                                                            <div class="col-md-3">
                                                                <label class="form-label">Tahun Lulus</label>
                                                                <input type="text" class="form-control" name="education[${educationIndex}][year_end]">
                                                            </div>
                                                            <div class="col-md-3">
                                                                <label class="form-label">IPK/Nilai (Opsional)</label>
                                                                <input type="text" class="form-control" name="education[${educationIndex}][gpa]">
                                                            </div>
                                                            <div class="col-md-3 d-flex align-items-end">
                                                                <button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="removeItem(this, '.education-item')">
                                                                    <i class="bi bi-trash"></i> Hapus
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                `;
                container.insertAdjacentHTML('beforeend', html);
                educationIndex++;
            }

            function addExperience() {
                const container = document.getElementById('experienceContainer');
                const html = `
                                                    <div class="experience-item border rounded p-3 mb-3" data-index="${experienceIndex}">
                                                        <div class="row g-3">
                                                            <div class="col-md-6">
                                                                <label class="form-label">Nama Perusahaan</label>
                                                                <input type="text" class="form-control" name="experience[${experienceIndex}][company]">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label">Posisi/Jabatan</label>
                                                                <input type="text" class="form-control" name="experience[${experienceIndex}][position]">
                                                            </div>
                                                            <div class="col-md-3">
                                                                <label class="form-label">Tahun Mulai</label>
                                                                <input type="text" class="form-control" name="experience[${experienceIndex}][year_start]">
                                                            </div>
                                                            <div class="col-md-3">
                                                                <label class="form-label">Tahun Selesai</label>
                                                                <input type="text" class="form-control" name="experience[${experienceIndex}][year_end]">
                                                            </div>
                                                            <div class="col-md-6 d-flex align-items-end">
                                                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeItem(this, '.experience-item')">
                                                                    <i class="bi bi-trash"></i> Hapus
                                                                </button>
                                                            </div>
                                                            <div class="col-12">
                                                                <label class="form-label">Deskripsi Pekerjaan</label>
                                                                <textarea class="form-control" name="experience[${experienceIndex}][description]" rows="3"></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                `;
                container.insertAdjacentHTML('beforeend', html);
                experienceIndex++;
            }

            function addSkill() {
                const container = document.getElementById('skillsContainer');
                const html = `
                                                    <div class="col-md-3 skill-item">
                                                        <div class="input-group">
                                                            <input type="text" class="form-control" name="skills[]" placeholder="Keahlian baru">
                                                            <button type="button" class="btn btn-outline-danger" onclick="this.closest('.skill-item').remove()">
                                                                <i class="bi bi-x"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                `;
                container.insertAdjacentHTML('beforeend', html);
            }

            function addPortfolio() {
                const container = document.getElementById('portfolioContainer');
                const html = `
                                                    <div class="portfolio-item border rounded p-3 mb-3" data-index="${portfolioIndex}">
                                                        <div class="row g-3">
                                                            <div class="col-md-6">
                                                                <label class="form-label">Nama Proyek</label>
                                                                <input type="text" class="form-control" name="portfolio[${portfolioIndex}][title]">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label">Link Proyek</label>
                                                                <input type="url" class="form-control" name="portfolio[${portfolioIndex}][link]">
                                                            </div>
                                                            <div class="col-md-10">
                                                                <label class="form-label">Deskripsi Singkat</label>
                                                                <input type="text" class="form-control" name="portfolio[${portfolioIndex}][description]">
                                                            </div>
                                                            <div class="col-md-2 d-flex align-items-end">
                                                                <button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="removeItem(this, '.portfolio-item')">
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                `;
                container.insertAdjacentHTML('beforeend', html);
                portfolioIndex++;
            }

            function addCertification() {
                const container = document.getElementById('certificationContainer');
                const html = `
                                                    <div class="certification-item border rounded p-3 mb-3" data-index="${certificationIndex}">
                                                        <div class="row g-3">
                                                            <div class="col-md-5">
                                                                <label class="form-label">Nama Sertifikat/Pelatihan</label>
                                                                <input type="text" class="form-control" name="certifications[${certificationIndex}][name]">
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label class="form-label">Penerbit/Lembaga</label>
                                                                <input type="text" class="form-control" name="certifications[${certificationIndex}][issuer]">
                                                            </div>
                                                            <div class="col-md-2">
                                                                <label class="form-label">Tahun</label>
                                                                <input type="text" class="form-control" name="certifications[${certificationIndex}][year]">
                                                            </div>
                                                            <div class="col-md-1 d-flex align-items-end">
                                                                <button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="removeItem(this, '.certification-item')">
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                `;
                container.insertAdjacentHTML('beforeend', html);
                certificationIndex++;
            }

            function addLanguage() {
                const container = document.getElementById('languageContainer');
                const html = `
                                                    <div class="language-item border rounded p-3 mb-3" data-index="${languageIndex}">
                                                        <div class="row g-3">
                                                            <div class="col-md-5">
                                                                <label class="form-label">Bahasa</label>
                                                                <input type="text" class="form-control" name="languages[${languageIndex}][name]">
                                                            </div>
                                                            <div class="col-md-5">
                                                                <label class="form-label">Tingkat Kemahiran</label>
                                                                <select class="form-select" name="languages[${languageIndex}][level]">
                                                                    <option value="Native">Native (Bahasa Ibu)</option>
                                                                    <option value="Fluent">Fluent (Lancar)</option>
                                                                    <option value="Intermediate">Intermediate (Menengah)</option>
                                                                    <option value="Basic">Basic (Dasar)</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-2 d-flex align-items-end">
                                                                <button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="removeItem(this, '.language-item')">
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                `;
                container.insertAdjacentHTML('beforeend', html);
                languageIndex++;
            }

            function removeItem(button, selector) {
                button.closest(selector).remove();
            }
        </script>
    @endpush

@endsection
