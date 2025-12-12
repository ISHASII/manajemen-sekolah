@extends('layouts.app')

@section('title', 'Edit Materi')

@section('content')
    <div class="container py-4">
        <h3>Edit Materi</h3>
        <div class="card mt-3 p-4">
            <form action="{{ route('teacher.materials.update', $material->id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Judul</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title', $material->title) }}"
                            required>
                        @error('title')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    @if(isset($classes) && $classes instanceof \Illuminate\Support\Collection && $classes->count() > 0)
                        <div class="col-md-6">
                            <label class="form-label">Kelas</label>
                            <select name="class_id" class="form-select">
                                <option value="" disabled>-- Pilih Kelas --</option>
                                @foreach($classes as $c)
                                    <option value="{{ $c->id }}" {{ old('class_id', $material->class_id) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                @endforeach
                                @if($material->classRoom && !(isset($classes) && ($classes instanceof \Illuminate\Support\Collection ? $classes->contains('id', $material->classRoom->id) : in_array($material->classRoom->id, (array) $classes))))
                                    <option value="{{ $material->classRoom->id }}" selected>{{ $material->classRoom->name }} (Tidak
                                        lagi diampu)</option>
                                @endif
                            </select>
                        </div>
                    @endif
                    <div class="col-md-6">
                        <label class="form-label">Kelas Pelatihan (Opsional)</label>
                        @if(isset($trainingClasses) && $trainingClasses->count() > 0)
                            <select name="training_class_id" class="form-select">
                                <option value="">-- Pilih Kelas Pelatihan --</option>
                                @foreach($trainingClasses as $tc)
                                    <option value="{{ $tc->id }}" {{ (string) (old('training_class_id', $material->training_class_id)) === (string) $tc->id ? 'selected' : '' }}>{{ $tc->title }}
                                        ({{ $tc->students->count() }}/{{ $tc->capacity ?? '-' }})</option>
                                @endforeach
                            </select>
                        @endif
                        <div class="form-text small">Jika materi ditujukan untuk kelas pelatihan, pilih kelas pelatihan ini.
                            Kosongkan Kelas biasa agar tidak menggandakan target.</div>
                        @error('training_class_id')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    @if(!request()->query('training_class_id') && empty($material->training_class_id))
                        <div class="col-md-6">
                            <label class="form-label">Mata Pelajaran</label>
                            <select name="subject_id" class="form-select" required>
                                <option value="" disabled>-- Pilih Mata Pelajaran --</option>
                                @if(isset($subjects) && (is_array($subjects) || $subjects instanceof \Illuminate\Support\Collection) && count($subjects) > 0)
                                    @foreach($subjects as $s)
                                        <option value="{{ $s->id }}" {{ old('subject_id', $material->subject_id) == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                                    @endforeach
                                @else
                                    <option value="" disabled>Tidak ada mata pelajaran yang diajarkan</option>
                                @endif
                                @if($material->subject && !(isset($subjects) && ($subjects instanceof \Illuminate\Support\Collection ? $subjects->contains('id', $material->subject->id) : in_array($material->subject->id, (array) $subjects))))
                                    <option value="{{ $material->subject->id }}" selected>{{ $material->subject->name }} (Tidak
                                        diajarkan saat ini)</option>
                                @endif
                            </select>
                        </div>
                    @endif
                    <div class="col-12">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" class="form-control"
                            rows="3">{{ old('description', $material->description) }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">File (pdf/doc/video/image) — Maks 50MB
                            @if($material->file_type === 'link')
                                — Saat ini: <a href="{{ $material->file_path }}" target="_blank">Link</a>
                            @else
                                — Saat ini: <a href="{{ Storage::url($material->file_path) }}" target="_blank">Lihat</a>
                            @endif
                        </label>
                        <input type="file" name="file" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Atau Link Materi</label>
                        <input type="url" name="link" class="form-control"
                            value="{{ $material->file_type === 'link' ? $material->file_path : '' }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tampilkan</label>
                        <select name="is_visible" class="form-select">
                            <option value="1" {{ old('is_visible', $material->is_visible) == 1 ? 'selected' : '' }}>Ya
                            </option>
                            <option value="0" {{ old('is_visible', $material->is_visible) == 0 ? 'selected' : '' }}>Tidak
                            </option>
                        </select>
                    </div>
                </div>
                <div class="mt-4 text-end">
                    @if(request()->query('training_class_id'))
                        <a href="{{ route('teacher.training-class.materials', request()->query('training_class_id')) }}"
                            class="btn btn-outline-secondary">Batal</a>
                    @else
                        <a href="{{ route('teacher.materials.index') }}" class="btn btn-outline-secondary">Batal</a>
                    @endif
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
            var trainingSel = document.querySelector('select[name=training_class_id]');
            var subjectSel = document.querySelector('select[name=subject_id]');
            var submitBtn = document.querySelector('button[type=submit]');
            if (submitBtn && ((!hasValidOption(classSel) && !hasValidOption(trainingSel)) || (subjectSel && !hasValidOption(subjectSel)))) {
                submitBtn.disabled = true;
            }
            // If a training class is selected, do not require class select
            if (classSel && trainingSel) {
                trainingSel.addEventListener('change', function () {
                    if (trainingSel.value) {
                        classSel.disabled = true;
                    } else {
                        classSel.disabled = false;
                    }
                });
            }
        });
    </script>
@endsection