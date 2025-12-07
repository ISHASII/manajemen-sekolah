@extends('layouts.app')

@section('title', 'Jadwal Mengajar')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Jadwal Mengajar</h2>
        </div>

        @if(($schedules ?? collect())->count() === 0)
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <p class="mb-0 text-muted">Belum ada jadwal mengajar.</p>
                </div>
            </div>
        @else
            @foreach($schedules as $day => $daySchedules)
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 text-capitalize">{{ $day }}</h5>
                    </div>
                    <div class="card-body">
                        @foreach($daySchedules as $sch)
                            <div class="d-flex align-items-center mb-2">
                                <div class="flex-grow-1">
                                    <strong>{{ optional($sch->classRoom)->name ?? '-' }} -
                                        {{ optional($sch->subject)->name ?? '-' }}</strong>
                                    <div class="small text-muted">{{ \Carbon\Carbon::parse($sch->start_time)->format('H:i') }} -
                                        {{ \Carbon\Carbon::parse($sch->end_time)->format('H:i') }} • {{ $sch->room ?? '-' }}</div>
                                </div>
                                <div class="text-end">
                                    <a href="{{ route('teacher.students') }}" class="btn btn-outline-primary btn-sm">Daftar Siswa</a>
                                </div>
                            </div>
                            <hr />
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endif

    </div>
@endsection