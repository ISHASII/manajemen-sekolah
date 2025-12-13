@extends('layouts.admin')

@section('title', 'Edit Siswa')

@section('admin-content')
    <div class="container py-4">
        <h3>Edit Siswa</h3>
        <div class="card mt-3 p-4">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if($errors && $errors->any())
                <div class="alert alert-warning">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('admin.students.update', $student->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="return_to" value="{{ request()->query('return_to') }}">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $student->user->name) }}"
                            required>
                        @error('name') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control"
                            value="{{ old('email', $student->user->email) }}" required>
                        @error('email') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Student ID</label>
                        <input type="text" name="student_id" class="form-control"
                            value="{{ old('student_id', $student->student_id) }}" required>
                        @error('student_id') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">NISN</label>
                        <input type="text" name="nisn" class="form-control" value="{{ old('nisn', $student->nisn) }}">
                        @error('nisn') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Kelas</label>
                        <select name="class_id" class="form-select">
                            @php $classesList = $classes ?? collect(); @endphp
                            @if($classesList->count() > 0)
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($classesList as $c)
                                    <option value="{{ $c->id }}" {{ old('class_id', $student->class_id) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                @endforeach
                            @else
                                <option value="" disabled>Tidak ada kelas tersedia</option>
                            @endif
                        </select>
                        @error('class_id') <div class="text-danger small">{{ $message }}</div> @enderror
                        @if($classesList->count() == 0)
                            <div class="form-text text-muted d-flex align-items-center justify-content-between">
                                <div>Belum ada kelas tersedia. Anda dapat memperbarui siswa tanpa memilih kelas.</div>
                                <div><a href="{{ route('admin.classes.create') }}" class="btn btn-sm btn-primary">Buat Kelas</a>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tempat Lahir</label>
                        <input type="text" name="place_of_birth" class="form-control"
                            value="{{ old('place_of_birth', $student->place_of_birth) }}" required>
                        @error('place_of_birth') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Lahir</label>
                        <input type="date" name="birth_date" class="form-control"
                            value="{{ old('birth_date', $student->birth_date?->toDateString()) }}" required>
                        @error('birth_date') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Agama</label>
                        <select name="religion" class="form-select" required>
                            <option value="">-- Pilih --</option>
                            <option value="islam" {{ old('religion', $student->religion) == 'islam' ? 'selected' : '' }}>Islam
                            </option>
                            <option value="kristen" {{ old('religion', $student->religion) == 'kristen' ? 'selected' : '' }}>
                                Kristen</option>
                            <option value="katolik" {{ old('religion', $student->religion) == 'katolik' ? 'selected' : '' }}>
                                Katolik</option>
                            <option value="hindu" {{ old('religion', $student->religion) == 'hindu' ? 'selected' : '' }}>Hindu
                            </option>
                            <option value="budha" {{ old('religion', $student->religion) == 'budha' ? 'selected' : '' }}>Budha
                            </option>
                            <option value="khonghucu" {{ old('religion', $student->religion) == 'khonghucu' ? 'selected' : '' }}>Khonghucu</option>
                        </select>
                        @error('religion') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Alamat</label>
                        <input type="text" name="address" class="form-control"
                            value="{{ old('address', $student->address) }}" required>
                        @error('address') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nama Orang Tua</label>
                        <input type="text" name="parent_name" class="form-control"
                            value="{{ old('parent_name', $student->parent_name) }}" required>
                        @error('parent_name') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Telepon Orang Tua</label>
                        <input type="text" name="parent_phone" class="form-control"
                            value="{{ old('parent_phone', $student->parent_phone) }}" required>
                        @error('parent_phone') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Alamat Orang Tua</label>
                        <input type="text" name="parent_address" class="form-control"
                            value="{{ old('parent_address', $student->parent_address) }}" required>
                        @error('parent_address') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Masuk</label>
                        <input type="date" name="enrollment_date" class="form-control"
                            value="{{ old('enrollment_date', $student->enrollment_date?->toDateString()) }}" required>
                        @error('enrollment_date') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-12">
                        <hr>
                        <h5>Dokumen Siswa</h5>
                    </div>
                    @php
                        $docMap = [];
                        foreach($student->documents as $d) {
                            $docMap[$d->document_type] = $d;
                        }
                    @endphp
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Akta Kelahiran (PDF/JPG/PNG)</label>
                        @if(isset($docMap['birth_certificate']))
                            <div class="mb-2">
                                <a href="{{ Storage::url($docMap['birth_certificate']->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-download me-1"></i>Lihat Akta Kelahiran
                                </a>
                            </div>
                        @endif
                        <input type="file" name="birth_certificate" class="form-control">
                        @error('birth_certificate') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Kartu Keluarga (KK) (PDF/JPG/PNG)</label>
                        @if(isset($docMap['kk']))
                            <div class="mb-2">
                                <a href="{{ Storage::url($docMap['kk']->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-download me-1"></i>Lihat KK
                                </a>
                            </div>
                        @endif
                        <input type="file" name="kk" class="form-control">
                        @error('kk') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Ijazah/Surat Keterangan Lulus Terakhir (PDF/JPG/PNG)</label>
                        @if(isset($docMap['last_certificate']))
                            <div class="mb-2">
                                <a href="{{ Storage::url($docMap['last_certificate']->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-download me-1"></i>Lihat Ijazah Terakhir
                                </a>
                            </div>
                        @endif
                        <input type="file" name="last_certificate" class="form-control">
                        @error('last_certificate') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Pas Foto 3x4 (JPG/PNG)</label>
                        @if(isset($docMap['photo']))
                            <div class="mb-2">
                                <a href="{{ Storage::url($docMap['photo']->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-download me-1"></i>Lihat Foto
                                </a>
                            </div>
                        @endif
                        <input type="file" name="photo" class="form-control">
                        @error('photo') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Sertifikat Medis (PDF/JPG/PNG)</label>
                        @if(isset($docMap['medical_certificate']))
                            <div class="mb-2">
                                <a href="{{ Storage::url($docMap['medical_certificate']->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-download me-1"></i>Lihat Sertifikat Medis
                                </a>
                            </div>
                        @endif
                        <input type="file" name="medical_certificate" class="form-control">
                        @error('medical_certificate') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                </div>
                <hr class="my-3" />
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nomor Telepon Siswa</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $student->user->phone) }}">
                        @error('phone') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Jenis Kelamin</label>
                        <select name="gender" class="form-select">
                            <option value="">-- Pilih --</option>
                            <option value="male" {{ old('gender', $student->user->gender) == 'male' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="female" {{ old('gender', $student->user->gender) == 'female' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('gender') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email Orang Tua</label>
                        <input type="email" name="parent_email" class="form-control" value="{{ old('parent_email', $student->parent_email) }}">
                        @error('parent_email') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Minat Kerja</label>
                        <input type="text" name="job_interest" class="form-control" value="{{ old('job_interest', $student->job_interest) }}">
                        @error('job_interest') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Pekerjaan Orang Tua</label>
                        <input type="text" name="parent_job" class="form-control" value="{{ old('parent_job', $student->parent_job) }}">
                        @error('parent_job') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Informasi Medis</label>
                        <textarea name="medical_info" rows="3" class="form-control">{{ old('medical_info', $student->medical_info) }}</textarea>
                        @error('medical_info') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Kondisi Kesehatan</label>
                        @php
                            $healthOpts = ['Alergi','Asma','Penyakit Jantung','Epilepsi','Lainnya'];
                            $selectedHealth = old('health_info', $student->health_info ?? []);
                        @endphp
                        <div class="row">
                            @foreach($healthOpts as $k => $label)
                                <div class="col-md-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="health_info[]" value="{{ $label }}" id="health_{{ $k }}"
                                            {{ in_array($label, (array) $selectedHealth) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="health_{{ $k }}">{{ $label }}</label>
                                    </div>
                                </div>
                            @endforeach
                            <div class="col-md-4 mt-2">
                                <input type="text" name="health_info_other" class="form-control mt-2" placeholder="Jika 'Lainnya', sebutkan..." value="{{ old('health_info_other') }}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Disabilitas</label>
                        @php
                            $disabilityOpts = ['Gangguan Penglihatan','Gangguan Pendengaran','Disabilitas Fisik','Disabilitas Intelektual','Lainnya'];
                            $selectedDis = old('disability_info', $student->disability_info ?? []);
                        @endphp
                        <div class="row">
                            @foreach($disabilityOpts as $k => $label)
                                <div class="col-md-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="disability_info[]" value="{{ $label }}" id="disability_{{ $k }}"
                                            {{ in_array($label, (array) $selectedDis) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="disability_{{ $k }}">{{ $label }}</label>
                                    </div>
                                </div>
                            @endforeach
                            <div class="col-md-4 mt-2">
                                <input type="text" name="disability_info_other" class="form-control mt-2" placeholder="Jika 'Lainnya', sebutkan..." value="{{ old('disability_info_other') }}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Sekolah Sebelumnya</label>
                        <input type="text" name="education_history[previous_school]" class="form-control" value="{{ old('education_history.previous_school', $student->education_history['previous_school'] ?? null) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tahun Lulus</label>
                        <input type="number" name="education_history[graduation_year]" class="form-control" value="{{ old('education_history.graduation_year', $student->education_history['graduation_year'] ?? null) }}">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Status Anak (Yatim/Piatu)</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="orphan_status" id="orphan_none" value="none" {{ old('orphan_status', $student->orphan_status ?? 'none') == 'none' ? 'checked' : '' }}>
                            <label class="form-check-label" for="orphan_none">Tidak Kedua Orang Tua</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="orphan_status" id="orphan_yatim" value="yatim" {{ old('orphan_status', $student->orphan_status) == 'yatim' ? 'checked' : '' }}>
                            <label class="form-check-label" for="orphan_yatim">Yatim (Ayah Meninggal)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="orphan_status" id="orphan_piatu" value="piatu" {{ old('orphan_status', $student->orphan_status) == 'piatu' ? 'checked' : '' }}>
                            <label class="form-check-label" for="orphan_piatu">Piatu (Ibu Meninggal)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="orphan_status" id="orphan_both" value="yatim_piatu" {{ old('orphan_status', $student->orphan_status) == 'yatim_piatu' ? 'checked' : '' }}>
                            <label class="form-check-label" for="orphan_both">Yatim & Piatu (Kedua Orang Tua Meninggal)</label>
                        </div>
                    </div>
                </div>
                <div class="mt-4 text-end">
                    @php
                        $cancelRoute = route('admin.students.index');
                        if (request()->query('return_to') === 'kejuruan') {
                            $cancelRoute = route('admin.students.kejuruan');
                        }
                    @endphp
                    <a href="{{ $cancelRoute }}" class="btn btn-outline-secondary">Batal</a>
                    <button class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>

        <!-- Riwayat Kelas -->
        <div class="card mt-4 p-4">
            <h5>Riwayat Kelas</h5>
            @if($student->gradeHistory->count() > 0)
                <div class="table-responsive mt-3">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Kelas</th>
                                <th>Tahun Ajaran</th>
                                <th>Semester</th>
                                <th>Rata-rata Nilai</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($student->gradeHistory as $history)
                                <tr>
                                    <td>{{ $history->class_name }}</td>
                                    <td>{{ $history->academic_year }}</td>
                                    <td>{{ $history->semester }}</td>
                                    <td>{{ number_format($history->average_grade, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $history->status == 'passed' ? 'success' : 'danger' }}">
                                            {{ $history->status == 'passed' ? 'Lulus' : 'Tidak Lulus' }}
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-info" onclick="showGrades({{ $history->id }})">
                                            Lihat Nilai
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted mt-3">Belum ada riwayat kelas untuk siswa ini.</p>
            @endif
        </div>

        <!-- Modal untuk menampilkan detail nilai -->
        <div class="modal fade" id="gradesModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Detail Nilai Mata Pelajaran</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div id="gradesContent">
                            <!-- Content will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<script>
function showGrades(historyId) {
    $.ajax({
        url: '/admin/students/grade-history/' + historyId,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                let content = `
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Kelas:</strong> ${response.class_name}
                        </div>
                        <div class="col-md-6">
                            <strong>Tahun Ajaran:</strong> ${response.academic_year}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Semester:</strong> ${response.semester}
                        </div>
                        <div class="col-md-6">
                            <strong>Rata-rata Nilai:</strong> ${parseFloat(response.average_grade).toFixed(2)}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Status:</strong>
                            <span class="badge bg-${response.status == 'passed' ? 'success' : 'danger'}">
                                ${response.status == 'passed' ? 'Lulus' : 'Tidak Lulus'}
                            </span>
                        </div>
                    </div>`;

                if (response.notes) {
                    content += `<div class="row mb-3">
                        <div class="col-12">
                            <strong>Catatan:</strong> ${response.notes}
                        </div>
                    </div>`;
                }

                content += `<h6 class="mt-4">Nilai Mata Pelajaran:</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>Mata Pelajaran</th>
                                    <th>Nilai</th>
                                    <th>Catatan</th>
                                </tr>
                            </thead>
                            <tbody>`;

                if (response.subjects_grades && response.subjects_grades.length > 0) {
                    response.subjects_grades.forEach(function(grade) {
                        content += `
                            <tr>
                                <td>${grade.subject || '-'}</td>
                                <td>${grade.score || '-'}</td>
                                <td>${grade.notes || '-'}</td>
                            </tr>`;
                    });
                } else {
                    content += `<tr><td colspan="3" class="text-center">Tidak ada data nilai</td></tr>`;
                }

                content += `</tbody></table></div>`;

                $('#gradesContent').html(content);
                $('#gradesModal').modal('show');
            } else {
                alert('Gagal mengambil data nilai');
            }
        },
        error: function() {
            alert('Terjadi kesalahan saat mengambil data');
        }
    });
}
</script>
