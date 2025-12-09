@extends('layouts.admin')

@section('title', 'Tambah Guru')

@section('admin-content')
    <div class="container py-4">
        <h3>Tambah Guru</h3>
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
            <form action="{{ route('admin.teachers.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                        @error('name') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                        @error('email') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Teacher ID</label>
                        <input type="text" name="teacher_id" class="form-control" value="{{ old('teacher_id') }}" required>
                        @error('teacher_id') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">NIP</label>
                        <input type="text" name="nip" class="form-control" value="{{ old('nip') }}">
                        @error('nip') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Mata Pelajaran</label>
                        <input type="hidden" name="subjects_present" value="1">
                        <select name="subjects[]" class="form-select" multiple>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ in_array((string) $subject->id, (array) old('subjects', [])) ? 'selected' : '' }}>{{ $subject->name }}</option>
                            @endforeach
                        </select>
                        <div class="form-text">Pilih satu atau beberapa mata pelajaran (Ctrl/Cmd + klik untuk memilih
                            beberapa).</div>
                        @error('subjects') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Kualifikasi</label>
                        <input type="hidden" name="qualifications_present" value="1">
                        <div class="multi-input" data-name="qualifications">
                            <div class="field-list">
                                @php $oq = old('qualifications', []); @endphp
                                @if(is_string($oq) && strlen($oq))
                                    @php $oq = array_map('trim', explode(',', $oq)); @endphp
                                @endif
                                @if(is_array($oq) && count($oq) > 0)
                                    @foreach($oq as $q)
                                        <div class="input-row input-group mb-2">
                                            <input type="text" name="qualifications[]" class="form-control" value="{{ $q }}">
                                            <button type="button" class="btn btn-outline-danger remove-input ms-2">Hapus</button>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="input-row input-group mb-2">
                                        <input type="text" name="qualifications[]" class="form-control" value="">
                                        <button type="button" class="btn btn-outline-danger remove-input ms-2">Hapus</button>
                                    </div>
                                @endif
                            </div>
                            <button type="button" class="btn btn-sm btn-secondary add-input mt-2"
                                data-name="qualifications">Tambah Kualifikasi</button>
                        </div>
                        @error('qualifications') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Sertifikasi</label>
                        <input type="hidden" name="certifications_present" value="1">
                        <div class="multi-input" data-name="certifications">
                            <div class="field-list">
                                @php $oc = old('certifications', []); @endphp
                                @if(is_string($oc) && strlen($oc))
                                    @php $oc = array_map('trim', explode(',', $oc)); @endphp
                                @endif
                                @if(is_array($oc) && count($oc) > 0)
                                    @foreach($oc as $c)
                                        <div class="input-row input-group mb-2">
                                            <input type="text" name="certifications[]" class="form-control" value="{{ $c }}">
                                            <button type="button" class="btn btn-outline-danger remove-input ms-2">Hapus</button>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="input-row input-group mb-2">
                                        <input type="text" name="certifications[]" class="form-control" value="">
                                        <button type="button" class="btn btn-outline-danger remove-input ms-2">Hapus</button>
                                    </div>
                                @endif
                            </div>
                            <button type="button" class="btn btn-sm btn-secondary add-input mt-2"
                                data-name="certifications">Tambah Sertifikasi</button>
                        </div>
                        @error('certifications') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Masuk</label>
                        <input type="date" name="hire_date" class="form-control"
                            value="{{ old('hire_date') ?? now()->toDateString() }}" required>
                        @error('hire_date') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Password (opsional)</label>
                        <input type="password" name="password" class="form-control" autocomplete="new-password">
                        <small class="form-text" style="color: black;">
                            Kosongkan untuk menggunakan password default <code>password</code>.
                        </small>
                        @error('password') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="form-control"
                            autocomplete="new-password">
                        @error('password_confirmation') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                            <option value="retired" {{ old('status') == 'retired' ? 'selected' : '' }}>Pensiun</option>
                        </select>
                    </div>
                </div>
                <div class="mt-4 text-end">
                    <a href="{{ route('admin.teachers.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

{{-- No tag-based script needed when using the multi-input UI below --}}
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function addInputRow(container, name, value = '') {
                const list = container.querySelector('.field-list');
                const wrapper = document.createElement('div');
                wrapper.className = 'input-row input-group mb-2';
                wrapper.innerHTML = `<input type="text" name="${name}[]" class="form-control" value="${value}">` +
                    `<button type="button" class="btn btn-outline-danger remove-input ms-2">Hapus</button>`;
                list.appendChild(wrapper);
                wrapper.querySelector('.remove-input').addEventListener('click', function () { wrapper.remove(); });
            }
            document.querySelectorAll('.multi-input').forEach(function (container) {
                const btn = container.querySelector('.add-input');
                const name = btn.dataset.name;
                btn.addEventListener('click', function () { addInputRow(container, name, ''); });
                container.querySelectorAll('.remove-input').forEach(el => el.addEventListener('click', function () {
                    console.debug('remove-input clicked', el);
                    const row = el.closest('.input-row');
                    if (row) row.remove();
                }));
            });
        });
    </script>
@endpush
