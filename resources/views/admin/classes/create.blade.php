@extends('layouts.admin')

@section('title', 'Buat Kelas')

@section('admin-content')
    <div class="container py-4">
        <h3>Buat Kelas Baru</h3>
        <div class="card mt-3 p-4">
            <form action="{{ route('admin.classes.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama Kelas</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                        @error('name') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tingkat / Semester</label>
                        <div class="input-group">
                            <select id="grade-level-select" class="form-select me-2" aria-label="Tingkat Preset">
                                <option value="">-- Pilih Tingkat --</option>
                                <option value="SD">SD</option>
                                <option value="SMP">SMP</option>
                                <option value="SMA">SMA</option>
                                <option value="kejuruan">kejuruan</option>
                                <option value="other">Lainnya...</option>
                            </select>
                            <input type="text" id="grade_level_input" name="grade_level" class="form-control"
                                value="{{ old('grade_level') }}" placeholder="Contoh: X, XI, Kejuruan, etc.">
                        </div>
                        @error('grade_level') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Kapasitas</label>
                        <input type="number" name="capacity" class="form-control" value="{{ old('capacity') ?? 0 }}">
                        @error('capacity') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Wali Kelas</label>
                        <select name="homeroom_teacher_id" class="form-select">
                            <option value="">-- Pilih Wali Kelas (opsional) --</option>
                            @foreach($teachers as $t)
                                <option value="{{ $t->id }}" {{ old('homeroom_teacher_id') == $t->id ? 'selected' : '' }}>
                                    {{ $t->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('homeroom_teacher_id') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                        @error('description') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12">
                        <div class="form-check mt-2">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Aktif</label>
                        </div>
                    </div>
                </div>
                <div class="mt-4 text-end">
                    <a href="{{ route('admin.classes.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        // Sync preset select with the grade level input
        (function () {
            var sel = document.getElementById('grade-level-select');
            var input = document.getElementById('grade_level_input');
            sel && sel.addEventListener('change', function (e) {
                var v = e.target.value;
                if (v === 'other' || v === '') {
                    // allow custom input
                    input.focus();
                    return;
                }
                input.value = v;
            });
        })();
    </script>
@endsection
