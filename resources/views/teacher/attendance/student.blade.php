@extends('layouts.app')

@section('title', 'Rekap Absensi Siswa')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4>Rekap Absensi: {{ $student->user->name }}</h4>
                <div class="small text-muted">Kelas: {{ optional($student->classRoom)->name ?? '-' }}</div>
            </div>
            <div>
                <a href="{{ route('teacher.students.detail', $student->id) }}"
                    class="btn btn-sm btn-outline-secondary">Kembali</a>
                <a href="?mode=monthly&month={{ request('month', now()->format('Y-m')) }}"
                    class="btn btn-sm btn-outline-primary">Lihat Bulanan</a>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><strong>Absensi pada
                    {{ \Carbon\Carbon::parse($selectedDate ?? now())->translatedFormat('d F Y') }}</strong></div>
            <div class="card-body">
                @if($attendances && $attendances->count())
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Jenis</th>
                                    <th>Asal</th>
                                    <th>Status</th>
                                    <th>Guru</th>
                                    <th>Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($attendances as $a)
                                    <tr>
                                        <td>{{ $a->training_class_id ? 'Pelatihan' : 'Kelas' }}</td>
                                        <td>
                                            @if($a->trainingClass)
                                                {{ $a->trainingClass->title }}
                                            @elseif($a->classRoom)
                                                {{ $a->classRoom->name }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td><span class="badge bg-info">{{ ucfirst($a->status) }}</span></td>
                                        <td>{{ optional($a->teacher)->name ?? '-' }}</td>
                                        <td>{{ $a->notes ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4 text-muted">Tidak ada data absensi untuk tanggal ini.</div>
                @endif
            </div>
        </div>
    </div>
@endsection