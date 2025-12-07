@extends('layouts.admin')

@section('title', 'Buat Jadwal')

@section('admin-content')
    <div class="container py-4">
        <h3>Buat Jadwal</h3>
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
            <form action="{{ route('admin.schedules.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Kelas</label>
                        <select name="class_id" class="form-select" required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}" {{ old('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('class_id') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Mata Pelajaran</label>
                        <select name="subject_id" class="form-select" required>
                            <option value="">-- Pilih Mata Pelajaran --</option>
                            @foreach($subjects as $s)
                                <option value="{{ $s->id }}" {{ old('subject_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('subject_id') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Guru</label>
                        <select name="teacher_id" class="form-select" required>
                            <option value="">-- Pilih Guru --</option>
                            @foreach($teachers as $t)
                                <option value="{{ $t->id }}" {{ old('teacher_id') == $t->id ? 'selected' : '' }}>{{ $t->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('teacher_id') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Hari</label>
                        <select name="day_of_week" class="form-select" required>
                            <option value="monday" {{ old('day_of_week') == 'monday' ? 'selected' : '' }}>Monday</option>
                            <option value="tuesday" {{ old('day_of_week') == 'tuesday' ? 'selected' : '' }}>Tuesday</option>
                            <option value="wednesday" {{ old('day_of_week') == 'wednesday' ? 'selected' : '' }}>Wednesday
                            </option>
                            <option value="thursday" {{ old('day_of_week') == 'thursday' ? 'selected' : '' }}>Thursday
                            </option>
                            <option value="friday" {{ old('day_of_week') == 'friday' ? 'selected' : '' }}>Friday</option>
                            <option value="saturday" {{ old('day_of_week') == 'saturday' ? 'selected' : '' }}>Saturday
                            </option>
                            <option value="sunday" {{ old('day_of_week') == 'sunday' ? 'selected' : '' }}>Sunday</option>
                        </select>
                        @error('day_of_week') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Mulai</label>
                        <input type="time" name="start_time" class="form-control" value="{{ old('start_time') }}" required>
                        @error('start_time') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Selesai</label>
                        <input type="time" name="end_time" class="form-control" value="{{ old('end_time') }}" required>
                        @error('end_time') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Ruang</label>
                        <input type="text" name="room" class="form-control" value="{{ old('room') }}">
                        @error('room') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Aktif</label>
                        </div>
                    </div>
                </div>
                <div class="mt-4 text-end">
                    <a href="{{ route('admin.schedules.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection