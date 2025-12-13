@extends('layouts.app')

@section('title', 'Pengumpulan Tugas - ' . ($material->title ?? ''))

@section('content')
    <div class="container py-4">
        <h3>Pengumpulan Tugas untuk: {{ $material->title }}</h3>
        <div class="card mt-3 p-4" style="background-color: #ffffff; color: #000;">
            @if($submissions && $submissions->count())
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama Siswa</th>
                                <th>Berkas / Link</th>
                                <th>Deskripsi</th>
                                <th>Dikirim Pada</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($submissions as $s)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $s->student?->user?->name ?? ($s->student?->student_id ?? 'N/A') }}</td>
                                    <td class="submission-file">
                                        @php
                                            $imageTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                                            $videoTypes = ['mp4', 'mov', 'avi', 'mkv', 'webm'];
                                            $docTypes = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx'];
                                        @endphp

                                        @if($s->file_type === 'link' && $s->link)
                                            <a href="{{ $s->link }}" target="_blank" rel="noopener noreferrer" class="text-dark">Link
                                                Pengumpulan</a>
                                        @elseif($s->file_path)
                                            @php
                                                $ext = strtolower($s->file_type ?? '');
                                                $url = Storage::url($s->file_path);
                                                $basename = \Illuminate\Support\Str::afterLast($s->file_path, '/');
                                            @endphp
                                            @if(in_array($ext, $imageTypes))
                                                <a href="{{ $url }}" target="_blank" class="text-dark">
                                                    <img src="{{ $url }}" alt="{{ $basename }}"
                                                        style="max-width:120px; max-height:90px; object-fit:cover; border-radius:4px;" />
                                                </a>
                                                <div class="small mt-1 text-dark">{{ $basename }}</div>
                                            @elseif(in_array($ext, $videoTypes))
                                                <video controls style="max-width:200px; max-height:120px;">
                                                    <source src="{{ $url }}" type="video/{{ $ext }}">
                                                    Your browser does not support the video tag.
                                                </video>
                                                <div class="small mt-1 text-dark">{{ $basename }}</div>
                                            @else
                                                <div class="d-flex align-items-center gap-2">
                                                    <a href="{{ $url }}" target="_blank"
                                                        class="btn btn-sm btn-outline-primary text-dark">Unduh</a>
                                                    <div class="small text-dark">{{ $basename }} <span
                                                            class="ms-2">({{ strtoupper($ext) }})</span></div>
                                                </div>
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $s->description }}</td>
                                    <td>{{ $s->created_at->translatedFormat('d M Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $submissions->links() }}
                </div>
            @else
                <div class="text-muted">Belum ada pengumpulan tugas.</div>
            @endif
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Ensure that file name and download links show black text on submissions listing */
        .submission-file a,
        .submission-file .small,
        .submission-file .btn {
            color: #000 !important;
        }

        /* Also prevent the link color from changing on hover */
        .submission-file a:hover,
        .submission-file .btn:hover {
            color: #000 !important;
        }
    </style>
@endpush