@extends('layouts.app')

@section('title', 'Rekap Absensi Bulanan')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>Rekap Absensi Bulanan - {{ $student->user->name }}</h4>
            <div>
                <a href="?mode=daily&date={{ now()->format('Y-m-d') }}" class="btn btn-sm btn-outline-secondary">Harian</a>
                <a href="?mode=monthly&month={{ request('month', $start->format('Y-m')) }}"
                    class="btn btn-sm btn-primary">Bulanan</a>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" class="row g-2 align-items-center">
                    <input type="hidden" name="mode" value="monthly" />
                    <div class="col-auto">
                        <label class="form-label">Bulan</label>
                        <input type="month" name="month" value="{{ request('month', $start->format('Y-m')) }}"
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
            <div class="card-header"><strong>Ringkasan Bulan:
                    {{ $start->translatedFormat('F Y') }}@if(isset($selectedSubject) && $selectedSubject) — Mata Pelajaran:
                    {{ $subjects->firstWhere('id', $selectedSubject)?->name ?? '-' }}@endif</strong></div>
            <div class="card-body">
                <div class="mb-3">
                    <span class="me-3"><strong>Hadir:</strong> {{ $summary['present'] ?? 0 }}</span>
                    <span class="me-3"><strong>Alpha:</strong> {{ $summary['absent'] ?? 0 }}</span>
                    <span class="me-3"><strong>Sakit:</strong> {{ $summary['sick'] ?? 0 }}</span>
                    <span class="me-3"><strong>Izin:</strong> {{ $summary['excused'] ?? 0 }}</span>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th>Rincian</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dates as $d)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($d)->translatedFormat('d F Y') }}</td>
                                    <td>
                                        @php $arr = $map[$d] ?? null; @endphp
                                        @if($arr)
                                            @foreach($arr as $rec)
                                                <span class="badge bg-info me-1">{{ ucfirst($rec->status) }}</span>
                                            @endforeach
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if($arr)
                                            @foreach($arr as $rec)
                                                <small class="d-block text-muted">
                                                    {{ $rec->trainingClass?->title ?? $rec->classRoom?->name ?? '-' }}
                                                    @if($rec->subject)
                                                        — {{ $rec->subject->name }}
                                                    @endif
                                                    — {{ $rec->notes ?? '-' }}
                                                </small>
                                            @endforeach
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection