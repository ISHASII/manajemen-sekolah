@extends('layouts.app')

@section('title', 'Edit Siswa')

@section('content')
    <div class="container py-4">
        <h3>Edit Siswa</h3>
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
            <form action="{{ route('admin.students.update', $student->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $student->user->name) }}"
                            required>
                        @error('name') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control"
                            value="{{ old('email', $student->user->email) }}" required>
                        @error('email') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Student ID</label>
                        <input type="text" name="student_id" class="form-control"
                            value="{{ old('student_id', $student->student_id) }}" required>
                        @error('student_id') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">NISN</label>
                        <input type="text" name="nisn" class="form-control" value="{{ old('nisn', $student->nisn) }}">
                        @error('nisn') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Kelas</label>
                        <select name="class_id" class="form-select">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}" {{ old('class_id', $student->class_id) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>
                        @error('class_id') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tempat Lahir</label>
                        <input type="text" name="place_of_birth" class="form-control"
                            value="{{ old('place_of_birth', $student->place_of_birth) }}" required>
                        @error('place_of_birth') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Lahir</label>
                        <input type="date" name="birth_date" class="form-control"
                            value="{{ old('birth_date', $student->birth_date?->toDateString()) }}" required>
                        @error('birth_date') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Agama</label>
                        <select name="religion" class="form-select" required>
                            <option value="">-- Pilih --</option>
                            <option value="islam" {{ old('religion', $student->religion) == 'islam' ? 'selected' : '' }}>Islam
                            </option>
                            <option value="kristen" {{ old('religion', $student->religion) == 'kristen' ? 'selected' : '' }}>
                                Kristen</option>
                            <option value="katolik" {{ old('religion', $student->religion) == 'katolik' ? 'selected' : '' }}>
                                Katolik</option>
                            <option value="hindu" {{ old('religion', $student->religion) == 'hindu' ? 'selected' : '' }}>Hindu
                            </option>
                            <option value="budha" {{ old('religion', $student->religion) == 'budha' ? 'selected' : '' }}>Budha
                            </option>
                            <option value="khonghucu" {{ old('religion', $student->religion) == 'khonghucu' ? 'selected' : '' }}>Khonghucu</option>
                        </select>
                        @error('religion') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Alamat</label>
                        <input type="text" name="address" class="form-control"
                            value="{{ old('address', $student->address) }}" required>
                        @error('address') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nama Orang Tua</label>
                        <input type="text" name="parent_name" class="form-control"
                            value="{{ old('parent_name', $student->parent_name) }}" required>
                        @error('parent_name') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Telepon Orang Tua</label>
                        <input type="text" name="parent_phone" class="form-control"
                            value="{{ old('parent_phone', $student->parent_phone) }}" required>
                        @error('parent_phone') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Alamat Orang Tua</label>
                        <input type="text" name="parent_address" class="form-control"
                            value="{{ old('parent_address', $student->parent_address) }}" required>
                        @error('parent_address') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Masuk</label>
                        <input type="date" name="enrollment_date" class="form-control"
                            value="{{ old('enrollment_date', $student->enrollment_date?->toDateString()) }}" required>
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