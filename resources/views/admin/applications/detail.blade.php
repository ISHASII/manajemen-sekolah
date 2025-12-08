@extends('layouts.admin')

@section('title', 'Detail Aplikasi')

@section('admin-content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Detail Aplikasi</h2>
                    <a href="{{ route('admin.applications.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Kembali
                    </a>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-warning text-white">
                        <div class="row align-items-center">
                            <div class="col">
                                <h5 class="mb-0">{{ $application->student_name }}</h5>
                                <small>{{ $application->application_number }}</small>
                            </div>
                            <div class="col-auto">
                                @if($application->status === 'pending')
                                    <span class="badge bg-primary fs-6">Pending</span>
                                @elseif($application->status === 'approved')
                                    <span class="badge bg-success fs-6">Disetujui</span>
                                @elseif($application->status === 'rejected')
                                    <span class="badge bg-danger fs-6">Ditolak</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if(session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Data Pribadi</strong>
                                <hr>
                                <p><strong>Nama:</strong> {{ $application->student_name }}</p>
                                <p><strong>Email:</strong> {{ $application->email }}</p>
                                <p><strong>Telepon:</strong> {{ $application->phone }}</p>
                                <p><strong>NISN:</strong> {{ $application->nisn ?: '-' }}</p>
                                <p><strong>Tempat Lahir:</strong> {{ $application->place_of_birth }}</p>
                                <p><strong>Tanggal Lahir:</strong>
                                    {{ Carbon\Carbon::parse($application->birth_date)->format('d M Y') }}</p>
                                <p><strong>Jenis Kelamin:</strong>
                                    {{ $application->gender === 'male' ? 'Laki-laki' : 'Perempuan' }}</p>
                                <p><strong>Agama:</strong> {{ ucfirst($application->religion) }}</p>
                                <p><strong>Alamat:</strong> {{ $application->address }}</p>
                            </div>

                            <div class="col-md-6">
                                <strong>Data Orang Tua/Wali</strong>
                                <hr>
                                <p><strong>Nama:</strong> {{ $application->parent_name }}</p>
                                <p><strong>Telepon:</strong> {{ $application->parent_phone }}</p>
                                <p><strong>Status Anak:</strong>
                                    @php
                                        $statusMap = [
                                            'none' => 'Tidak Ketiganya',
                                            'yatim' => 'Yatim (Ayah Meninggal)',
                                            'piatu' => 'Piatu (Ibu Meninggal)',
                                            'yatim_piatu' => 'Yatim & Piatu (Kedua Orang Tua Meninggal)'
                                        ];
                                    @endphp
                                    {{ $statusMap[$application->orphan_status ?? 'none'] ?? 'Tidak Kedua Orang Tua' }}
                                </p>
                                <p><strong>Pekerjaan:</strong> {{ $application->parent_job ?: '-' }}</p>
                                <p><strong>Alamat:</strong> {{ $application->parent_address }}</p>

                                <strong class="mt-3 d-block">Informasi Lainnya</strong>
                                <hr>
                                <p><strong>Kelas yang Diinginkan:</strong> {{ $application->desired_class }}</p>
                                <p><strong>Tanggal Aplikasi:</strong> {{ $application->application_date->format('d M Y') }}
                                </p>
                            </div>
                        </div>

                        @if($application->health_info && count($application->health_info) > 0)
                            <div class="mb-3">
                                <strong>Informasi Kesehatan</strong>
                                <hr>
                                <ul>
                                    @foreach($application->health_info as $health)
                                        <li>{{ $health }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if($application->disability_info && count($application->disability_info) > 0)
                            <div class="mb-3">
                                <strong>Informasi Disabilitas</strong>
                                <hr>
                                <ul>
                                    @foreach($application->disability_info as $disability)
                                        <li>{{ $disability }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if($application->education_history && count($application->education_history) > 0)
                            <div class="mb-3">
                                <strong>Riwayat Pendidikan</strong>
                                <hr>
                                @if(isset($application->education_history['previous_school']))
                                    <p><strong>Sekolah Sebelumnya:</strong> {{ $application->education_history['previous_school'] }}
                                    </p>
                                @endif
                                @if(isset($application->education_history['graduation_year']))
                                    <p><strong>Tahun Lulus:</strong> {{ $application->education_history['graduation_year'] }}</p>
                                @endif
                            </div>
                        @endif

                        @if($application->medical_info)
                            <div class="mb-3">
                                <strong>Informasi Medis</strong>
                                <hr>
                                <p>{{ $application->medical_info }}</p>
                            </div>
                        @endif

                        @if($application->documents && count($application->documents) > 0)
                            <div class="mb-3">
                                <strong>Dokumen</strong>
                                <hr>
                                <div class="row">
                                    @foreach($application->documents as $document)
                                        <div class="col-md-4 mb-2">
                                            <div class="card">
                                                <div class="card-body text-center bg-warning rounded-3">
                                                    <i class="fas fa-file fa-2x text-danger mb-2"></i>
                                                    <h6>{{ ucfirst(str_replace('_', ' ', $document['type'])) }}</h6>
                                                    <small class="text-muted">{{ $document['name'] }}</small>
                                                    <br>
                                                    <a href="{{ Storage::url($document['path']) }}"
                                                        class="btn btn-sm btn-outline-primary mt-2" target="_blank">
                                                        <i class="fas fa-download me-1"></i>Lihat
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                @if($application->status === 'pending')
                    <div class="card border-0 shadow-sm bg-warning">
                        <div class="card-header">
                            <h5 class="mb-0">Aksi</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <form method="POST" action="{{ route('admin.applications.approve', $application->id) }}"
                                        class="d-inline">
                                        @csrf
                                        <div class="mb-3">
                                            @php
                                                $availableClasses = App\Models\ClassRoom::where('is_active', true)
                                                    ->where('grade_level', $application->desired_class)
                                                    ->get();
                                            @endphp
                                            <label for="class_id" class="form-label">Pilih Kelas</label>
                                            <select name="class_id" id="class_id" class="form-control" required {{ $availableClasses->count() == 0 ? 'disabled' : '' }}>
                                                @if($availableClasses->count() > 0)
                                                    <option value="">-- Pilih Kelas --</option>
                                                    @foreach($availableClasses as $class)
                                                        <option value="{{ $class->id }}">{{ $class->name }}
                                                            ({{ $class->current_students }}/{{ $class->capacity }})</option>
                                                    @endforeach
                                                @else
                                                    <option value="" disabled>Tidak ada kelas tersedia untuk tingkat ini</option>
                                                @endif
                                            </select>
                                            @if($availableClasses->count() == 0)
                                                <div
                                                    class="form-text text-muted mt-2 d-flex align-items-center justify-content-between">
                                                    <div>Tidak ada kelas aktif yang cocok dengan tingkat tujuan
                                                        ({{ $application->desired_class }}). Silakan buat/aktifkan kelas lebih dulu.
                                                    </div>
                                                    <div><a href="{{ route('admin.classes.create') }}"
                                                            class="btn btn-sm btn-primary">Buat Kelas</a></div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="mb-3">
                                            <label for="notes" class="form-label">Catatan (Opsional)</label>
                                            <textarea name="notes" id="notes" class="form-control" rows="3"></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-success"
                                            onclick="return confirm('Yakin ingin menyetujui aplikasi ini?')" {{ $availableClasses->count() == 0 ? 'disabled' : '' }}>
                                            <i class="fas fa-check me-1"></i>Setujui Aplikasi
                                        </button>
                                    </form>
                                </div>

                                <div class="col-md-6">
                                    <form method="POST" action="{{ route('admin.applications.reject', $application->id) }}"
                                        class="d-inline">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="reject_notes" class="form-label">Alasan Penolakan</label>
                                            <textarea name="notes" id="reject_notes" class="form-control" rows="3"
                                                required></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-danger"
                                            onclick="return confirm('Yakin ingin menolak aplikasi ini?')">
                                            <i class="fas fa-times me-1"></i>Tolak Aplikasi
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    @if($application->notes)
                        <div class="alert alert-info">
                            <strong>Catatan:</strong><br>
                            {{ $application->notes }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
@endsection
