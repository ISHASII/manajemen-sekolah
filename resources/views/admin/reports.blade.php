@extends('layouts.admin')

@section('title', 'Laporan Sistem')

@section('admin-content')
    <div class="container py-4">
        <h3>Laporan & Statistik</h3>
        <div class="row mt-3">
            <div class="col-md-4">
                <div class="card p-3">
                    <h5>Total Siswa</h5>
                    <p class="fs-3">{{ $totalStudents ?? 0 }}</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3">
                    <h5>Siswa dengan Disabilitas</h5>
                    <p class="fs-3">{{ $studentsWithDisability ?? 0 }}</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3">
                    <h5>Siswa Yatim</h5>
                    <p class="fs-3">{{ $orphanStudents ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card p-3">
                    <h5>Distribusi Nilai</h5>
                    @if(!empty($gradeDistribution))
                        <ul>
                            @foreach($gradeDistribution as $grade => $count)
                                <li>{{ $grade }}: {{ $count }}</li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">Tidak ada data.</p>
                    @endif
                </div>
            </div>
            <div class="col-md-6">
                <div class="card p-3">
                    <h5>Distribusi Kelas</h5>
                    @if(!empty($classDistribution))
                        <ul>
                            @foreach($classDistribution as $class)
                                <li>{{ $class->name }}: {{ $class->students_count ?? 0 }} siswa</li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">Tidak ada data.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection