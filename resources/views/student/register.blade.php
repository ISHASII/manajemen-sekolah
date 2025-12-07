@extends('layouts.app')

@section('title', 'Pendaftaran Siswa Baru')

@section('content')
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title mb-0">
                            <i class="bi bi-person-plus me-2"></i>Pendaftaran Siswa Baru
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info" role="alert">
                            <i class="bi bi-info-circle me-2"></i>
                            Lengkapi form berikut untuk mendaftar sebagai siswa baru di {{ $school->name }}.
                        </div>

                        <form action="{{ route('student.register') }}" method="POST" enctype="multipart/form-data"
                            id="registrationForm">
                            @csrf

                            <!-- Data Siswa -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="full_name" class="form-label required">Nama Lengkap *</label>
                                    <input type="text" class="form-control @error('full_name') is-invalid @enderror"
                                        id="full_name" name="full_name" value="{{ old('full_name') }}" required>
                                    @error('full_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="nis" class="form-label required">NIS (Nomor Induk Siswa) *</label>
                                    <input type="text" class="form-control @error('nis') is-invalid @enderror" id="nis"
                                        name="nis" value="{{ old('nis') }}" required>
                                    @error('nis')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label required">Email *</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                        name="email" value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Nomor Telepon</label>
                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone"
                                        name="phone" value="{{ old('phone') }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="birth_date" class="form-label required">Tanggal Lahir *</label>
                                    <input type="date" class="form-control @error('birth_date') is-invalid @enderror"
                                        id="birth_date" name="birth_date" value="{{ old('birth_date') }}" required>
                                    @error('birth_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="birth_place" class="form-label required">Tempat Lahir *</label>
                                    <input type="text" class="form-control @error('birth_place') is-invalid @enderror"
                                        id="birth_place" name="birth_place" value="{{ old('birth_place') }}" required>
                                    @error('birth_place')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="gender" class="form-label required">Jenis Kelamin *</label>
                                    <select class="form-select @error('gender') is-invalid @enderror" id="gender"
                                        name="gender" required>
                                        <option value="">Pilih Jenis Kelamin</option>
                                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Laki-laki
                                        </option>
                                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Perempuan
                                        </option>
                                    </select>
                                    @error('gender')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="class_id" class="form-label required">Kelas yang Dituju *</label>
                                    <select class="form-select @error('class_id') is-invalid @enderror" id="class_id"
                                        name="class_id" required>
                                        <option value="">Pilih Kelas</option>
                                        @foreach($classes as $class)
                                            <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                                {{ $class->name }} - {{ $class->level }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('class_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="address" class="form-label required">Alamat Lengkap *</label>
                                <textarea class="form-control @error('address') is-invalid @enderror" id="address"
                                    name="address" rows="3" required>{{ old('address') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Data Orang Tua -->
                            <div class="card border-secondary mt-4">
                                <div class="card-header bg-secondary text-white">
                                    <h5 class="mb-0">Data Orang Tua/Wali</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="parent_name" class="form-label required">Nama Orang Tua/Wali
                                                *</label>
                                            <input type="text"
                                                class="form-control @error('parent_name') is-invalid @enderror"
                                                id="parent_name" name="parent_name" value="{{ old('parent_name') }}"
                                                required>
                                            @error('parent_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="parent_phone" class="form-label required">Nomor Telepon Orang
                                                Tua/Wali *</label>
                                            <input type="tel"
                                                class="form-control @error('parent_phone') is-invalid @enderror"
                                                id="parent_phone" name="parent_phone" value="{{ old('parent_phone') }}"
                                                required>
                                            @error('parent_phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="parent_job" class="form-label">Pekerjaan Orang Tua/Wali</label>
                                        <input type="text" class="form-control @error('parent_job') is-invalid @enderror"
                                            id="parent_job" name="parent_job" value="{{ old('parent_job') }}">
                                        @error('parent_job')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Upload Dokumen -->
                            <div class="card border-info mt-4">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0">Dokumen Pendukung</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="birth_certificate" class="form-label">Akta Kelahiran
                                            (PDF/JPG/PNG)</label>
                                        <input type="file"
                                            class="form-control @error('birth_certificate') is-invalid @enderror"
                                            id="birth_certificate" name="birth_certificate" accept=".pdf,.jpg,.jpeg,.png">
                                        @error('birth_certificate')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="last_certificate" class="form-label">Ijazah/Surat Keterangan Lulus
                                            Terakhir (PDF/JPG/PNG)</label>
                                        <input type="file"
                                            class="form-control @error('last_certificate') is-invalid @enderror"
                                            id="last_certificate" name="last_certificate" accept=".pdf,.jpg,.jpeg,.png">
                                        @error('last_certificate')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="photo" class="form-label">Pas Foto 3x4 (JPG/PNG)</label>
                                        <input type="file" class="form-control @error('photo') is-invalid @enderror"
                                            id="photo" name="photo" accept=".jpg,.jpeg,.png">
                                        @error('photo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Motivasi -->
                            <div class="mb-4 mt-4">
                                <label for="motivation" class="form-label">Motivasi Masuk Sekolah</label>
                                <textarea class="form-control @error('motivation') is-invalid @enderror" id="motivation"
                                    name="motivation" rows="4"
                                    placeholder="Ceritakan alasan dan motivasi Anda ingin bergabung di sekolah ini...">{{ old('motivation') }}</textarea>
                                @error('motivation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Persetujuan -->
                            <div class="form-check mb-4">
                                <input class="form-check-input @error('agreement') is-invalid @enderror" type="checkbox"
                                    id="agreement" name="agreement" value="1" required>
                                <label class="form-check-label" for="agreement">
                                    Saya menyetujui <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">syarat
                                        dan ketentuan</a> yang berlaku *
                                </label>
                                @error('agreement')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="{{ route('home') }}" class="btn btn-secondary me-md-2">
                                    <i class="bi bi-arrow-left me-1"></i>Batal
                                </a>
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="bi bi-send me-1"></i>Kirim Pendaftaran
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
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="termsModalLabel">Syarat dan Ketentuan Pendaftaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6>Ketentuan Pendaftaran Siswa Baru</h6>
                    <ul>
                        <li>Data yang diisi harus sesuai dengan dokumen asli yang dimiliki</li>
                        <li>Dokumen yang diunggah harus jelas dan dapat dibaca</li>
                        <li>Pendaftar wajib mengikuti seluruh tahapan seleksi yang ditentukan sekolah</li>
                        <li>Keputusan penerimaan siswa sepenuhnya berada di tangan pihak sekolah</li>
                        <li>Biaya pendaftaran yang sudah dibayar tidak dapat dikembalikan</li>
                    </ul>

                    <h6>Hak dan Kewajiban</h6>
                    <ul>
                        <li>Pendaftar berhak mendapat informasi status pendaftaran</li>
                        <li>Pendaftar wajib menjaga kerahasiaan akun yang dibuat</li>
                        <li>Pendaftar wajib melengkapi dokumen yang diminta dalam waktu yang ditentukan</li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .required:after {
            content: " *";
            color: red;
        }

        #registrationForm {
            position: relative;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }

        .invalid-feedback {
            display: block;
        }

        .card-header {
            border-bottom: 2px solid rgba(0, 0, 0, 0.125);
        }

        .btn:focus {
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.5);
        }

        @media (max-width: 768px) {
            .container {
                padding-left: 10px;
                padding-right: 10px;
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

            // NIS validation (numbers only)
            document.getElementById('nis').addEventListener('input', function () {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        });
    </script>
@endpush
