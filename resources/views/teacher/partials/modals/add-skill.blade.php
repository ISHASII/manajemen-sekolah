<div class="modal fade" id="addSkillModal{{ $student->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('teacher.skills.store') }}">
                @csrf
                <input type="hidden" name="student_id" value="{{ $student->id }}">
                <div class="modal-header">
                    <h5 class="modal-title">Penilaian Keterampilan - {{ $student->user->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="skill_name{{ $student->id }}" class="form-label">Nama Keterampilan</label>
                        <input type="text" name="skill_name" id="skill_name{{ $student->id }}" class="form-control"
                            required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="skill_category{{ $student->id }}" class="form-label">Kategori</label>
                                <select name="skill_category" id="skill_category{{ $student->id }}" class="form-control"
                                    required>
                                    <option value="academic">Akademik</option>
                                    <option value="technical">Teknis</option>
                                    <option value="soft_skill">Soft Skill</option>
                                    <option value="language">Bahasa</option>
                                    <option value="art">Seni</option>
                                    <option value="sport">Olahraga</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="proficiency_level{{ $student->id }}" class="form-label">Level
                                    Kemampuan</label>
                                <select name="proficiency_level" id="proficiency_level{{ $student->id }}"
                                    class="form-control" required>
                                    <option value="beginner">Pemula</option>
                                    <option value="intermediate">Menengah</option>
                                    <option value="advanced">Mahir</option>
                                    <option value="expert">Ahli</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="skill_description{{ $student->id }}" class="form-label">Deskripsi</label>
                        <textarea name="description" id="skill_description{{ $student->id }}" class="form-control"
                            rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Penilaian</button>
                </div>
            </form>
        </div>
    </div>
</div>
