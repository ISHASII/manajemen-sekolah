@extends('layouts.app')

@section('title', 'Jadwal Pelajaran')

@section('content')
    <div class="container py-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Jadwal Pelajaran</h2>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-primary" onclick="window.print()">
                            <i class="fas fa-print"></i> Cetak
                        </button>
                    </div>
                </div>

                @if($schedules->count() > 0)
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-calendar me-2"></i>Jadwal Mingguan</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="120">Waktu</th>
                                            <th>Senin</th>
                                            <th>Selasa</th>
                                            <th>Rabu</th>
                                            <th>Kamis</th>
                                            <th>Jumat</th>
                                            <th>Sabtu</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            // Get all unique time slots
                                            $timeSlots = collect();
                                            foreach ($schedules as $daySchedules) {
                                                foreach ($daySchedules as $schedule) {
                                                    $timeSlot = $schedule->start_time . ' - ' . $schedule->end_time;
                                                    if (!$timeSlots->contains($timeSlot)) {
                                                        $timeSlots->push($timeSlot);
                                                    }
                                                }
                                            }
                                            $timeSlots = $timeSlots->sort();

                                            $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
                                            $dayNames = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                                        @endphp

                                        @foreach($timeSlots as $timeSlot)
                                            <tr>
                                                <td class="fw-bold text-center align-middle bg-light">
                                                    {{ $timeSlot }}
                                                </td>
                                                @foreach($days as $day)
                                                    <td class="align-middle">
                                                        @php
                                                            $daySchedules = $schedules->get($day, collect());
                                                            $currentSchedule = $daySchedules->first(function ($schedule) use ($timeSlot) {
                                                                return ($schedule->start_time . ' - ' . $schedule->end_time) === $timeSlot;
                                                            });
                                                        @endphp

                                                        @if($currentSchedule)
                                                            <div
                                                                class="schedule-item p-2 rounded border-start border-4 border-primary bg-light">
                                                                <div class="fw-bold text-primary">{{ $currentSchedule->subject->name }}
                                                                </div>
                                                                <small
                                                                    class="text-dark d-block">{{ $currentSchedule->teacher->name }}</small>
                                                                @if($currentSchedule->room)
                                                                    <small class="text-dark d-block">
                                                                        <i class="fas fa-map-marker-alt me-1"></i>{{ $currentSchedule->room }}
                                                                    </small>
                                                                @endif
                                                            </div>
                                                        @else
                                                            <div class="text-center text-muted py-3">
                                                                -
                                                            </div>
                                                        @endif
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Today's Schedule -->
                    @php
                        $today = strtolower(date('l'));
                        $todayIndonesian = [
                            'sunday' => 'minggu',
                            'monday' => 'senin',
                            'tuesday' => 'selasa',
                            'wednesday' => 'rabu',
                            'thursday' => 'kamis',
                            'friday' => 'jumat',
                            'saturday' => 'sabtu'
                        ];
                        $todaySchedules = $schedules->get($today, collect());
                    @endphp

                    @if($todaySchedules->count() > 0)
                        <div class="card border-0 shadow-sm mt-4">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-today me-2"></i>
                                    Jadwal Hari Ini ({{ ucfirst($todayIndonesian[$today]) }}, {{ date('d M Y') }})
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach($todaySchedules as $schedule)
                                        <div class="col-md-6 col-lg-4 mb-3">
                                            <div class="card border-start border-4 border-success h-100">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <h6 class="card-title mb-1">{{ $schedule->subject->name }}</h6>
                                                        <small class="text-muted">{{ $schedule->subject->code }}</small>
                                                    </div>

                                                    <p class="text-muted mb-2">
                                                        <i class="fas fa-user me-1"></i>{{ $schedule->teacher->name }}
                                                    </p>

                                                    <p class="text-muted mb-2">
                                                        <i class="fas fa-clock me-1"></i>
                                                        {{ $schedule->start_time }} - {{ $schedule->end_time }}
                                                    </p>

                                                    @if($schedule->room)
                                                        <p class="text-muted mb-0">
                                                            <i class="fas fa-map-marker-alt me-1"></i>{{ $schedule->room }}
                                                        </p>
                                                    @endif

                                                    @php
                                                        $now = now();
                                                        $scheduleStart = \Carbon\Carbon::createFromFormat('H:i:s', $schedule->start_time);
                                                        $scheduleEnd = \Carbon\Carbon::createFromFormat('H:i:s', $schedule->end_time);
                                                        $currentTime = \Carbon\Carbon::createFromFormat('H:i:s', $now->format('H:i:s'));
                                                    @endphp

                                                    @if($currentTime->between($scheduleStart, $scheduleEnd))
                                                        <span class="badge bg-success mt-2">Sedang Berlangsung</span>
                                                    @elseif($currentTime->lt($scheduleStart))
                                                        <span class="badge bg-info mt-2">Akan Datang</span>
                                                    @else
                                                        <span class="badge bg-secondary mt-2">Selesai</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Schedule Legend -->
                    <div class="card border-0 shadow-sm mt-4">
                        <div class="card-header">
                            <h6 class="mb-0">Keterangan</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 mb-2">
                                    <span class="badge bg-success">Sedang Berlangsung</span> - Kelas sedang aktif
                                </div>
                                <div class="col-md-3 mb-2">
                                    <span class="badge bg-info">Akan Datang</span> - Kelas belum dimulai
                                </div>
                                <div class="col-md-3 mb-2">
                                    <span class="badge bg-secondary">Selesai</span> - Kelas telah berakhir
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum ada jadwal</h5>
                            <p class="text-muted">Jadwal pelajaran akan muncul setelah Anda ditempatkan di kelas</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        @media print {

            .btn,
            .card-header,
            .navbar,
            .footer {
                display: none !important;
            }

            .card {
                border: 1px solid #000 !important;
                box-shadow: none !important;
            }

            .table {
                font-size: 12px;
            }

            .schedule-item {
                background-color: #f8f9fa !important;
            }
        }

        .schedule-item {
            transition: all 0.3s ease;
        }

        .schedule-item:hover {
            background-color: #e9ecef !important;
            transform: translateY(-1px);
        }
    </style>
@endsection