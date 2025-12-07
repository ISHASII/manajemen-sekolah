@extends('layouts.app')

@section('title', 'Buat Profil Siswa')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0">Buat Profil Siswa</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Profil untuk: </strong> {{ auth()->user()->name }} <small
                                class="text-muted">({{ auth()->user()->email }})</small>
                        </div>
                        @if(session('info'))
                            <div class="alert alert-info">{{ session('info') }}</div>
                        @endif
                        @if(session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif
                        <form method="POST" action="{{ route('student.profile.store') }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nomor Induk (Student ID)</label>
                                    <input type="text" name="student_id"
                                        class="form-control @error('student_id') is-invalid @enderror"
                                        value="{{ old('student_id') }}" required>
                                    @error('student_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tempat Lahir</label>
                                    <input type="text" name="place_of_birth" class="form-control"
                                        value="{{ old('place_of_birth') }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tanggal Lahir</label>
                                    <input type="date" name="birth_date" class="form-control"
                                        value="{{ old('birth_date') }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Agama</label>
                                    <select name="religion" class="form-select @error('religion') is-invalid @enderror"
                                        required>
                                        <option value="">Pilih Agama</option>
                                        <option value="islam" {{ old('religion') === 'islam' ? 'selected' : '' }}>Islam
                                        </option>
                                        <option value="kristen" {{ old('religion') === 'kristen' ? 'selected' : '' }}>Kristen
                                        </option>
                                        <option value="katolik" {{ old('religion') === 'katolik' ? 'selected' : '' }}>Katolik
                                        </option>
                                        <option value="hindu" {{ old('religion') === 'hindu' ? 'selected' : '' }}>Hindu
                                        </option>
                                        <option value="budha" {{ old('religion') === 'budha' ? 'selected' : '' }}>Budha
                                        </option>
                                        <option value="khonghucu" {{ old('religion') === 'khonghucu' ? 'selected' : '' }}>
                                            Khonghucu</option>
                                    </select>
                                    @error('religion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <!-- Class select removed. Class assignment should be done by admin. -->
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Alamat</label>
                                <textarea name="address" class="form-control" required>{{ old('address') }}</textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Orang Tua / Wali</label>
                                <input type="text" name="parent_name" class="form-control" value="{{ old('parent_name') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Telepon Orang Tua</label>
                                <input type="text" name="parent_phone" class="form-control"
                                    value="{{ old('parent_phone') }}">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Pekerjaan Orang Tua</label>
                                <input type="text" name="parent_job" class="form-control" value="{{ old('parent_job') }}">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Alamat Orang Tua</label>
                                <textarea name="parent_address" class="form-control"
                                    required>{{ old('parent_address') }}</textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Masuk (Enrollment date)</label>
                                <input type="date" name="enrollment_date" class="form-control"
                                    value="{{ old('enrollment_date', now()->format('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Minat & Bakat</label>
                                <div id="interests-container">
                                    <div class="input-group mb-2">
                                        <input type="text" name="interests_talents[]" class="form-control"
                                            placeholder="Minat/Bakat">
                                        <button type="button" class="btn btn-outline-danger"
                                            onclick="removeInterest(this)"><i class="fas fa-minus"></i></button>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addInterest()"><i
                                        class="fas fa-plus"></i> Tambah</button>
                            </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('student.profile') }}" class="btn btn-secondary me-2">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                    </form>
                </div>
            </div>
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
                                    <button type="button" class="btn btn-outline-danger" onclick="removeInterest(this)"><i class="fas fa-minus"></i></button>
                                `;
            container.appendChild(div);
        }

        function removeInterest(button) {
            button.parentElement.remove();
        }
    </script>
@endsection