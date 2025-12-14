@php
    $modalId = 'attendanceModal' . ($student->id ?? '');
@endphp

<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-labelledby="{{ $modalId }}Label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('teacher.attendance.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="{{ $modalId }}Label">Absensi: {{ $student->user->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="student_id" value="{{ $student->id }}">
                    @if(isset($classId))
                        <input type="hidden" name="class_id" value="{{ $classId }}">
                    @endif
                    @if(isset($trainingClassId))
                        <input type="hidden" name="training_class_id" value="{{ $trainingClassId }}">
                    @endif

                    <div class="mb-3">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="date"
                            value="{{ optional($student->latestAttendance)->date?->format('Y-m-d') ?? now()->format('Y-m-d') }}"
                            class="form-control" required>
                        @if($student->latestAttendance)
                            <div class="small text-muted mt-1">Terakhir:
                                {{ $student->latestAttendance->date->translatedFormat('d F Y') }} —
                                {{ ucfirst($student->latestAttendance->status) }}
                            </div>
                        @endif
                    </div>

                    @if(isset($subjects) && $subjects->count())
                        <div class="mb-3">
                            <label class="form-label">Mata Pelajaran</label>
                            <select name="subject_id" class="form-select">
                                <option value="">-- Pilih Mata Pelajaran --</option>
                                @foreach($subjects as $sub)
                                    <option value="{{ $sub->id }}" {{ optional($student->latestAttendance)->subject_id == $sub->id ? 'selected' : '' }}>{{ $sub->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="present" {{ optional($student->latestAttendance)->status === 'present' ? 'selected' : '' }}>Hadir</option>
                            <option value="absent" {{ optional($student->latestAttendance)->status === 'absent' ? 'selected' : '' }}>Alpha</option>
                            <option value="sick" {{ optional($student->latestAttendance)->status === 'sick' ? 'selected' : '' }}>Sakit</option>
                            <option value="excused" {{ optional($student->latestAttendance)->status === 'excused' ? 'selected' : '' }}>Izin</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Catatan (opsional)</label>
                        <textarea name="notes" class="form-control"
                            rows="3">{{ optional($student->latestAttendance)->notes ?? '' }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Absensi</button>
                </div>
            </form>
        </div>
    </div>
</div>