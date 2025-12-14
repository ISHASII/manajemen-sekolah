@extends('layouts.app')

@section('title', 'Daftar Rekap Absensi')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h3>Daftar Rekap Absensi</h3>
                <div class="small text-muted">Pilih kelas untuk melihat rekap absensi per-hari atau bulanan.</div>
            </div>
            <div>
                <a href="{{ route('teacher.dashboard') }}" class="btn btn-outline-secondary">Kembali</a>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-header"><strong>Kelas Anda</strong></div>
                    <div class="card-body">
                        @if($classes && $classes->count())
                            <ul class="list-group list-group-flush">
                                @foreach($classes as $class)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $class->name }}</strong>
                                            <div class="small text-muted">{{ $class->students_count ?? 0 }} siswa</div>
                                        </div>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('teacher.class.attendance', $class->id) }}"
                                                class="btn btn-primary">Rekap</a>
                                            <a href="{{ route('teacher.class.detail', $class->id) }}"
                                                class="btn btn-sm btn-warning">Lihat</a>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="text-muted">Tidak ada kelas</div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header"><strong>Kelas Pelatihan</strong></div>
                    <div class="card-body">
                        @if($trainingClasses && $trainingClasses->count())
                            <ul class="list-group list-group-flush">
                                @foreach($trainingClasses as $tc)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $tc->title }}</strong>
                                            <div class="small text-muted">{{ $tc->students_count ?? 0 }} peserta</div>
                                        </div>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('teacher.training-class.attendance', $tc->id) }}"
                                                class="btn btn-primary">Rekap</a>
                                            <a href="{{ route('teacher.training-class.detail', $tc->id) }}"
                                                class="btn btn-sm btn-warning">Lihat</a>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="text-muted">Tidak ada pelatihan</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection