@extends('layouts.app')

@section('title', 'Materi Kelas')

@section('content')
    <div class="container py-4">
        <h3>Materi Kelas</h3>
        <div class="card mt-3 p-4" style="background-color: #ffffff; color: #000;">
            @if(session('success'))
                <div class="alert alert-success" style="color:#000;">{{ session('success') }}</div>
            @endif

            @if($materials && $materials->count())
                <div class="list-group">
                    @foreach($materials as $m)
                        <div class="list-group-item d-flex justify-content-between align-items-center" style="color:#000;">

                            <div>
                                <strong style="color:#000;">{{ $m->title }}</strong>

                                <div class="small" style="color: #555 !important;">
                                    {{ $m->classRoom?->name ?? 'Semua' }} —
                                    {{ $m->subject?->name ?? '-' }}
                                </div>
                            </div>

                            <div>
                                <a class="btn btn-sm btn-outline-secondary" style="color:#000; border-color:#000;"
                                    href="{{ Storage::url($m->file_path) }}" target="_blank">
                                    Lihat/Unduh
                                </a>
                            </div>

                        </div>
                    @endforeach
                </div>

                <div class="mt-3" style="color:#000;">
                    {{ $materials->links() }}
                </div>

            @else
                <div class="text-muted" style="color:#000 !important;">Tidak ada materi yang tersedia untuk kelas Anda.</div>
            @endif
        </div>

    </div>
@endsection
