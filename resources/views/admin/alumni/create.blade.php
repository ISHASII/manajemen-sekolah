@extends('layouts.admin')

@section('title', 'Tambah Alumni')

@section('admin-content')
    <div class="container py-4">
        <h3>Tambah Alumni</h3>
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
            <form action="{{ route('admin.alumni.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Siswa</label>
                    @if(isset($students) && count($students) > 0)
                        <select name="student_id" class="form-select" id="student_select" required>
                            <option value="">Pilih Siswa</option>
                            @foreach($students as $s)
                                <option value="{{ $s->id }}" {{ old('student_id') == $s->id ? 'selected' : '' }}>
                                    {{ optional($s->user)->name ?? 'ID-' . $s->id }}
                                </option>
                            @endforeach
                        </select>
                    @else
                        <div class="alert alert-warning mb-0">Belum ada data siswa. Tambahkan siswa dulu sebelum menambah
                            alumni.</div>
                    @endif
                </div>
                <div class="mb-3">
                    <label class="form-label">Tanggal Kelulusan</label>
                    <input type="date" name="graduation_date" class="form-control" value="{{ old('graduation_date') }}"
                        required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Kelas Kelulusan (Opsional)</label>
                    <input type="text" name="graduation_class" class="form-control" value="{{ old('graduation_class') }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Pekerjaan Saat Ini (Opsional)</label>
                    <input type="text" name="current_job" class="form-control" value="{{ old('current_job') }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Perusahaan Saat Ini (Opsional)</label>
                    <input type="text" name="current_company" class="form-control" value="{{ old('current_company') }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">LinkedIn / CV Online (Opsional)</label>
                    <input type="url" name="linkedin_profile" class="form-control" value="{{ old('linkedin_profile') }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Skills (diisi otomatis dari data siswa)</label>
                    <input type="text" name="skills" id="skills_input" class="form-control" readonly>
                    <small class="form-text text-muted">Skills akan terisi otomatis ketika siswa dipilih</small>
                </div>
                <div class="d-grid gap-2">
                    <button class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('student_select').addEventListener('change', function () {
            const studentId = this.value;
            const skillsInput = document.getElementById('skills_input');

            if (studentId) {
                // Fetch student skills via AJAX
                fetch(`/admin/students/${studentId}/skills`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.skills && data.skills.length > 0) {
                            skillsInput.value = data.skills.join(', ');
                        } else {
                            skillsInput.value = 'Tidak ada skills yang tercatat';
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching skills:', error);
                        skillsInput.value = 'Error mengambil data skills';
                    });
            } else {
                skillsInput.value = '';
            }
        });

        // Load skills on page load if student is already selected
        document.addEventListener('DOMContentLoaded', function () {
            const studentSelect = document.getElementById('student_select');
            if (studentSelect.value) {
                studentSelect.dispatchEvent(new Event('change'));
            }
        });
    </script>
@endsection