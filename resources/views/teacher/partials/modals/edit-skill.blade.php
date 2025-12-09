<div class="modal fade" id="editSkillModal{{ $skill->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <form method="POST" action="{{ route('teacher.skills.update', $skill->id) }}">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">Edit Keterampilan - {{ $skill->student?->user->name ?? '' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Keterampilan</label>
                        <input type="text" name="skill_name" value="{{ $skill->skill_name }}" class="form-control"
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kategori</label>
                        <select name="skill_category" class="form-control" required>
                            @foreach(['academic', 'technical', 'soft_skill', 'language', 'art', 'sport'] as $cat)
                                <option value="{{ $cat }}" {{ $skill->skill_category == $cat ? 'selected' : '' }}>
                                    {{ ucfirst($cat) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Level Kemampuan</label>
                        <select name="proficiency_level" class="form-control" required>
                            @foreach(['beginner', 'intermediate', 'advanced', 'expert'] as $lvl)
                                <option value="{{ $lvl }}" {{ $skill->proficiency_level == $lvl ? 'selected' : '' }}>
                                    {{ ucfirst($lvl) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" class="form-control">{{ $skill->description }}</textarea>
                    </div>
                </div>

                <div class="modal-footer d-flex justify-content-between">
                    <!-- Tombol Delete dipindah ke ekstra form di sini -->
                    <form method="POST" action="{{ route('teacher.skills.destroy', $skill->id) }}"
                        onsubmit="return confirm('Hapus penilaian keterampilan ini?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger">Hapus</button>
                    </form>

                    <div>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>
