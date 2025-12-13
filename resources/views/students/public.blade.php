@extends('layouts.app')

@section('title', 'Profil Publik Siswa - ' . $student->user->name)

@push('styles')
    <style>
        /* Hide global navigation on public student page */
        .navbar {
            display: none !important;
        }

        .student-public-profile {
            max-width: 800px;
            margin: 0 auto;
        }

        .profile-header {
            background: var(--bs-warning, #ffc107);
            border-radius: 16px 16px 0 0;
            padding: 30px;
            color: #000;
            text-align: center;
        }

        .profile-photo {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 4px solid white;
            object-fit: cover;
            background: #e9ecef;
        }

        .profile-photo-placeholder {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 4px solid white;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .student-name {
            font-size: 1.75rem;
            font-weight: 700;
            margin-top: 15px;
            margin-bottom: 5px;
        }

        .student-id-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
        }

        .profile-body {
            background: white;
            border-radius: 0 0 16px 16px;
            padding: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .info-section {
            margin-bottom: 25px;
        }

        .info-section-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e5e7eb;
        }

        .info-row {
            display: flex;
            padding: 10px 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            width: 150px;
            font-weight: 600;
            color: #6b7280;
            flex-shrink: 0;
        }

        .info-value {
            color: #111827;
            flex: 1;
        }

        .qr-section {
            text-align: center;
            padding: 20px;
            background: #f9fafb;
            border-radius: 12px;
            margin-top: 20px;
        }

        .qr-container {
            display: inline-block;
            padding: 15px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .qr-label {
            margin-top: 10px;
            font-size: 0.85rem;
            color: #6b7280;
        }

        .class-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.3);
            padding: 3px 12px;
            border-radius: 15px;
            font-size: 0.85rem;
            margin-top: 5px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-active {
            background: #d1fae5;
            color: #065f46;
        }

        .status-inactive {
            background: #fee2e2;
            color: #991b1b;
        }
    </style>
@endpush

@section('content')
    <div class="container py-4">
        <div class="student-public-profile">
            {{-- Header dengan foto dan nama --}}
            <div class="profile-header">
                <div class="d-flex justify-content-center">
                    @if($student->user->profile_photo)
                        <img src="{{ Storage::url($student->user->profile_photo) }}" class="profile-photo"
                            alt="Foto {{ $student->user->name }}">
                    @else
                        <div class="profile-photo-placeholder">
                            <i class="fas fa-user fa-3x text-white"></i>
                        </div>
                    @endif
                </div>
                <h1 class="student-name">{{ $student->user->name }}</h1>
                <div class="student-id-badge">
                    <i class="fas fa-id-card me-1"></i> {{ $student->student_id ?: 'ID: -' }}
                </div>
                @if($student->classRoom)
                    <div class="class-badge">
                        <i class="fas fa-graduation-cap me-1"></i> {{ $student->classRoom->name }}
                    </div>
                @endif
            </div>

            {{-- Body dengan informasi detail --}}
            <div class="profile-body">
                {{-- Status --}}
                <div class="text-center mb-4">
                    @if($student->status === 'active')
                        <span class="status-badge status-active">
                            <i class="fas fa-check-circle me-1"></i> Siswa Aktif
                        </span>
                    @else
                        <span class="status-badge status-inactive">
                            <i class="fas fa-times-circle me-1"></i> {{ ucfirst($student->status ?? 'Tidak Diketahui') }}
                        </span>
                    @endif
                </div>

                {{-- Data Pribadi --}}
                <div class="info-section">
                    <h5 class="info-section-title">
                        <i class="fas fa-user me-2 text-primary"></i>Data Pribadi
                    </h5>
                    <div class="info-row">
                        <div class="info-label">NISN</div>
                        <div class="info-value">{{ $student->nisn ?: '-' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Jenis Kelamin</div>
                        <div class="info-value">
                            @if($student->user->gender === 'male')
                                <i class="fas fa-mars text-primary me-1"></i> Laki-laki
                            @elseif($student->user->gender === 'female')
                                <i class="fas fa-venus text-danger me-1"></i> Perempuan
                            @else
                                -
                            @endif
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Tempat Lahir</div>
                        <div class="info-value">{{ $student->place_of_birth ?: '-' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Tanggal Lahir</div>
                        <div class="info-value">
                            {{ $student->birth_date ? \Carbon\Carbon::parse($student->birth_date)->translatedFormat('d F Y') : '-' }}
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Agama</div>
                        <div class="info-value">{{ $student->religion ? ucfirst($student->religion) : '-' }}</div>
                    </div>
                </div>

                {{-- Kontak --}}
                <div class="info-section">
                    <h5 class="info-section-title">
                        <i class="fas fa-address-book me-2 text-success"></i>Informasi Kontak
                    </h5>
                    <div class="info-row">
                        <div class="info-label">Alamat</div>
                        <div class="info-value">{{ $student->address ?: ($student->user->address ?? '-') }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">No. Telepon</div>
                        <div class="info-value">
                            @if($student->user->phone)
                                <i class="fas fa-phone me-1 text-muted"></i> {{ $student->user->phone }}
                            @else
                                -
                            @endif
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Email</div>
                        <div class="info-value">
                            @if($student->user->email)
                                <i class="fas fa-envelope me-1 text-muted"></i> {{ $student->user->email }}
                            @else
                                -
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Data Orang Tua --}}
                <div class="info-section">
                    <h5 class="info-section-title">
                        <i class="fas fa-users me-2 text-warning"></i>Data Orang Tua/Wali
                    </h5>
                    <div class="info-row">
                        <div class="info-label">Nama</div>
                        <div class="info-value">{{ $student->parent_name ?: '-' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">No. Telepon</div>
                        <div class="info-value">{{ $student->parent_phone ?: '-' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Pekerjaan</div>
                        <div class="info-value">{{ $student->parent_job ?: '-' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Alamat</div>
                        <div class="info-value">{{ $student->parent_address ?: '-' }}</div>
                    </div>
                </div>

                {{-- Informasi Tambahan --}}
                @if(($student->disability_info && is_array($student->disability_info) && count($student->disability_info)) || ($student->health_info && is_array($student->health_info) && count($student->health_info)) || ($student->interests_talents && is_array($student->interests_talents) && count($student->interests_talents)))
                    <div class="info-section">
                        <h5 class="info-section-title">
                            <i class="fas fa-info-circle me-2 text-info"></i>Informasi Tambahan
                        </h5>
                        @if($student->disability_info && is_array($student->disability_info) && count($student->disability_info))
                            <div class="info-row">
                                <div class="info-label">Disabilitas</div>
                                <div class="info-value">{{ implode(', ', $student->disability_info) }}</div>
                            </div>
                        @endif
                        @if($student->health_info && is_array($student->health_info) && count($student->health_info))
                            <div class="info-row">
                                <div class="info-label">Info Kesehatan</div>
                                <div class="info-value">{{ implode(', ', $student->health_info) }}</div>
                            </div>
                        @endif
                        @if($student->interests_talents && is_array($student->interests_talents) && count($student->interests_talents))
                            <div class="info-row">
                                <div class="info-label">Minat & Bakat</div>
                                <div class="info-value">
                                    @foreach($student->interests_talents as $talent)
                                        <span class="badge bg-light text-dark me-1 mb-1">{{ $talent }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- QR Code Section --}}
                <div class="qr-section">
                    <h6 class="mb-3"><i class="fas fa-qrcode me-2"></i>QR Code Profil</h6>
                    <div class="qr-container">
                        <div id="publicQrCode"></div>
                    </div>
                    <p class="qr-label">Scan untuk membuka halaman profil ini</p>
                </div>
            </div>

            {{-- Footer --}}
            <div class="text-center mt-4">
                <a href="{{ route('home') }}" class="btn btn-outline-primary">
                    <i class="fas fa-home me-1"></i> Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Generate QR Code
            const qrContainer = document.getElementById('publicQrCode');
            if (qrContainer) {
                const currentUrl = window.location.href;
                new QRCode(qrContainer, {
                    text: currentUrl,
                    width: 150,
                    height: 150,
                    colorDark: '#000000',
                    colorLight: '#ffffff',
                    correctLevel: QRCode.CorrectLevel.H
                });
                console.log('QR Code generated for:', currentUrl);
            }
        });
    </script>
@endpush