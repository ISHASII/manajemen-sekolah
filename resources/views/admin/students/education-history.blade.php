@extends('layouts.admin')

@section('title', 'Riwayat Pendidikan - ' . ($student->user->name ?? 'Siswa'))

@section('admin-content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3>Riwayat Pendidikan</h3>
                <p class="text-muted mb-0">Siswa: <strong>{{ $student->user->name ?? 'N/A' }}</strong></p>
            </div>
            <div>
                <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @foreach(['SD' => 'Sekolah Dasar', 'SMP' => 'Sekolah Menengah Pertama', 'SMA' => 'Sekolah Menengah Atas'] as $level => $levelName)
            <div class="mb-5">
                <h4 class="mb-3">
                    <i class="bi bi-building"></i> {{ $levelName }}
                </h4>

                @if(count($educationLevels[$level]) > 0)
                    <div class="row">
                        @foreach($educationLevels[$level] as $history)
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="card-title mb-0">
                                            <i class="bi bi-book"></i> {{ $history->class_name }}
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row text-center">
                                            <div class="col-6">
                                                <div class="border-end">
                                                    <small class="text-muted">Tahun Ajaran</small>
                                                    <div class="fw-bold">{{ $history->academic_year }}</div>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted">Semester</small>
                                                <div class="fw-bold">{{ $history->semester }}</div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="text-center">
                                            <small class="text-muted">Rata-rata Nilai</small>
                                            <div class="h4 text-primary mb-0">{{ number_format($history->average_grade, 2) }}</div>
                                        </div>
                                        <div class="text-center mt-2">
                                            <span class="badge bg-{{ $history->status == 'passed' ? 'success' : 'danger' }}">
                                                {{ $history->status == 'passed' ? 'Lulus' : 'Tidak Lulus' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="card-footer text-center">
                                        <button class="btn btn-outline-primary btn-sm" onclick="showGrades({{ $history->id }})">
                                            <i class="bi bi-eye"></i> Detail Nilai
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="card">
                        <div class="card-body text-center py-4">
                            <i class="bi bi-info-circle text-muted" style="font-size: 2rem;"></i>
                            <p class="text-muted mt-2">Belum ada data riwayat untuk {{ $levelName }}</p>
                        </div>
                    </div>
                @endif
            </div>
        @endforeach

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
            success: function (response) {
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
                        response.subjects_grades.forEach(function (grade) {
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
            error: function () {
                alert('Terjadi kesalahan saat mengambil data');
            }
        });
    }
</script>