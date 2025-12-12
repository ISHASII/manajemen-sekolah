<div class="modal fade" id="editGradeModal{{ $grade->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('teacher.grades.update', $grade->id) }}">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Nilai - {{ $grade->student?->user->name ?? '' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Mata Pelajaran</label>
                        <select name="subject_id" class="form-control" required>
                            @foreach(App\Models\Subject::where('is_active', true)->get() as $subject)
                                <option value="{{ $subject->id }}" {{ $grade->subject_id == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nilai</label>
                                <input type="number" name="score" value="{{ $grade->score }}" class="form-control"
                                    min="0" max="100" step="0.1" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Jenis Penilaian</label>
                                <select name="assessment_type" class="form-control" required>
                                    <option value="daily" {{ $grade->assessment_type == 'daily' ? 'selected' : '' }}>
                                        Harian</option>
                                    <option value="midterm" {{ $grade->assessment_type == 'midterm' ? 'selected' : '' }}>
                                        UTS</option>
                                    <option value="final" {{ $grade->assessment_type == 'final' ? 'selected' : '' }}>UAS
                                    </option>
                                    <option value="project" {{ $grade->assessment_type == 'project' ? 'selected' : '' }}>
                                        Proyek</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Semester</label>
                        <input type="text" name="semester" value="{{ $grade->semester }}" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Penilaian</label>
                        <input type="date" name="assessment_date"
                            value="{{ optional($grade->assessment_date)->format('Y-m-d') }}" class="form-control"
                            required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan (Opsional)</label>
                        <textarea name="notes" class="form-control">{{ $grade->notes }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
            <form method="POST" action="{{ route('teacher.grades.destroy', $grade->id) }}" class="ms-2 p-2">
                @csrf
                @method('DELETE')
                <button class="btn btn-danger" onclick="return confirm('Hapus nilai ini?')">Hapus</button>
            </form>
        </div>
    </div>
</div>
