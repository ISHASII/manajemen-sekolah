@extends('layouts.app')

@section('title', 'Tenaga Pendidik')

@section('content')
    <section class="py-5" style="background: linear-gradient(135deg, rgba(10,29,81,0.02), rgba(10,29,81,0.03));">
        <div class="container py-5">
            <div class="text-center mb-5">
                <span class="badge rounded-pill px-3 py-2 mb-3"
                    style="background: rgba(245,158,11,0.15); color: var(--orange);">Tenaga Pendidik</span>
                <h1 class="display-5 fw-bold mb-3" style="color: #fff;">Tenaga Pendidik</h1>
                <p class="lead text-white-50 mx-auto" style="max-width: 800px;">
                    Profesional guru dan tenaga pendidik kami yang berdedikasi, berpengalaman, dan berkomitmen untuk
                    mendampingi tumbuh kembang siswa.
                </p>
            </div>

            @if(($teachers ?? collect())->count() > 0)
                <div class="row g-4">
                    @foreach($teachers as $teacher)
                        @php
                            // $teacher is User model (role=teacher) with optional ->teacher relation
                            $profile = $teacher->profile_photo ?? null;
                            $teacherProfile = $teacher->teacher ?? null;
                            $subjects = $teacherProfile->subjects ?? [];
                            $qualification = data_get($teacherProfile, 'qualifications', null);
                        @endphp
                        <div class="col-xl-3 col-lg-4 col-md-6">
                            <div class="card rounded-4 shadow-lg p-4 h-100 text-center bg-dark-subtle"
                                style="border: 1px solid rgba(255,255,255,0.03)">
                                <div class="mb-3 d-flex align-items-center justify-content-center mx-auto rounded-circle bg-gray"
                                    style="width:90px; height:90px; overflow:hidden;">
                                    @if($profile)
                                        <img src="{{ asset($profile) }}" alt="{{ $teacher->name }}"
                                            style="width:90px; height:90px; object-fit:cover;">
                                    @else
                                        <div class="d-flex align-items-center justify-content-center bg-secondary"
                                            style="width:90px; height:90px;">
                                            <i class="bi bi-person-fill fs-2 text-white"></i>
                                        </div>
                                    @endif
                                </div>

                                <h5 class="fw-semibold text-white mb-1">{{ $teacher->name }}</h5>
                                @if(!empty($subjects))
                                    @php
                                        // Normalize subjects into a string. Subjects might be an array of scalars or arrays/objects
                                        $subjectsList = collect((array) $subjects)->map(function ($s) {
                                            if (is_array($s) || is_object($s)) {
                                                // Try common keys
                                                $sArr = (array) $s;
                                                return data_get($sArr, 'name') ?? data_get($sArr, 'subject') ?? implode(' - ', array_filter(array_values($sArr)));
                                            }
                                            return (string) $s;
                                        })->filter()->values()->all();
                                    @endphp
                                    <p class="mb-2 text-white-50 small">{{ implode(', ', $subjectsList) }}</p>
                                @endif

                                @if($qualification)
                                    @php
                                        $qualList = collect((array) $qualification)->map(function ($q) {
                                            if (is_array($q) || is_object($q)) {
                                                $qArr = (array) $q;
                                                // Format: Degree (Institution, Year) when available
                                                $degree = data_get($qArr, 'degree') ?? data_get($qArr, 'name') ?? null;
                                                $institution = data_get($qArr, 'institution') ?? data_get($qArr, 'issuer') ?? null;
                                                $year = data_get($qArr, 'year') ?? null;
                                                $parts = array_filter([$degree, $institution]);
                                                $text = $parts ? implode(', ', $parts) : implode(' ', array_filter(array_values($qArr)));
                                                return $year ? ($text . ' (' . $year . ')') : $text;
                                            }
                                            return (string) $q;
                                        })->filter()->values()->all();
                                    @endphp
                                    <p class="text-white-50 small mb-3">{{ implode('; ', $qualList) }}</p>
                                @endif

                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    @if(!empty($teacher->phone))
                                        <a href="tel:{{ $teacher->phone }}" class="btn btn-outline-light btn-sm rounded-pill px-3 py-1">
                                            <i class="bi bi-telephone"></i>
                                        </a>
                                    @endif
                                    <a href="mailto:{{ $teacher->email }}"
                                        class="btn btn-outline-light btn-sm rounded-pill px-3 py-1">
                                        <i class="bi bi-envelope-fill"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <p class="lead text-white-50">Belum ada data tenaga pendidik. Silakan tambah guru lewat dashboard admin.</p>
                    @auth
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.teachers.create') }}" class="btn btn-primary">Tambah Tenaga Pendidik</a>
                        @endif
                    @endauth
                </div>
            @endif
        </div>
    </section>

    <style>
        .bg-gray {
            background: rgba(255, 255, 255, 0.04);
            border-radius: 50%;
        }

        .bg-dark-subtle {
            background: linear-gradient(180deg, rgba(10, 29, 81, 0.98), rgba(17, 35, 64, 0.98));
        }

        @media (max-width: 767px) {
            .card {
                padding: 1.5rem;
            }

            .mb-3 img {
                width: 72px;
                height: 72px;
            }
        }
    </style>
@endsection