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
            <form action="{{ route('admin.students.store') }}" method="POST">
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
                <div class="mt-4 text-end">
                    <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection