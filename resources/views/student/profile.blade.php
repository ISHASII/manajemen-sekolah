@extends('layouts.app')

@section('title', 'Profil Siswa')

@section('content')
    <div class="student-page-wrapper">
        <div class="container py-4">
            @if(!$student)
                <div class="alert alert-info">Profil siswa belum dibuat. <a href="{{ route('student.profile.create') }}">Buat
                        Profil</a></div>
            @endif
            @if($student)
                <div class="row">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center">
                                <div class="mb-3">
                                    @if($student->user->profile_photo)
                                        <img src="{{ Storage::url($student->user->profile_photo) }}" class="rounded-circle"
                                            width="100" height="100">
                                    @else
                                        <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center"
                                            style="width: 100px; height: 100px;">
                                            <i class="fas fa-user fa-2x text-white"></i>
                                        </div>
                                    @endif

                                    <!-- Update Profile Modal -->
                                    <div class="modal fade" id="updateProfileModal" tabindex="-1">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <form method="POST" action="{{ route('student.profile.update') }}"
                                                    enctype="multipart/form-data">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Edit Profil Siswa</h5>
                                                        <button type="button" class="btn-close"
                                                            data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">NISN</label>
                                                                <input type="text" name="nisn" class="form-control"
                                                                    value="{{ old('nisn', $student->nisn) }}">
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">Email</label>
                                                                <input type="email" name="email" class="form-control"
                                                                    value="{{ old('email', $student->user->email) }}">
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">Tempat Lahir</label>
                                                                <input type="text" name="place_of_birth" class="form-control"
                                                                    value="{{ old('place_of_birth', $student->place_of_birth) }}">
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">Tanggal Lahir</label>
                                                                <input type="date" name="birth_date" class="form-control"
                                                                    value="{{ old('birth_date', optional($student->birth_date)->format('Y-m-d')) }}">
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">Agama</label>
                                                                <select name="religion" class="form-select">
                                                                    <option value="">Pilih Agama</option>
                                                                    <option value="islam" {{ old('religion', $student->religion) === 'islam' ? 'selected' : '' }}>Islam
                                                                    </option>
                                                                    <option value="kristen" {{ old('religion', $student->religion) === 'kristen' ? 'selected' : '' }}>
                                                                        Kristen</option>
                                                                    <option value="katolik" {{ old('religion', $student->religion) === 'katolik' ? 'selected' : '' }}>
                                                                        Katolik</option>
                                                                    <option value="hindu" {{ old('religion', $student->religion) === 'hindu' ? 'selected' : '' }}>Hindu
                                                                    </option>
                                                                    <option value="budha" {{ old('religion', $student->religion) === 'budha' ? 'selected' : '' }}>Budha
                                                                    </option>
                                                                    <option value="khonghucu" {{ old('religion', $student->religion) === 'khonghucu' ? 'selected' : '' }}>
                                                                        Khonghucu</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-12 mb-3">
                                                                <label class="form-label">Alamat</label>
                                                                <textarea name="address"
                                                                    class="form-control">{{ old('address', $student->address) }}</textarea>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">Nama Orang Tua / Wali</label>
                                                                <input type="text" name="parent_name" class="form-control"
                                                                    value="{{ old('parent_name', $student->parent_name) }}">
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">Telepon Orang Tua</label>
                                                                <input type="text" name="parent_phone" class="form-control"
                                                                    value="{{ old('parent_phone', $student->parent_phone) }}">
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">Telepon</label>
                                                                <input type="text" name="phone" class="form-control"
                                                                    value="{{ old('phone', $student->user->phone) }}">
                                                            </div>
                                                            <div class="col-md-12 mb-3">
                                                                <label class="form-label">Alamat Orang Tua</label>
                                                                <textarea name="parent_address"
                                                                    class="form-control">{{ old('parent_address', $student->parent_address) }}</textarea>
                                                            </div>
                                                            <div class="col-md-12 mb-3">
                                                                <label class="form-label">Pekerjaan Orang Tua</label>
                                                                <input type="text" name="parent_job" class="form-control"
                                                                    value="{{ old('parent_job', $student->parent_job) }}">
                                                            </div>
                                                            <div class="col-md-12 mb-3">
                                                                <label class="form-label">Foto Profil (opsional)</label>
                                                                <input type="file" name="profile_photo" class="form-control">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <h5>{{ $student->user->name }}</h5>
                                <p class="text-muted">{{ $student->student_id }}</p>
                                @if($student->classRoom)
                                    <span class="badge bg-primary">{{ $student->classRoom->name }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm mt-3">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">Informasi Kontak</h6>
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                        data-bs-target="#updateProfileModal">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <p><i class="fas fa-envelope text-primary me-2"></i>{{ $student->user->email }}</p>
                                <p><i class="fas fa-phone text-primary me-2"></i>{{ $student->user->phone }}</p>
                                <p><i class="fas fa-map-marker-alt text-primary me-2"></i>{{ $student->user->address }}</p>
                            </div>
                        </div>
                    </div>
            @endif

                <div class="col-md-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Data Pribadi</h5>
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                    data-bs-target="#updateProfileModal">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">NISN</label>
                                    <p>{{ $student->nisn ?: '-' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Tempat Lahir</label>
                                    <p>{{ $student->place_of_birth }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Tanggal Lahir</label>
                                    <p>{{ $student->user->birth_date ? $student->user->birth_date->format('d M Y') : '-' }}
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Jenis Kelamin</label>
                                    <p>{{ $student->user->gender === 'male' ? 'Laki-laki' : 'Perempuan' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Agama</label>
                                    <p>{{ ucfirst($student->religion) }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Status</label>
                                    <p>
                                        @if($student->status === 'active')
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($student->status) }}</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm mt-3">
                        <div class="card-header">
                            <h5 class="mb-0">Data Orang Tua/Wali</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Nama</label>
                                    <p>{{ $student->parent_name }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Telepon</label>
                                    <p>{{ $student->parent_phone }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Pekerjaan</label>
                                    <p>{{ $student->parent_job ?: '-' }}</p>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label fw-bold">Alamat</label>
                                    <p>{{ $student->parent_address }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm mt-3">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Minat & Bakat</h5>
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                    data-bs-target="#updateInterestsModal">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($student->interests_talents && count($student->interests_talents) > 0)
                                <div class="row">
                                    @foreach($student->interests_talents as $interest)
                                        <div class="col-md-6 mb-2">
                                            <span class="badge bg-light text-dark border">{{ $interest }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted">Belum ada data minat dan bakat</p>
                            @endif
                        </div>
                    </div>

                    @if(count($documents) > 0)
                        <div class="card border-0 shadow-sm mt-3">
                            <div class="card-header">
                                <h5 class="mb-0">Dokumen</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach($documents as $document)
                                        <div class="col-md-4 mb-3">
                                            <div class="card border">
                                                <div class="card-body text-center">
                                                    <i class="fas fa-file fa-2x text-primary mb-2"></i>
                                                    <h6>{{ ucfirst(str_replace('_', ' ', $document->document_type)) }}</h6>
                                                    <small class="text-muted">{{ $document->document_name }}</small>
                                                    <br>
                                                    <a href="{{ Storage::url($document->file_path) }}"
                                                        class="btn btn-sm btn-outline-primary mt-2" target="_blank">
                                                        <i class="fas fa-eye"></i> Lihat
                                                    </a>
                                                    @if($document->is_verified)
                                                        <span class="badge bg-success d-block mt-1">Terverifikasi</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($student)
        <!-- Update Interests Modal -->
        <div class="modal fade" id="updateInterestsModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" action="{{ route('student.profile.update') }}">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">Update Minat & Bakat</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="interests_talents" class="form-label">Minat & Bakat</label>
                                <div id="interests-container">
                                    @if($student->interests_talents && count($student->interests_talents) > 0)
                                        @foreach($student->interests_talents as $index => $interest)
                                            <div class="input-group mb-2">
                                                <input type="text" name="interests_talents[]" class="form-control"
                                                    value="{{ $interest }}" placeholder="Minat/Bakat">
                                                <button type="button" class="btn btn-outline-danger" onclick="removeInterest(this)">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="input-group mb-2">
                                            <input type="text" name="interests_talents[]" class="form-control"
                                                placeholder="Minat/Bakat">
                                            <button type="button" class="btn btn-outline-danger" onclick="removeInterest(this)">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addInterest()">
                                    <i class="fas fa-plus"></i> Tambah
                                </button>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            function addInterest() {
                const container = document.getElementById('interests-container');
                const div = document.createElement('div');
                div.className = 'input-group mb-2';
                div.innerHTML = `
                                                                        <input type="text" name="interests_talents[]" class="form-control" placeholder="Minat/Bakat">
                                                                        <button type="button" class="btn btn-outline-danger" onclick="removeInterest(this)">
                                                                            <i class="fas fa-minus"></i>
                                                                        </button>
                                                                    `;
                container.appendChild(div);
            }

            function removeInterest(button) {
                button.parentElement.remove();
            }
        </script>
    @endif
@endsection

@push('styles')
    <style>
        .student-page-wrapper {
            background-color: #ffffff !important;
            min-height: 100vh;
            padding-top: 1rem;
            padding-bottom: 1rem;
        }
    </style>
@endpush