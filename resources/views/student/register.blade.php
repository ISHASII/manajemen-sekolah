@extends('layouts.app')

@section('title', 'Pendaftaran Siswa Baru')

@section('content')
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-9">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-warning border-0">
                        <h4 class="mb-0 fw-bold" style="color: #000;">
                            <i class="bi bi-person-plus-fill me-2" style="color: #000;"></i>Pendaftaran Siswa Baru
                        </h4>
                    </div>
                    <div class="card-body bg-white p-4 text-black">
                        <div id="formAlertContainer"></div>
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                <strong>Terdapat kesalahan:</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form action="{{ route('student.register.submit') }}" method="POST" enctype="multipart/form-data"
                            id="registrationForm">
                            @csrf

                            <!-- Data Siswa -->
                            <div class="section-divider mb-4">
                                <h5 class="section-title" style="color: #000;">
                                    <i class="bi bi-person-circle me-2" style="color: #000 !important;"></i>Data Siswa
                                </h5>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="student_name" class="form-label fw-semibold required">Nama Lengkap</label>
                                    <input type="text" class="form-control @error('student_name') is-invalid @enderror"
                                        id="student_name" name="student_name" value="{{ old('student_name') }}" required>
                                    @error('student_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="nisn" class="form-label fw-semibold required">NISN</label>
                                    <input type="text" class="form-control @error('nisn') is-invalid @enderror" id="nisn"
                                        name="nisn" value="{{ old('nisn') }}" placeholder="Nomor Induk Siswa Nasional">
                                    @error('nisn')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row g-3 mt-2">
                                <div class="col-md-6">
                                    <label for="email" class="form-label fw-semibold required">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                        name="email" value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="phone" class="form-label fw-semibold">Nomor Telepon</label>
                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone"
                                        name="phone" value="{{ old('phone') }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>



                            <div class="row g-3 mt-2">
                                <div class="col-md-6">
                                    <label for="birth_date" class="form-label fw-semibold required">Tanggal Lahir</label>
                                    <input type="date" class="form-control @error('birth_date') is-invalid @enderror"
                                        id="birth_date" name="birth_date" value="{{ old('birth_date') }}" required>
                                    @error('birth_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="place_of_birth" class="form-label fw-semibold required">Tempat Lahir</label>
                                    <input type="text" class="form-control @error('place_of_birth') is-invalid @enderror"
                                        id="place_of_birth" name="place_of_birth" value="{{ old('place_of_birth') }}"
                                        required>
                                    @error('place_of_birth')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row g-3 mt-2">
                                <div class="col-md-4">
                                    <label for="gender" class="form-label fw-semibold required">Jenis Kelamin</label>
                                    <select class="form-select @error('gender') is-invalid @enderror" id="gender"
                                        name="gender" required>
                                        <option value="">Pilih</option>
                                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Laki-laki
                                        </option>
                                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Perempuan
                                        </option>
                                    </select>
                                    @error('gender')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="religion" class="form-label fw-semibold required">Agama</label>
                                    <select id="religion" name="religion"
                                        class="form-select @error('religion') is-invalid @enderror" required>
                                        <option value="">Pilih</option>
                                        <option value="islam" {{ old('religion') == 'islam' ? 'selected' : '' }}>Islam
                                        </option>
                                        <option value="kristen" {{ old('religion') == 'kristen' ? 'selected' : '' }}>Kristen
                                        </option>
                                        <option value="katolik" {{ old('religion') == 'katolik' ? 'selected' : '' }}>Katolik
                                        </option>
                                        <option value="hindu" {{ old('religion') == 'hindu' ? 'selected' : '' }}>Hindu
                                        </option>
                                        <option value="budha" {{ old('religion') == 'budha' ? 'selected' : '' }}>Budha
                                        </option>
                                        <option value="khonghucu" {{ old('religion') == 'khonghucu' ? 'selected' : '' }}>
                                            Khonghucu</option>
                                    </select>
                                    @error('religion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="desired_class" class="form-label fw-semibold required">Kelas yang
                                        Dituju</label>
                                    <select class="form-select @error('desired_class') is-invalid @enderror"
                                        id="desired_class" name="desired_class" required>
                                        <option value="">Pilih Tingkat</option>
                                        <option value="SD" {{ old('desired_class') == 'SD' ? 'selected' : '' }}>SD</option>
                                        <option value="SMP" {{ old('desired_class') == 'SMP' ? 'selected' : '' }}>SMP</option>
                                        <option value="SMA" {{ old('desired_class') == 'SMA' ? 'selected' : '' }}>SMA</option>
                                        <option value="kejuruan" {{ old('desired_class') == 'kejuruan' ? 'selected' : '' }}>
                                            Kejuruan</option>
                                    </select>
                                    @error('desired_class')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mt-3">
                                <label for="address" class="form-label fw-semibold required">Alamat Lengkap</label>
                                <textarea class="form-control @error('address') is-invalid @enderror" id="address"
                                    name="address" rows="3" required>{{ old('address') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Data Kesehatan & Disabilitas -->
                            <div class="section-divider mt-5 mb-4">
                                <h5 class="section-title" style="color: #000;">
                                    <i class="bi bi-heart-pulse-fill me-2" style="color: #000 !important;"></i>Data
                                    Kesehatan & Disabilitas
                                </h5>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Kondisi Kesehatan</label>
                                <small class="text-muted d-block mb-2">Centang yang sesuai</small>
                                <div class="row g-2">
                                    @php
                                        $healthOpts = ['alergi' => 'Alergi', 'asthma' => 'Asma', 'heart' => 'Penyakit Jantung', 'epilepsy' => 'Epilepsi', 'other' => 'Lainnya'];
                                        $selectedHealth = old('health_conditions', []);
                                    @endphp
                                    @foreach($healthOpts as $k => $label)
                                        <div class="col-md-3 col-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="health_conditions[]"
                                                    value="{{ $label }}" id="health_{{ $k }}" {{ in_array($label, (array) $selectedHealth) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="health_{{ $k }}">{{ $label }}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <input type="text" class="form-control mt-2" name="health_conditions_other"
                                    id="health_conditions_other" placeholder="Jika 'Lainnya', sebutkan..."
                                    value="{{ old('health_conditions_other') }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Data Disabilitas</label>
                                <small class="text-muted d-block mb-2">Centang yang sesuai</small>
                                <div class="row g-2">
                                    @php
                                        $disabilityOpts = ['visual' => 'Gangguan Penglihatan', 'hearing' => 'Gangguan Pendengaran', 'physical' => 'Disabilitas Fisik', 'intellectual' => 'Disabilitas Intelektual', 'other' => 'Lainnya'];
                                        $selectedDis = old('disabilities', []);
                                    @endphp
                                    @foreach($disabilityOpts as $k => $label)
                                        <div class="col-md-3 col-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="disabilities[]"
                                                    value="{{ $label }}" id="disability_{{ $k }}" {{ in_array($label, (array) $selectedDis) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="disability_{{ $k }}">{{ $label }}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <input type="text" class="form-control mt-2" name="disabilities_other"
                                    id="disabilities_other" placeholder="Jika 'Lainnya', sebutkan..."
                                    value="{{ old('disabilities_other') }}">
                            </div>

                            <div class="mt-3">
                                <label for="medical_info" class="form-label fw-semibold">Informasi Medis Tambahan</label>
                                <textarea class="form-control @error('medical_info') is-invalid @enderror" id="medical_info"
                                    name="medical_info" rows="2"
                                    placeholder="Tambahkan informasi medis penting (alergi, kondisi kronis, dsb.)">{{ old('medical_info') }}</textarea>
                                @error('medical_info')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Data Orang Tua -->
                            <div class="section-divider mt-5 mb-4">
                                <h5 class="section-title" style="color: #000;">
                                    <i class="bi bi-people-fill me-2" style="color: #000 !important;"></i>Data Orang
                                    Tua/Wali
                                </h5>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="parent_name" class="form-label fw-semibold required">Nama Orang
                                        Tua/Wali</label>
                                    <input type="text" class="form-control @error('parent_name') is-invalid @enderror"
                                        id="parent_name" name="parent_name" value="{{ old('parent_name') }}" required>
                                    @error('parent_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="parent_phone" class="form-label fw-semibold required">Nomor Telepon</label>
                                    <input type="tel" class="form-control @error('parent_phone') is-invalid @enderror"
                                        id="parent_phone" name="parent_phone" value="{{ old('parent_phone') }}" required>
                                    @error('parent_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row g-3 mt-2">
                                <div class="col-md-6">
                                    <label for="parent_email" class="form-label fw-semibold">Email Orang Tua/Wali</label>
                                    <input type="email" class="form-control @error('parent_email') is-invalid @enderror"
                                        id="parent_email" name="parent_email" value="{{ old('parent_email') }}">
                                    @error('parent_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="parent_job" class="form-label fw-semibold">Pekerjaan Orang Tua/Wali</label>
                                    <input type="text" class="form-control @error('parent_job') is-invalid @enderror"
                                        id="parent_job" name="parent_job" value="{{ old('parent_job') }}">
                                    @error('parent_job')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mt-3">
                                <label for="parent_address" class="form-label fw-semibold required">Alamat Orang
                                    Tua/Wali</label>
                                <input type="text" class="form-control @error('parent_address') is-invalid @enderror"
                                    id="parent_address" name="parent_address" value="{{ old('parent_address') }}" required>
                                @error('parent_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mt-3">
                                <label class="form-label fw-semibold">Status Anak</label>
                                <div class="row g-2">
                                    <div class="col-md-3 col-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="orphan_status"
                                                id="orphan_none" value="none" {{ old('orphan_status', 'none') == 'none' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="orphan_none">Tidak Ada</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="orphan_status"
                                                id="orphan_yatim" value="yatim" {{ old('orphan_status') == 'yatim' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="orphan_yatim">Yatim</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="orphan_status"
                                                id="orphan_piatu" value="piatu" {{ old('orphan_status') == 'piatu' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="orphan_piatu">Piatu</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="orphan_status"
                                                id="orphan_both" value="yatim_piatu" {{ old('orphan_status') == 'yatim_piatu' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="orphan_both">Yatim & Piatu</label>
                                        </div>
                                    </div>
                                </div>
                                @error('orphan_status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Dokumen Pendukung -->
                            <div class="section-divider mt-5 mb-4">
                                <h5 class="section-title" style="color: #000;">
                                    <i class="bi bi-file-earmark-text-fill me-2" style="color: #000 !important;"></i>Dokumen
                                    Pendukung
                                </h5>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="birth_certificate" class="form-label fw-semibold required">Akta
                                        Kelahiran</label>
                                    <input type="file" class="form-control @error('birth_certificate') is-invalid @enderror"
                                        id="birth_certificate" name="birth_certificate" accept=".pdf,.jpg,.jpeg,.png"
                                        required>
                                    <small class="text-muted">PDF/JPG/PNG, Max 2MB</small>
                                    @error('birth_certificate')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="kk" class="form-label fw-semibold">Kartu Keluarga (KK)</label>
                                    <input type="file" class="form-control @error('kk') is-invalid @enderror" id="kk"
                                        name="kk" accept=".pdf,.jpg,.jpeg,.png">
                                    <small class="text-muted">PDF/JPG/PNG, Max 2MB</small>
                                    @error('kk')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row g-3 mt-2">
                                <div class="col-md-6">
                                    <label for="last_certificate" class="form-label fw-semibold">Ijazah/Surat Keterangan
                                        Lulus</label>
                                    <input type="file" class="form-control @error('last_certificate') is-invalid @enderror"
                                        id="last_certificate" name="last_certificate" accept=".pdf,.jpg,.jpeg,.png">
                                    <small class="text-muted">Opsional - PDF/JPG/PNG, Max 2MB</small>
                                    @error('last_certificate')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="photo" class="form-label fw-semibold required">Pas Foto 3x4</label>
                                    <input type="file" class="form-control @error('photo') is-invalid @enderror" id="photo"
                                        name="photo" accept=".jpg,.jpeg,.png" required>
                                    <small class="text-muted">JPG/PNG, Max 2MB</small>
                                    @error('photo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mt-3">
                                <label for="medical_certificate" class="form-label fw-semibold">Sertifikat Medis</label>
                                <input type="file" class="form-control @error('medical_certificate') is-invalid @enderror"
                                    id="medical_certificate" name="medical_certificate" accept=".pdf,.jpg,.jpeg,.png">
                                <small class="text-muted">Opsional - PDF/JPG/PNG, Max 2MB</small>
                                @error('medical_certificate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Motivasi -->
                            <div class="section-divider mt-5 mb-4">
                                <h5 class="section-title" style="color: #000;">
                                    <i class="bi bi-bullseye me-2" style="color: #000 !important;"></i>Motivasi
                                </h5>
                            </div>

                            <div class="mb-4">
                                <label for="motivation" class="form-label fw-semibold">Motivasi Masuk Sekolah</label>
                                <textarea class="form-control @error('motivation') is-invalid @enderror" id="motivation"
                                    name="motivation" rows="4"
                                    placeholder="Ceritakan alasan dan motivasi Anda ingin bergabung di sekolah ini...">{{ old('motivation') }}</textarea>
                                @error('motivation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Persetujuan -->
                            <div class="alert alert-warning border-0 text-black" role="alert">
                                <div class="form-check">
                                    <input class="form-check-input @error('agreement') is-invalid @enderror" type="checkbox"
                                        id="agreement" name="agreement" value="1" required>
                                    <label class="form-check-label text-black" for="agreement">
                                        Saya menyetujui <a href="#" class="text-black text-decoration-underline fw-semibold"
                                            data-bs-toggle="modal" data-bs-target="#termsModal">syarat dan ketentuan</a>
                                        yang berlaku <span class="text-danger">*</span>
                                    </label>
                                    @error('agreement')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                <a href="{{ route('home') }}" class="btn btn-outline-secondary" style="color: #000;">
                                    <i class="bi bi-arrow-left me-1" style="color: #000;"></i>Batal
                                </a>
                                <button type="submit" class="btn btn-warning fw-semibold" id="submitBtn"
                                    style="color: #000;">
                                    <i class="bi bi-send-fill me-1" style="color: #000;"></i>Kirim Pendaftaran
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Syarat dan Ketentuan -->
    <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title fw-bold" id="termsModalLabel" style="color: #000;">
                        <i class="bi bi-file-text me-2" style="color: #000;"></i>Syarat dan Ketentuan Pendaftaran
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6 class="fw-bold">Ketentuan Pendaftaran Siswa Baru</h6>
                    <ul>
                        <li>Data yang diisi harus sesuai dengan dokumen asli yang dimiliki</li>
                        <li>Dokumen yang diunggah harus jelas dan dapat dibaca</li>
                        <li>Pendaftar wajib mengikuti seluruh tahapan seleksi yang ditentukan sekolah</li>
                        <li>Keputusan penerimaan siswa sepenuhnya berada di tangan pihak sekolah</li>
                        <li>Biaya pendaftaran yang sudah dibayar tidak dapat dikembalikan</li>
                    </ul>

                    <h6 class="fw-bold mt-4">Hak dan Kewajiban</h6>
                    <ul>
                        <li>Pendaftar berhak mendapat informasi status pendaftaran</li>
                        <li>Pendaftar wajib menjaga kerahasiaan akun yang dibuat</li>
                        <li>Pendaftar wajib melengkapi dokumen yang diminta dalam waktu yang ditentukan</li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning fw-semibold" data-bs-dismiss="modal" style="color: #000;">
                        <i class="bi bi-x-circle me-1" style="color: #000;"></i>Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .required:after {
            content: " *";
            color: #dc3545;
        }

        .section-divider {
            border-bottom: 2px solid #ffc107;
            padding-bottom: 0.5rem;
        }

        .section-title {
            color: #333;
            font-weight: 600;
            margin: 0;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #ffc107;
            box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.25);
        }

        .form-check-input:checked {
            background-color: #ffc107;
            border-color: #ffc107;
        }

        .form-check-input:focus {
            border-color: #ffc107;
            box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.25);
        }

        .card {
            border-radius: 0.5rem;
            overflow: hidden;
        }

        .card-header {
            padding: 1.25rem 1.5rem;
        }

        .btn-warning {
            background-color: #ffc107;
            border-color: #ffc107;
        }

        .btn-warning:hover {
            background-color: #e0a800;
            border-color: #d39e00;
        }

        .btn-outline-secondary {
            color: #000;
            border-color: #6c757d;
        }

        .btn-outline-secondary:hover {
            background-color: #6c757d;
            border-color: #6c757d;
            color: #fff;
        }

        .btn-outline-secondary:hover i {
            color: #fff !important;
        }

        small.text-muted {
            font-size: 0.875rem;
        }

        .invalid-feedback {
            display: block;
        }

        .alert {
            border-radius: 0.375rem;
        }

        @media (max-width: 768px) {
            .container {
                padding-left: 15px;
                padding-right: 15px;
            }

            .card-body {
                padding: 1.5rem !important;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Form validation
            const form = document.getElementById('registrationForm');
            const submitBtn = document.getElementById('submitBtn');

            form.addEventListener('submit', function (e) {
                // Validate required fields client-side and focus on the first empty one
                const requiredFields = Array.from(form.querySelectorAll('[required]'));
                let firstInvalid = null;
                for (const field of requiredFields) {
                    // Skip fields that are hidden or disabled
                    if (field.disabled || field.type === 'hidden' || field.closest('.d-none')) continue;
                    if (field.tagName.toLowerCase() === 'select') {
                        if (!field.value) { firstInvalid = field; break; }
                    } else if (field.type === 'checkbox') {
                        if (!field.checked) { firstInvalid = field; break; }
                    } else if (field.type === 'radio') {
                        // Radio groups validation handled server-side or can be enhanced
                        continue;
                    } else if (field.type === 'file') {
                        if (!field.files || field.files.length === 0) { firstInvalid = field; break; }
                    } else if (!String(field.value || '').trim()) { firstInvalid = field; break; }
                }

                const alertContainer = document.getElementById('formAlertContainer');
                alertContainer.innerHTML = '';
                if (firstInvalid) {
                    e.preventDefault();
                    // Determine label text for user-friendly message
                    let labelText = firstInvalid.getAttribute('id') || 'field';
                    const label = form.querySelector('label[for="' + firstInvalid.id + '"]');
                    if (label) {
                        labelText = label.innerText.replace('*', '').trim();
                    }

                    alertContainer.innerHTML = `<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                        <strong>Silakan lengkapi:</strong> ${labelText}.
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>`;

                    // Add invalid class and focus
                    firstInvalid.classList.add('is-invalid');
                    firstInvalid.focus();
                    // Smooth scroll to the element
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    return;
                }

                // No client-side issues, proceed to submit
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Mengirim...';
            });

            // File size validation
            const fileInputs = document.querySelectorAll('input[type="file"]');
            fileInputs.forEach(input => {
                input.addEventListener('change', function () {
                    const file = this.files[0];
                    if (file && file.size > 2 * 1024 * 1024) { // 2MB
                        alert('Ukuran file maksimal 2MB');
                        this.value = '';
                    }
                });
            });

            // Phone number formatting
            const phoneInputs = document.querySelectorAll('input[type="tel"]');
            phoneInputs.forEach(input => {
                input.addEventListener('input', function () {
                    this.value = this.value.replace(/[^0-9+\-\s]/g, '');
                });
            });

            // NISN validation (numbers only)
            const nisnInput = document.getElementById('nisn');
            if (nisnInput) {
                nisnInput.addEventListener('input', function () {
                    this.value = this.value.replace(/[^0-9]/g, '');
                });
            }

            // Clear validation alert and invalid state when user updates a required field
            const allRequired = Array.from(form.querySelectorAll('[required]'));
            allRequired.forEach(field => {
                ['input', 'change'].forEach(evt => {
                    field.addEventListener(evt, function () {
                        if (this.classList.contains('is-invalid') && String(this.value || '').trim()) {
                            this.classList.remove('is-invalid');
                        }
                        const alertContainer = document.getElementById('formAlertContainer');
                        if (alertContainer) {
                            alertContainer.innerHTML = '';
                        }
                    });
                });
            });
        });
    </script>
@endpush