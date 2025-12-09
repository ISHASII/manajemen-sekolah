<div class="modal fade" id="addGradeModal{{ $student->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('teacher.grades.store') }}">
                @csrf
                <input type="hidden" name="student_id" value="{{ $student->id }}">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Nilai - {{ $student->user->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="subject_id{{ $student->id }}" class="form-label">Mata Pelajaran</label>
                        <select name="subject_id" id="subject_id{{ $student->id }}" class="form-control" required>
                            <option value="">-- Pilih Mata Pelajaran --</option>
                            @foreach(App\Models\Subject::where('is_active', true)->get() as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="score{{ $student->id }}" class="form-label">Nilai</label>
                                <input type="number" name="score" id="score{{ $student->id }}" class="form-control"
                                    min="0" max="100" step="0.1" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="assessment_type{{ $student->id }}" class="form-label">Jenis
                                    Penilaian</label>
                                <select name="assessment_type" id="assessment_type{{ $student->id }}"
                                    class="form-control" required>
                                    <option value="daily">Harian</option>
                                    <option value="midterm">UTS</option>
                                    <option value="final">UAS</option>
                                    <option value="project">Proyek</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="semester{{ $student->id }}" class="form-label">Semester</label>
                        <input type="text" name="semester" id="semester{{ $student->id }}" class="form-control"
                            placeholder="Contoh: Ganjil 2024/2025" required>
                    </div>
                    <div class="mb-3">
                        <label for="assessment_date{{ $student->id }}" class="form-label">Tanggal Penilaian</label>
                        <input type="date" name="assessment_date" id="assessment_date{{ $student->id }}"
                            class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="notes{{ $student->id }}" class="form-label">Catatan (Opsional)</label>
                        <textarea name="notes" id="notes{{ $student->id }}" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Nilai</button>
                </div>
            </form>
        </div>
    </div>
</div>
