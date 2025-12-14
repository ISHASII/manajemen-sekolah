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
                                                <form method="POST" action="{{ route('student.profile.update') }}" enctype="multipart/form-data">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Edit Profil Siswa</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">NISN</label>
                                                                <input type="text" name="nisn" class="form-control" value="{{ old('nisn', $student->nisn) }}">
                                                                @error('nisn')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">Email</label>
                                                                <input type="email" name="email" class="form-control" value="{{ old('email', $student->user->email) }}">
                                                                @error('email')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">Password Baru (opsional)</label>
                                                                <input type="password" name="password" class="form-control" autocomplete="new-password">
                                                                @error('password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">Konfirmasi Password</label>
                                                                <input type="password" name="password_confirmation" class="form-control" autocomplete="new-password">
                                                                @error('password_confirmation')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">Tempat Lahir</label>
                                                                <input type="text" name="place_of_birth" class="form-control" value="{{ old('place_of_birth', $student->place_of_birth) }}">
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">Tanggal Lahir</label>
                                                                <input type="date" name="birth_date" class="form-control" value="{{ old('birth_date', optional($student->birth_date)->format('Y-m-d')) }}">
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">Agama</label>
                                                                <select name="religion" class="form-select">
                                                                    <option value="">Pilih Agama</option>
                                                                    <option value="islam" {{ old('religion', $student->religion) === 'islam' ? 'selected' : '' }}>Islam</option>
                                                                    <option value="kristen" {{ old('religion', $student->religion) === 'kristen' ? 'selected' : '' }}>Kristen</option>
                                                                    <option value="katolik" {{ old('religion', $student->religion) === 'katolik' ? 'selected' : '' }}>Katolik</option>
                                                                    <option value="hindu" {{ old('religion', $student->religion) === 'hindu' ? 'selected' : '' }}>Hindu</option>
                                                                    <option value="budha" {{ old('religion', $student->religion) === 'budha' ? 'selected' : '' }}>Budha</option>
                                                                    <option value="khonghucu" {{ old('religion', $student->religion) === 'khonghucu' ? 'selected' : '' }}>Khonghucu</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-12 mb-3">
                                                                <label class="form-label">Alamat</label>
                                                                <textarea name="address" class="form-control">{{ old('address', $student->address) }}</textarea>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">Nama Orang Tua / Wali</label>
                                                                <input type="text" name="parent_name" class="form-control" value="{{ old('parent_name', $student->parent_name) }}">
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">Telepon Orang Tua</label>
                                                                <input type="text" name="parent_phone" class="form-control" value="{{ old('parent_phone', $student->parent_phone) }}">
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">Telepon</label>
                                                                <input type="text" name="phone" class="form-control" value="{{ old('phone', $student->user->phone) }}">
                                                            </div>
                                                            @if($student->classRoom && $student->classRoom->grade_level === 'kejuruan')
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">Minat Kerja</label>
                                                                <input type="text" name="job_interest" class="form-control" value="{{ old('job_interest', $student->job_interest) }}">
                                                            </div>
                                                            <div class="col-md-12 mb-3">
                                                                <label class="form-label">CV / LinkedIn (URL)</label>
                                                                <input type="text" name="cv_link" class="form-control" value="{{ old('cv_link', $student->cv_link) }}" placeholder="https://linkedin.com/in/your-profile atau link CV">
                                                            </div>
                                                            <div class="col-md-12 mb-3">
                                                                <label class="form-label">Portofolio (link, pisah koma)</label>
                                                                <textarea name="portfolio_links" class="form-control">{{ old('portfolio_links', is_array($student->portfolio_links) ? implode(', ', $student->portfolio_links) : ($student->portfolio_links ?? '') ) }}</textarea>
                                                            </div>
                                                            @endif
                                                            <div class="col-md-12 mb-3">
                                                                <label class="form-label">Alamat Orang Tua</label>
                                                                <textarea name="parent_address" class="form-control">{{ old('parent_address', $student->parent_address) }}</textarea>
                                                            </div>
                                                            <div class="col-md-12 mb-3">
                                                                <label class="form-label">Pekerjaan Orang Tua</label>
                                                                <input type="text" name="parent_job" class="form-control" value="{{ old('parent_job', $student->parent_job) }}">
                                                            </div>
                                                            <div class="col-md-12 mb-3">
                                                                <label class="form-label">Foto Profil (opsional)</label>
                                                                <input type="file" name="profile_photo" class="form-control">
                                                            </div>
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
                                </div>
                                <h5>{{ $student->user->name }}</h5>
                                <p class="text-muted">{{ $student->student_id }}</p>
                                @if($student->classRoom)
                                    <span class="badge bg-primary">{{ $student->classRoom->name }}</span>
                                @endif
                                <div class="mt-2 d-flex gap-2 justify-content-center">
                                    <button class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#idCardModal">
                                        <i class="fas fa-id-card"></i> Generate ID Card
                                    </button>
                                    @php $routePrefix = auth()->user()->role === 'kejuruan' ? 'kejuruan' : 'student'; @endphp
                                    <a href="{{ route($routePrefix . '.attendance') }}" class="btn btn-sm btn-primary">
                                        <i class="bi bi-list-check me-1"></i> Rekap Absensi
                                    </a>
                                </div>
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
                                @if($student->job_interest)
                                    <p><i class="fas fa-briefcase text-primary me-2"></i>{{ $student->job_interest }}</p>
                                @endif
                                @if($student->cv_link)
                                    <p><i class="fas fa-link text-primary me-2"></i><a href="{{ $student->cv_link }}" target="_blank">CV / LinkedIn</a></p>
                                @endif
                                @if($student->portfolio_links && is_array($student->portfolio_links) && count($student->portfolio_links) > 0)
                                    <p class="mt-1"><strong>Portofolio:</strong></p>
                                    <ul class="mb-0">
                                        @foreach($student->portfolio_links as $pl)
                                            <li><a href="{{ $pl }}" target="_blank">{{ $pl }}</a></li>
                                        @endforeach
                                    </ul>
                                @endif
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
                                    <p>{{ $student->user->birth_date ? \Carbon\Carbon::parse($student->user->birth_date)->translatedFormat('d M Y') : '-' }}
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

                                @push('scripts')
                                <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
                                <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
                                <script>
                                    document.addEventListener('DOMContentLoaded', function () {
                                        const downloadBtn = document.getElementById('downloadIdCardBtn');
                                        const printBtn = document.getElementById('printIdCardBtn');
                                        const targetEl = document.getElementById('studentIdCard');
                                        if (downloadBtn) {
                                            downloadBtn.addEventListener('click', function () {
                                                html2canvas(targetEl).then(function (canvas) {
                                                    const dataUrl = canvas.toDataURL('image/png');
                                                    const link = document.createElement('a');
                                                    link.href = dataUrl;
                                                    link.download = 'student-id-{{ $student->student_id }}.png';
                                                    document.body.appendChild(link);
                                                    link.click();
                                                    document.body.removeChild(link);
                                                });
                                            });
                                        }
                                        if (printBtn) {
                                            printBtn.addEventListener('click', function () {
                                                html2canvas(targetEl).then(function (canvas) {
                                                    const dataUrl = canvas.toDataURL('image/png');
                                                    const w = window.open('', '_blank');
                                                    w.document.write('<html><head><title>ID Card</title></head><body style="margin:0; padding:20px; display:flex; justify-content:center;">');
                                                    w.document.write('<img src="' + dataUrl + '" style="max-width:100%;">');
                                                    w.document.write('</body></html>');
                                                    w.document.close();
                                                    w.focus();
                                                    setTimeout(function(){ w.print(); w.close(); }, 500);
                                                });
                                            });
                                        }

                                        // Generate QR code when modal opens using QRCode.js library
                                        const idCardModal = document.getElementById('idCardModal');
                                        const qrContainer = document.getElementById('studentCardQr');
                                        if (idCardModal && qrContainer) {
                                            idCardModal.addEventListener('shown.bs.modal', function () {
                                                // Clear previous QR code
                                                qrContainer.innerHTML = '';
                                                // Generate new QR code
                                                const publicUrl = '{{ url('/students/public/'.$student->id) }}';
                                                new QRCode(qrContainer, {
                                                    text: publicUrl,
                                                    width: 100,
                                                    height: 100,
                                                    colorDark: '#000000',
                                                    colorLight: '#ffffff',
                                                    correctLevel: QRCode.CorrectLevel.H
                                                });
                                                console.log('QR code generated for:', publicUrl);
                                            });
                                        }
                                    });
                                </script>
                                @endpush
                        </div>
                    </div>

                    @if(isset($application) && $application)
                        <div class="card border-0 shadow-sm mt-3">
                            <div class="card-header bg-warning text-white">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h5 class="mb-0">Aplikasi Pendaftaran</h5>
                                        <small>{{ $application->application_number ?? '' }}</small>
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
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <strong>Informasi Aplikasi</strong>
                                        <hr>
                                        <p><strong>Nama:</strong> {{ $application->student_name }}</p>
                                        <p><strong>Email:</strong> {{ $application->email }}</p>
                                        <p><strong>Telepon:</strong> {{ $application->phone }}</p>
                                        <p><strong>NISN:</strong> {{ $application->nisn ?: '-' }}</p>
                                        <p><strong>Tempat Lahir:</strong> {{ $application->place_of_birth }}</p>
                                        <p><strong>Tanggal Lahir:</strong> {{ \Carbon\Carbon::parse($application->birth_date)->translatedFormat('d M Y') }}</p>
                                        <p><strong>Jenis Kelamin:</strong> {{ $application->gender === 'male' ? 'Laki-laki' : 'Perempuan' }}</p>
                                        <p><strong>Agama:</strong> {{ ucfirst($application->religion) }}</p>
                                        <p><strong>Alamat:</strong> {{ $application->address }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Informasi Orang Tua</strong>
                                        <hr>
                                        <p><strong>Nama:</strong> {{ $application->parent_name }}</p>
                                        <p><strong>Telepon:</strong> {{ $application->parent_phone }}</p>
                                        <p><strong>Pekerjaan:</strong> {{ $application->parent_job ?: '-' }}</p>
                                        <p><strong>Alamat:</strong> {{ $application->parent_address }}</p>
                                        <p><strong>Kelas yang Diinginkan:</strong> {{ $application->desired_class }}</p>
                                        <p><strong>Tanggal Aplikasi:</strong> {{ $application->application_date ? $application->application_date->translatedFormat('d M Y') : '-' }}</p>
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
                                            <p><strong>Sekolah Sebelumnya:</strong> {{ $application->education_history['previous_school'] }}</p>
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
                            </div>
                        </div>
                    @endif

                    <!-- ID Card Modal -->
                    <div class="modal fade" id="idCardModal" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">ID Card Siswa</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body d-flex justify-content-center">
                                    <div id="studentIdCard" style="width:360px; padding:16px; border-radius:8px; background:#fff; box-shadow:0 2px 8px rgba(0,0,0,0.08); font-family:Arial, sans-serif; color:#111;">
                                        <div style="display:flex; align-items:center; gap:12px;">
                                            <div style="width:80px; height:80px; border-radius:8px; overflow:hidden; background:#e9ecef; display:flex; align-items:center; justify-content:center;">
                                                @if($student->user->profile_photo)
                                                    <img src="{{ Storage::url($student->user->profile_photo) }}" style="width:80px; height:80px; object-fit:cover;">
                                                @else
                                                    <i class="fas fa-user fa-2x text-dark"></i>
                                                @endif
                                            </div>
                                            <div style="flex:1;">
                                                <div style="font-weight:700; font-size:18px;">{{ $student->user->name }}</div>
                                                <div style="font-size:12px; color:#6b7280;">ID: {{ $student->student_id ?: '-' }}</div>
                                            </div>
                                            <div style="width:110px; text-align:center;">
                                                <div id="studentCardQr" style="width:100px; height:100px;"></div>
                                            </div>
                                        </div>
                                        <hr style="margin:12px 0; border-color:#e5e7eb;">
                                        <div style="margin-top:12px; font-size:13px; color:#374151;">
                                            <div style="margin-bottom:6px;"><strong>ID:</strong> {{ $student->student_id ?: '-' }}</div>
                                            <div style="margin-bottom:6px;"><strong>Nama:</strong> {{ $student->user->name }}</div>
                                            <div style="margin-bottom:6px;"><strong>Alamat:</strong> {{ $student->address ? $student->address : ($student->user->address ?? '-') }}</div>
                                            <div style="margin-bottom:6px;"><strong>No Telepon:</strong> {{ $student->user->phone ?: '-' }}</div>
                                            <div style="margin-bottom:6px;"><strong>Jenis Kelamin:</strong> {{ $student->user->gender === 'male' ? 'Laki-laki' : ($student->user->gender === 'female' ? 'Perempuan' : '-') }}</div>
                                            <div style="margin-bottom:6px;"><strong>Disabilitas:</strong> {{ ($student->disability_info && is_array($student->disability_info) && count($student->disability_info)) ? implode(', ', $student->disability_info) : 'Tidak ada' }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-secondary" id="downloadIdCardBtn">Download PNG</button>
                                    <button type="button" class="btn btn-outline-secondary" id="printIdCardBtn">Print</button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
                                                <button type="button" class="btn btn-outline-danger" onclick="removeInterest(this)" aria-label="Hapus minat">
                                                    <span class="fw-bold text-dark" aria-hidden="true">×</span>
                                                </button>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="input-group mb-2">
                                            <input type="text" name="interests_talents[]" class="form-control"
                                                placeholder="Minat/Bakat">
                                            <button type="button" class="btn btn-outline-danger" onclick="removeInterest(this)" aria-label="Hapus minat">
                                                    <span class="fw-bold text-dark" aria-hidden="true">×</span>
                                                </button>
                                        </div>
                                    @endif
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addInterest()">
                                    <span class="fw-bold">+</span> Tambah
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
                                                                        <button type="button" class="btn btn-outline-danger" onclick="removeInterest(this)" aria-label="Hapus minat">
                                                                            <span class="fw-bold text-dark" aria-hidden="true">×</span>
                                                                        </button>
                                                                    `;
                container.appendChild(div);
            }

            function removeInterest(button) {
                button.parentElement.remove();
            }
        </script>
    @endif

    @if($student && $student->classRoom && $student->classRoom->grade_level === 'kejuruan')
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Portofolio</h5>
                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addPortfolioModal">Tambah Portofolio</button>
            </div>
            <div class="card-body">
                @if(isset($portfolios) && $portfolios->count() > 0)
                    <div class="row">
                        @foreach($portfolios as $pf)
                            <div class="col-md-4 mb-3">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h6>{{ $pf->title }}</h6>
                                        <p class="text-muted small">{{ Str::limit($pf->description, 120) }}</p>
                                        @if($pf->link)
                                            <a href="{{ $pf->link }}" target="_blank" class="btn btn-sm btn-outline-primary">Lihat</a>
                                        @endif
                                        @if($pf->file_path)
                                            <a href="{{ Storage::url($pf->file_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary">File</a>
                                        @endif
                                        <form method="POST" action="{{ route('student.portfolio.destroy', $pf->id) }}" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus portofolio ini?')">Hapus</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-muted">Belum ada portofolio. Tambahkan portofolio untuk melengkapi profil kejuruan Anda.</div>
                @endif
            </div>
        </div>
        <!-- Add Portfolio Modal -->
        <div class="modal fade" id="addPortfolioModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" action="{{ route('student.portfolio.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Tambah Portofolio</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Judul</label>
                                <input type="text" name="title" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Deskripsi</label>
                                <textarea name="description" class="form-control"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Link (opsional)</label>
                                <input type="url" name="link" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">File (opsional)</label>
                                <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png" class="form-control">
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
