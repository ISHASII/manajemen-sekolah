@extends('layouts.app')

@section('title', 'Manage Aplikasi')

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <div class="row align-items-center">
                            <div class="col">
                                <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>Manage Aplikasi Siswa</h5>
                            </div>
                            <div class="col-auto">
                                <span class="badge bg-warning">{{ $applications->where('status', 'pending')->count() }}
                                    Pending</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($applications->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No. Aplikasi</th>
                                            <th>Nama Siswa</th>
                                            <th>Email</th>
                                            <th>Tanggal Daftar</th>
                                            <th>Kelas</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($applications as $application)
                                            <tr>
                                                <td class="fw-bold">{{ $application->application_number }}</td>
                                                <td>{{ $application->student_name }}</td>
                                                <td>{{ $application->email }}</td>
                                                <td>{{ $application->application_date->format('d M Y') }}</td>
                                                <td>{{ $application->desired_class }}</td>
                                                <td>
                                                    @if($application->status === 'pending')
                                                        <span class="badge bg-warning">Pending</span>
                                                    @elseif($application->status === 'approved')
                                                        <span class="badge bg-success">Disetujui</span>
                                                    @elseif($application->status === 'rejected')
                                                        <span class="badge bg-danger">Ditolak</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.applications.detail', $application->id) }}"
                                                        class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i> Detail
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{ $applications->links() }}
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Belum ada aplikasi</h5>
                                <p class="text-muted">Aplikasi siswa akan muncul di sini</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection