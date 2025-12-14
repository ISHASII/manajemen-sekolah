@extends('layouts.app')

@section('title', 'Rekap Absensi Saya')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>Rekap Absensi - {{ $student->user->name }}</h4>
            <div>
                <a href="?mode=daily&date={{ request('date', now()->format('Y-m-d')) }}"
                    class="btn btn-sm btn-primary">Harian</a>
                <a href="?mode=monthly&month={{ request('month', now()->format('Y-m')) }}"
                    class="btn btn-sm btn-outline-secondary">Bulanan</a>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" class="row g-2 align-items-center">
                    <input type="hidden" name="mode" value="daily" />
                    <div class="col-auto">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="date" value="{{ $selectedDate ?? now()->format('Y-m-d') }}"
                            class="form-control">
                    </div>
                    <div class="col-auto">
                        <label class="form-label">Mata Pelajaran</label>
                        <select name="subject_id" class="form-select">
                            <option value="">-- Semua Mata Pelajaran --</option>
                            @foreach($subjects as $sub)
                                <option value="{{ $sub->id }}" {{ (isset($selectedSubject) && $selectedSubject == $sub->id) ? 'selected' : '' }}>{{ $sub->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto mt-4">
                        <button class="btn btn-primary">Tampilkan</button>
                    </div>
                </form>
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
                                    <th>Mata Pelajaran</th>
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
                                        <td>{{ $a->subject?->name ?? '-' }}</td>
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