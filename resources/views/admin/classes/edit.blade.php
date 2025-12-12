@extends('layouts.admin')

@section('title', 'Edit Kelas')

@section('admin-content')
    <div class="container py-4">
        <h3>Edit Kelas</h3>
        <div class="card mt-3 p-4">
            <form action="{{ route('admin.classes.update', $class->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama Kelas</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $class->name) }}"
                            required>
                        @error('name') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tingkat / Semester</label>
                        <div class="input-group">
                            <select id="grade-level-select" class="form-select me-2" aria-label="Tingkat Preset">
                                <option value="">-- Pilih Tingkat --</option>
                                <option value="SD" {{ (old('grade_level', $class->grade_level) === 'SD') ? 'selected' : '' }}>
                                    SD</option>
                                <option value="SMP" {{ (old('grade_level', $class->grade_level) === 'SMP') ? 'selected' : '' }}>SMP</option>
                                <option value="SMA" {{ (old('grade_level', $class->grade_level) === 'SMA') ? 'selected' : '' }}>SMA</option>
                                <option value="other" {{ in_array(old('grade_level', $class->grade_level), ['SD', 'SMP', 'SMA']) ? '' : 'selected' }}>Lainnya...</option>
                            </select>
                            <input type="text" id="grade_level_input" name="grade_level" class="form-control"
                                value="{{ old('grade_level', $class->grade_level) }}" placeholder="Contoh: X, XI, dll.">
                        </div>
                        @error('grade_level') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Kapasitas</label>
                        <input type="number" name="capacity" class="form-control"
                            value="{{ old('capacity', $class->capacity) ?? 0 }}">
                        @error('capacity') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Wali Kelas</label>
                        <select name="homeroom_teacher_id" class="form-select">
                            <option value="">-- Pilih Wali Kelas (opsional) --</option>
                            @foreach($teachers as $t)
                                <option value="{{ $t->id }}" {{ (old('homeroom_teacher_id', $class->homeroom_teacher_id) == $t->id) ? 'selected' : '' }}>{{ $t->name }}</option>
                            @endforeach
                        </select>
                        @error('homeroom_teacher_id') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" class="form-control"
                            rows="3">{{ old('description', $class->description) }}</textarea>
                        @error('description') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="row g-3 mt-3">
                    <div class="col-12">
                        <div class="form-check">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $class->is_active) ? 'checked' : '' }}>
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
                    input.focus();
                    return;
                }
                input.value = v;
            });
        })();

        // Prevent updating grade to 'kejuruan' from this form for non-kejuruan classes
        (function () {
            var form = document.querySelector('form[action="{{ route('admin.classes.update', $class->id) }}"]');
            var input = document.getElementById('grade_level_input');
            form && form.addEventListener('submit', function (e) {
                if (!input) return;
                var v = (input.value || '').trim().toLowerCase();
                var currentIsKejuruan = '{{ $class->grade_level }}' === 'kejuruan';
                if (v === 'kejuruan' && !currentIsKejuruan) {
                    e.preventDefault();
                    alert('Mengubah kelas menjadi kejuruan harus dilakukan melalui menu Kelola Pelatihan.');
                    input.focus();
                }
            });
        })();
    </script>
@endsection