@extends('layouts.app')

@section('title', 'Materi Saya')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Materi Saya</h3>
            <a href="{{ route('teacher.materials.create') }}" class="btn btn-primary">Tambah Materi</a>
        </div>
        <div class="card">
            <div class="card-body bg-warning rounded-3">
                @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>@endif
                @if($materials->count())
                    <div class="list-group">
                        @foreach($materials as $m)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $m->title }}</strong>
                                    <div class="small text-dark">{{ $m->classRoom?->name ?? '-' }} -
                                        {{ $m->subject?->name ?? '-' }}
                                    </div>
                                </div>
                                <div>
                                    <a class="btn btn-sm btn-outline-secondary text-dark" href="{{ Storage::url($m->file_path) }}"
                                        target="_blank">Lihat/Unduh</a>
                                    <a class="btn btn-sm btn-outline-primary text-dark"
                                        href="{{ route('teacher.materials.edit', $m->id) }}">Edit</a>
                                    <form method="POST" action="{{ route('teacher.materials.destroy', $m->id) }}"
                                        class="d-inline-block" onsubmit="return confirm('Hapus materi?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">Hapus</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-3">{{ $materials->links() }}</div>
                @else
                    <div class="text-dark">Belum ada materi.</div>

                @endif
            </div>
        </div>
    </div>
@endsection
