@extends('layouts.app')

@section('title', 'Status Aplikasi')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card border-0 shadow-lg">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Status Aplikasi</h4>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <strong>Nomor Aplikasi:</strong><br>
                                <span class="text-primary fs-5">{{ $application->application_number }}</span>
                            </div>
                            <div class="col-md-6">
                                <strong>Nama:</strong><br>
                                {{ $application->student_name }}
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <strong>Tanggal Daftar:</strong><br>
                                {{ $application->application_date->format('d M Y') }}
                            </div>
                            <div class="col-md-6">
                                <strong>Kelas yang Diinginkan:</strong><br>
                                {{ $application->desired_class }}
                            </div>
                        </div>

                        <div class="mb-4">
                            <strong>Status Aplikasi:</strong><br>
                            @if($application->status === 'pending')
                                <span class="badge bg-warning fs-6">
                                    <i class="fas fa-clock me-1"></i>Menunggu Review
                                </span>
                                <p class="text-muted mt-2">Aplikasi Anda sedang dalam proses review. Tim admin akan menghubungi
                                    Anda segera.</p>
                            @elseif($application->status === 'approved')
                                <span class="badge bg-success fs-6">
                                    <i class="fas fa-check me-1"></i>Disetujui
                                </span>
                                <p class="text-success mt-2">Selamat! Aplikasi Anda telah disetujui. Silakan tunggu informasi
                                    lebih lanjut dari pihak sekolah.</p>
                            @elseif($application->status === 'rejected')
                                <span class="badge bg-danger fs-6">
                                    <i class="fas fa-times me-1"></i>Ditolak
                                </span>
                                <p class="text-danger mt-2">Maaf, aplikasi Anda belum dapat kami setujui saat ini.</p>
                            @elseif($application->status === 'waiting_payment')
                                <span class="badge bg-info fs-6">
                                    <i class="fas fa-credit-card me-1"></i>Menunggu Pembayaran
                                </span>
                                <p class="text-info mt-2">Aplikasi Anda telah disetujui. Silakan lakukan pembayaran sesuai
                                    instruksi yang telah dikirim.</p>
                            @endif
                        </div>

                        @if($application->notes)
                            <div class="alert alert-info">
                                <strong>Catatan:</strong><br>
                                {{ $application->notes }}
                            </div>
                        @endif

                        <div class="timeline">
                            <div class="timeline-item">
                                <div class="timeline-marker bg-primary"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Aplikasi Diterima</h6>
                                    <p class="timeline-description">
                                        {{ $application->application_date->format('d M Y, H:i') }}</p>
                                </div>
                            </div>

                            @if($application->status !== 'pending')
                                <div class="timeline-item">
                                    <div class="timeline-marker
                                            @if($application->status === 'approved' || $application->status === 'waiting_payment') bg-success
                                            @else bg-danger @endif">
                                    </div>
                                    <div class="timeline-content">
                                        <h6 class="timeline-title">Status Update</h6>
                                        <p class="timeline-description">{{ $application->updated_at->format('d M Y, H:i') }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="text-center mt-4">
                            <a href="{{ route('application.status') }}" class="btn btn-outline-primary me-2">
                                <i class="fas fa-refresh me-1"></i>Cek Lagi
                            </a>
                            <a href="{{ route('home') }}" class="btn btn-secondary">
                                <i class="fas fa-home me-1"></i>Beranda
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .timeline {
            position: relative;
            padding-left: 30px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 10px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #dee2e6;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 20px;
        }

        .timeline-marker {
            position: absolute;
            left: -25px;
            top: 5px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
        }

        .timeline-content {
            padding-left: 10px;
        }

        .timeline-title {
            margin-bottom: 5px;
            font-weight: 600;
        }

        .timeline-description {
            margin-bottom: 0;
            color: #6c757d;
            font-size: 0.9rem;
        }
    </style>
@endsection