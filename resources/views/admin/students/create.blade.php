@extends('layouts.admin')

@section('title', 'Tambah Siswa')

@section('admin-content')
    <div class="container py-4">
        <h3>Tambah Siswa</h3>
        <div class="card mt-3 p-4">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if($errors && $errors->any())
                <div class="alert alert-warning">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('admin.students.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    @if(isset($prefillUser) && $prefillUser)
                        <div class="col-md-6">
                            <label class="form-label">Nama (User Exist)</label>
                            <input type="text" class="form-control" value="{{ $prefillUser->name }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email (User Exist)</label>
                            <input type="email" class="form-control" value="{{ $prefillUser->email }}" readonly>
                        </div>
                        <input type="hidden" name="user_id" value="{{ $prefillUser->id }}">
                    @else
                        <div class="col-md-6">
                            <label class="form-label">Nama</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                            @error('name') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                            @error('email') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>
                    @endif
                    <div class="col-md-6">
                        <label class="form-label">Student ID</label>
                        <input type="text" name="student_id" class="form-control" value="{{ old('student_id') }}" required>
                        @error('student_id') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">NISN</label>
                        <input type="text" name="nisn" class="form-control" value="{{ old('nisn') }}">
                        @error('nisn') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Kelas</label>
                        <select name="class_id" class="form-select">
                            @php $classesList = $classes ?? collect(); @endphp
                            @if($classesList->count() > 0)
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($classesList as $c)
                                    <option value="{{ $c->id }}" {{ old('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}
                                    </option>
                                @endforeach
                            @else
                                <option value="" disabled>Tidak ada kelas tersedia</option>
                            @endif
                        </select>
                        @error('class_id') <div class="text-danger small">{{ $message }}</div> @enderror
                        @if($classesList->count() == 0)
                            <div class="form-text text-muted d-flex align-items-center justify-content-between">
                                <div>Belum ada kelas, Anda dapat menambahkan siswa tanpa memilih kelas.</div>
                                <div><a href="{{ route('admin.classes.create') }}" class="btn btn-sm btn-primary">Buat Kelas</a>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tempat Lahir</label>
                        <input type="text" name="place_of_birth" class="form-control" value="{{ old('place_of_birth') }}"
                            required>
                        @error('place_of_birth') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Lahir</label>
                        <input type="date" name="birth_date" class="form-control" value="{{ old('birth_date') }}" required>
                        @error('birth_date') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Agama</label>
                        <select name="religion" class="form-select" required>
                            <option value="">-- Pilih --</option>
                            <option value="islam">Islam</option>
                            <option value="kristen">Kristen</option>
                            <option value="katolik">Katolik</option>
                            <option value="hindu">Hindu</option>
                            <option value="budha">Budha</option>
                            <option value="khonghucu">Khonghucu</option>
                        </select>
                        @error('religion') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Alamat</label>
                        <input type="text" name="address" class="form-control" value="{{ old('address') }}" required>
                        @error('address') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nama Orang Tua</label>
                        <input type="text" name="parent_name" class="form-control" value="{{ old('parent_name') }}"
                            required>
                        @error('parent_name') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Telepon Orang Tua</label>
                        <input type="text" name="parent_phone" class="form-control" value="{{ old('parent_phone') }}"
                            required>
                        @error('parent_phone') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Alamat Orang Tua</label>
                        <input type="text" name="parent_address" class="form-control" value="{{ old('parent_address') }}"
                            required>
                        @error('parent_address') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Masuk</label>
                        <input type="date" name="enrollment_date" class="form-control"
                            value="{{ old('enrollment_date', now()->toDateString()) }}" required>
                        @error('enrollment_date') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                </div>
                <hr class="my-3" />
                <div class="row g-3">
                    <div class="col-md-12">
                        <h5>Dokumen Siswa</h5>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Akta Kelahiran (PDF/JPG/PNG)</label>
                        <input type="file" name="birth_certificate" class="form-control">
                        @error('birth_certificate') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Kartu Keluarga (KK) (PDF/JPG/PNG)</label>
                        <input type="file" name="kk" class="form-control">
                        @error('kk') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Ijazah/Surat Keterangan Lulus Terakhir (PDF/JPG/PNG)</label>
                        <input type="file" name="last_certificate" class="form-control">
                        @error('last_certificate') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Pas Foto 3x4 (JPG/PNG)</label>
                        <input type="file" name="photo" class="form-control">
                        @error('photo') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Sertifikat Medis (PDF/JPG/PNG)</label>
                        <input type="file" name="medical_certificate" class="form-control">
                        @error('medical_certificate') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nomor Telepon Siswa</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                        @error('phone') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Jenis Kelamin</label>
                        <select name="gender" class="form-select">
                            <option value="">-- Pilih --</option>
                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('gender') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email Orang Tua</label>
                        <input type="email" name="parent_email" class="form-control" value="{{ old('parent_email') }}">
                        @error('parent_email') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Minat Kerja</label>
                        <input type="text" name="job_interest" class="form-control" value="{{ old('job_interest') }}">
                        @error('job_interest') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Pekerjaan Orang Tua</label>
                        <input type="text" name="parent_job" class="form-control" value="{{ old('parent_job') }}">
                        @error('parent_job') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Informasi Medis</label>
                        <textarea name="medical_info" rows="3" class="form-control">{{ old('medical_info') }}</textarea>
                        @error('medical_info') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Kondisi Kesehatan</label>
                        @php
                            $healthOpts = ['Alergi','Asma','Penyakit Jantung','Epilepsi','Lainnya'];
                            $selectedHealth = old('health_info', []);
                        @endphp
                        <div class="row">
                            @foreach($healthOpts as $k => $label)
                                <div class="col-md-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="health_info[]" value="{{ $label }}" id="health_{{ $k }}"
                                            {{ in_array($label, (array) $selectedHealth) ? 'checked' : '' }}>
                                <div class="col-md-12 mt-2">
                                    <label class="form-label">CV / LinkedIn (URL)</label>
                                    <input type="text" name="cv_link" class="form-control" value="{{ old('cv_link') }}">
                                </div>
                                <div class="col-md-12 mt-2">
                                    <label class="form-label">Portofolio (link, pisah koma)</label>
                                    <input type="text" name="portfolio_links" class="form-control" value="{{ old('portfolio_links') }}">
                                </div>
                                        <label class="form-check-label" for="health_{{ $k }}">{{ $label }}</label>
                                    </div>
                                </div>
                            @endforeach
                            <div class="col-md-4 mt-2">
                                <input type="text" name="health_info_other" class="form-control mt-2" placeholder="Jika 'Lainnya', sebutkan..." value="{{ old('health_info_other') }}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Disabilitas</label>
                        @php
                            $disabilityOpts = ['Gangguan Penglihatan','Gangguan Pendengaran','Disabilitas Fisik','Disabilitas Intelektual','Lainnya'];
                            $selectedDis = old('disability_info', []);
                        @endphp
                        <div class="row">
                            @foreach($disabilityOpts as $k => $label)
                                <div class="col-md-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="disability_info[]" value="{{ $label }}" id="disability_{{ $k }}"
                                            {{ in_array($label, (array) $selectedDis) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="disability_{{ $k }}">{{ $label }}</label>
                                    </div>
                                </div>
                            @endforeach
                            <div class="col-md-4 mt-2">
                                <input type="text" name="disability_info_other" class="form-control mt-2" placeholder="Jika 'Lainnya', sebutkan..." value="{{ old('disability_info_other') }}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Sekolah Sebelumnya</label>
                        <input type="text" name="education_history[previous_school]" class="form-control" value="{{ old('education_history.previous_school') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tahun Lulus</label>
                        <input type="number" name="education_history[graduation_year]" class="form-control" value="{{ old('education_history.graduation_year') }}">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Status Anak (Yatim/Piatu)</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="orphan_status" id="orphan_none" value="none" {{ old('orphan_status', 'none') == 'none' ? 'checked' : '' }}>
                            <label class="form-check-label" for="orphan_none">Tidak Kedua Orang Tua</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="orphan_status" id="orphan_yatim" value="yatim" {{ old('orphan_status') == 'yatim' ? 'checked' : '' }}>
                            <label class="form-check-label" for="orphan_yatim">Yatim (Ayah Meninggal)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="orphan_status" id="orphan_piatu" value="piatu" {{ old('orphan_status') == 'piatu' ? 'checked' : '' }}>
                            <label class="form-check-label" for="orphan_piatu">Piatu (Ibu Meninggal)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="orphan_status" id="orphan_both" value="yatim_piatu" {{ old('orphan_status') == 'yatim_piatu' ? 'checked' : '' }}>
                            <label class="form-check-label" for="orphan_both">Yatim & Piatu (Kedua Orang Tua Meninggal)</label>
                        </div>
                    </div>
                </div>
                <div class="mt-4 text-end">
                    <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

