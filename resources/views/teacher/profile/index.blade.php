@extends('layouts.app')

@section('title', 'Profil Guru')

@section('content')
    <div class="teacher-page-wrapper">
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12 mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-warning border-bottom">
                            <h5 class="mb-0">Profil Guru</h5>
                        </div>
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif
                            <div class="row">
                                <div class="col-md-3 text-center">
                                    @if($user->profile_photo)
                                        <img src="{{ Storage::url($user->profile_photo) }}"
                                            class="img-fluid rounded-circle mb-3" alt="Profile Photo">
                                    @else
                                        <div
                                            class="avatar-lg bg-primary rounded-circle d-flex align-items-center justify-content-center mb-3">
                                            <i class="bi bi-person-circle text-white fs-1"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <a href="{{ route('teacher.profile.edit') }}"
                                            class="btn btn-outline-secondary btn-sm">Edit Profil</a>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <h4>{{ $user->name }}</h4>
                                    <p class="text-muted mb-1">Email: {{ $user->email }}</p>
                                    <p class="text-muted mb-1">Telepon: {{ $user->phone ?? '-' }}</p>
                                    <p class="text-muted mb-1">Alamat: {{ $user->address ?? '-' }}</p>
                                    @if($teacher)
                                        <hr>
                                        <p class="mb-1"><strong>NIP:</strong> {{ $teacher->nip ?? '-' }}</p>
                                        <p class="mb-1"><strong>Mata Pelajaran:</strong>
                                            @php
                                                // Use subjectNames prepared by controller; fallback to teacher->subjects if not set
                                                if (isset($subjectNames) && is_array($subjectNames) && count($subjectNames) > 0) {
                                                    echo implode(', ', $subjectNames);
                                                } else {
                                                    // show fallback, converting arrays or strings properly
                                                    echo is_array($teacher->subjects) ? implode(', ', $teacher->subjects) : ($teacher->subjects ?? '-');
                                                }
                                            @endphp
                                        </p>
                                        <p class="mb-1"><strong>Kualifikasi:</strong>
                                            {{ is_array($teacher->qualifications) ? implode(', ', $teacher->qualifications) : ($teacher->qualifications ?? '-') }}
                                        </p>
                                        <p class="mb-1"><strong>Sertifikasi:</strong>
                                            {{ is_array($teacher->certifications) ? implode(', ', $teacher->certifications) : ($teacher->certifications ?? '-') }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection