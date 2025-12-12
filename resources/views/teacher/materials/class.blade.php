@extends('layouts.app')

@section('title', 'Materi Kelas')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Materi untuk: {{ $classRoom->name }}</h3>
            <a href="{{ route('teacher.materials.create') }}?class_id={{ $classRoom->id }}" class="btn btn-primary">Tambah
                Materi</a>
        </div>

        <div class="card">
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if($materials->count() > 0)
                    <div class="list-group">
                        @foreach($materials as $m)
                            <div class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="materials-actions">
                                    <strong>{{ $m->title }}</strong>
                                    @if(isset($hasSubmissionsTable) && $hasSubmissionsTable && ($m->submissions_count ?? 0) > 0)
                                        <span class="badge bg-info text-dark">{{ $m->submissions_count ?? 0 }}</span>
                                    @endif
                                    <div class="small text-muted">{{ optional($m->subject)->name ?? '-' }}</div>
                                    @if($m->description)
                                        <p class="small mb-0">{{ $m->description }}</p>
                                    @endif
                                </div>
                                <div class="materials-actions">
                                    @if($m->file_type === 'link')
                                        <a href="{{ $m->file_path }}" target="_blank"
                                            class="btn btn-sm btn-outline-primary me-1 text-dark">Buka Link</a>
                                    @else
                                        <a href="{{ Storage::url($m->file_path) }}" target="_blank"
                                            class="btn btn-sm btn-outline-primary me-1 text-dark">Lihat/Unduh</a>
                                    @endif
                                    <a href="{{ route('teacher.materials.edit', $m->id) }}?class_id={{ $classRoom->id }}"
                                        class="btn btn-sm btn-outline-secondary me-1 text-dark">Edit</a>
                                    <a href="{{ route('teacher.materials.submissions.index', $m->id) }}"
                                        class="btn btn-sm btn-outline-info me-1 text-dark">Lihat Pengumpulan</a>
                                    <form action="{{ route('teacher.materials.destroy', $m->id) }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger text-dark"
                                            onclick="return confirm('Hapus materi?')">Hapus</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-3">{{ $materials->links() }}</div>
                @else
                    <div class="text-center py-5">Belum ada materi untuk kelas ini.</div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Ensure action button text is black on class materials list */
        .materials-actions .btn {
            color: #000 !important;
        }

        /* If outline classes change color on hover, still keep text black */
        .materials-actions .btn:hover {
            color: #000 !important;
        }
    </style>
@endpush