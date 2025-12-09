@extends('layouts.app')

@section('title', 'Unggah Materi')

@section('content')
    <div class="container py-4">
        <h3>Unggah Materi</h3>
        <div class="card mt-3 p-4">
            <form action="{{ route('teacher.materials.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Judul</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                        @error('title')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Kelas</label>
                        <select name="class_id" class="form-select" required>
                            @if(isset($classes) && (is_array($classes) || $classes instanceof \Illuminate\Support\Collection) && count($classes) > 0)
                                @foreach($classes as $c)
                                    <option value="{{ $c->id }}" {{ old('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}
                                    </option>
                                @endforeach
                            @else
                                <option value="" disabled>Tidak ada kelas yang diampu</option>
                                <script>
                                    document.addEventListener('DOMContentLoaded', function () {
                                        const btn = document.querySelector('button[type=submit]');
                                        if (btn) btn.disabled = true;
                                    });
                                </script>
                            @endif
                        </select>
                        @error('class_id')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Mata Pelajaran</label>
                        <select name="subject_id" class="form-select" required>
                            <option value="">-- Pilih Mata Pelajaran --</option>
                            @if(isset($subjects) && (is_array($subjects) || $subjects instanceof \Illuminate\Support\Collection) && count($subjects) > 0)
                                @foreach($subjects as $s)
                                    <option value="{{ $s->id }}" {{ old('subject_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}
                                    </option>
                                @endforeach
                            @else
                                <option value="" disabled>Tidak ada mata pelajaran yang diajarkan</option>
                                <script>
                                    document.addEventListener('DOMContentLoaded', function () {
                                        const btn = document.querySelector('button[type=submit]');
                                        if (btn) btn.disabled = true;
                                    });
                                </script>
                            @endif
                        </select>
                        @error('subject_id')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                        @error('description')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">File (pdf/doc/video/image) — Maks 20MB</label>
                        <input type="file" name="file" class="form-control" required>
                        @error('file')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tampilkan</label>
                        <select name="is_visible" class="form-select">
                            <option value="1" {{ old('is_visible', 1) == 1 ? 'selected' : '' }}>Ya</option>
                            <option value="0" {{ old('is_visible') == 0 ? 'selected' : '' }}>Tidak</option>
                        </select>
                    </div>
                </div>
                <div class="mt-4 text-end">
                    <a href="{{ route('teacher.materials.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function hasValidOption(select) {
                if (!select) return false;
                return Array.from(select.options).some(opt => opt.value && !opt.disabled);
            }
            var classSel = document.querySelector('select[name=class_id]');
            var subjectSel = document.querySelector('select[name=subject_id]');
            var submitBtn = document.querySelector('button[type=submit]');
            if (submitBtn && (!hasValidOption(classSel) || !hasValidOption(subjectSel))) {
                submitBtn.disabled = true;
            }
        });
    </script>
@endsection
