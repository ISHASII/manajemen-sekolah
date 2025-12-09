@extends('layouts.app')

@section('title', 'Edit Profil Guru')

@section('content')
<div class="teacher-page-wrapper">
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header border-bottom" style="background-color: #ff8c00;">
                        <h5 class="mb-0">Edit Profil Guru</h5>
                    </div>
                    <div class="card-body">
                        @if($errors->any())
                            <div class="alert alert-danger">{{ $errors->first() }}</div>
                        @endif

                        @php
                            // Prepare array-safe string values
                            $arrToString = function($val) {
                                if (is_iterable($val) && !is_string($val)) {
                                    $items = [];
                                    foreach ((array)$val as $v) {
                                        if (is_iterable($v) && !is_string($v)) {
                                            // nested array/object; json encode to preserve info
                                            $items[] = json_encode($v);
                                        } else {
                                            $items[] = (string)$v;
                                        }
                                    }
                                    return implode(', ', $items);
                                }
                                if (is_object($val)) {
                                    return json_encode($val);
                                }
                                return $val ?? '';
                            };
                            $safe = function($val, $default = '') use ($arrToString) {
                                if (is_iterable($val) && !is_string($val)) {
                                    return $arrToString($val);
                                }
                                return $val ?? $default;
                            };
                            $nameValue = $safe(old('name', $user->name));
                            $emailValue = $safe(old('email', $user->email));
                            $phoneValue = $safe(old('phone', $user->phone));
                            $addressValue = $safe(old('address', $user->address));
                            $birthDateValue = $safe(old('birth_date', isset($user->birth_date) && !is_array($user->birth_date) ? optional($user->birth_date)->format('Y-m-d') : ''));
                            $genderValue = $safe(old('gender', is_array($user->gender) ? $arrToString($user->gender) : ($user->gender ?? '')));
                            $nipValue = $safe(old('nip', $teacher->nip ?? ''));
                            $selectedSubjects = (array) old('subjects', $teacher->subjects ?? []);
                            if (old('subjects')) {
                                $selectedSubjects = (array) old('subjects');
                            } elseif (!empty($teacher->subjects)) {
                                    // teacher->subjects might be array, JSON, comma separated string, or null
                                        if (is_array($teacher->subjects)) {
                                        $subjectsValue = $arrToString($teacher->subjects);
                                    } elseif (is_string($teacher->subjects)) {
                                        // if JSON array string
                                        $maybeJson = @json_decode($teacher->subjects, true);
                                        if (is_array($maybeJson)) {
                                            $subjectsValue = $arrToString($maybeJson);
                                        } elseif (strpos($teacher->subjects, ',') !== false) {
                                            $subjectsValue = implode(', ', array_map('trim', explode(',', $teacher->subjects)));
                                        } else {
                                            $subjectsValue = $teacher->subjects;
                                        }
                                    } else {
                                        $subjectsValue = '';
                                    }
                            }
                            $qualValue = '';
                            if (old('qualifications')) {
                                $qualValue = $arrToString(old('qualifications'));
                            } elseif (!empty($teacher->qualifications)) {
                                if (is_array($teacher->qualifications)) {
                                    $qualValue = $arrToString($teacher->qualifications);
                                } elseif (is_string($teacher->qualifications)) {
                                    $maybeJson = @json_decode($teacher->qualifications, true);
                                    if (is_array($maybeJson)) {
                                        $qualValue = $arrToString($maybeJson);
                                    } elseif (strpos($teacher->qualifications, ',') !== false) {
                                        $qualValue = implode(', ', array_map('trim', explode(',', $teacher->qualifications)));
                                    } else {
                                        $qualValue = $teacher->qualifications;
                                    }
                                } else {
                                    $qualValue = '';
                                }
                            }
                            $certValue = '';
                            if (old('certifications')) {
                                $certValue = $arrToString(old('certifications'));
                            } elseif (!empty($teacher->certifications)) {
                                if (is_array($teacher->certifications)) {
                                    $certValue = $arrToString($teacher->certifications);
                                } elseif (is_string($teacher->certifications)) {
                                    $maybeJson = @json_decode($teacher->certifications, true);
                                    if (is_array($maybeJson)) {
                                        $certValue = $arrToString($maybeJson);
                                    } elseif (strpos($teacher->certifications, ',') !== false) {
                                        $certValue = implode(', ', array_map('trim', explode(',', $teacher->certifications)));
                                    } else {
                                        $certValue = $teacher->certifications;
                                    }
                                } else {
                                    $certValue = '';
                                }
                            }
                        @endphp

                        <form method="POST" action="{{ route('teacher.profile.update') }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Nama</label>
                                        <input type="text" name="name" value="{{ $nameValue }}" class="form-control">
                                        @error('name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="email" value="{{ $emailValue }}" class="form-control">
                                        @error('email')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Telepon</label>
                                        <input type="text" name="phone" value="{{ $phoneValue }}" class="form-control">
                                        @error('phone')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Alamat</label>
                                        <textarea name="address" class="form-control" rows="2">{{ $addressValue }}</textarea>
                                        @error('address')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Tanggal Lahir</label>
                                        <input type="date" name="birth_date" value="{{ $birthDateValue }}" class="form-control">
                                        @error('birth_date')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Jenis Kelamin</label>
                                        <select name="gender" class="form-select">
                                            <option value="">-- Pilih Jenis Kelamin --</option>
                                            <option value="male" {{ $genderValue === 'male' ? 'selected' : '' }}>Laki-laki</option>
                                            <option value="female" {{ $genderValue === 'female' ? 'selected' : '' }}>Perempuan</option>
                                            <option value="other" {{ $genderValue === 'other' ? 'selected' : '' }}>Lainnya</option>
                                        </select>
                                        @error('gender')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">NIP</label>
                                        <input type="text" name="nip" value="{{ $nipValue }}" class="form-control">
                                        @error('nip')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Mata Pelajaran</label>
                                        <input type="hidden" name="subjects_present" value="1">
                                        <select name="subjects[]" class="form-select" multiple>
                                            @foreach($subjects as $subject)
                                                <option value="{{ $subject->id }}" {{ in_array((string)$subject->id, array_map('strval', $selectedSubjects)) ? 'selected' : '' }}>{{ $subject->name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="form-text text-white">Pilih satu atau beberapa mata pelajaran (Ctrl/Cmd + klik untuk memilih beberapa).</div>
                                        @error('subjects')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Kualifikasi</label>
                                        <input type="hidden" name="qualifications_present" value="1">
                                        <div class="multi-input" data-name="qualifications">
                                                <div class="field-list">
                                                    @php $qVals = old('qualifications', $teacher->qualifications ?? []); @endphp
                                                    @if(is_string($qVals) && strlen($qVals))
                                                        @php $qVals = array_map('trim', explode(',', $qVals)); @endphp
                                                    @endif
                                                    @if(is_array($qVals) && count($qVals) > 0)
                                                        @foreach($qVals as $q)
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
                                                <button type="button" class="btn btn-sm btn-secondary add-input mt-2" data-name="qualifications">Tambah Kualifikasi</button>
                                        </div>
                                        @error('qualifications')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Sertifikasi</label>
                                        <input type="hidden" name="certifications_present" value="1">
                                        <div class="multi-input" data-name="certifications">
                                            <div class="field-list">
                                                @php $cVals = old('certifications', $teacher->certifications ?? []); @endphp
                                                @if(is_string($cVals) && strlen($cVals))
                                                    @php $cVals = array_map('trim', explode(',', $cVals)); @endphp
                                                @endif
                                                @if(is_array($cVals) && count($cVals) > 0)
                                                    @foreach($cVals as $c)
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
                                            <button type="button" class="btn btn-sm btn-secondary add-input mt-2" data-name="certifications">Tambah Sertifikasi</button>
                                        </div>
                                        @error('certifications')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Foto Profil</label>
                                        <input type="file" name="profile_photo" class="form-control">
                                        @if($user->profile_photo)
                                            <div class="mt-2">
                                                <img src="{{ Storage::url($user->profile_photo) }}" class="img-fluid rounded-circle" width="80" alt="Current">
                                            </div>
                                        @endif
                                        @error('profile_photo')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Password Baru (opsional)</label>
                                        <input type="password" name="password" class="form-control" autocomplete="new-password">
                                        <small class="form-text text-white">Isi untuk mengganti password Anda (minimal 8 karakter).</small>
                                        @error('password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Konfirmasi Password</label>
                                        <input type="password" name="password_confirmation" class="form-control" autocomplete="new-password">
                                        @error('password_confirmation')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>
                            <div>
                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                <a href="{{ route('teacher.profile') }}" class="btn btn-outline-secondary">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    function addInputRow(container, name, value = ''){
        const list = container.querySelector('.field-list');
        const wrapper = document.createElement('div');
        wrapper.className = 'input-row input-group mb-2';
        wrapper.innerHTML = `<input type="text" name="${name}[]" class="form-control" value="${value}">` +
            `<button type="button" class="btn btn-outline-danger remove-input ms-2">Hapus</button>`;
        list.appendChild(wrapper);
        wrapper.querySelector('.remove-input').addEventListener('click', function(){ wrapper.remove(); });
    }
    document.querySelectorAll('.multi-input').forEach(function(container){
        const btn = container.querySelector('.add-input');
        const name = btn.dataset.name;
        btn.addEventListener('click', function(){ addInputRow(container, name, ''); });
        container.querySelectorAll('.remove-input').forEach(el=>el.addEventListener('click', function(){
            console.debug('remove-input clicked', el);
            const row = el.closest('.input-row');
            if (row) row.remove();
        }));
    });
});
</script>
@endpush
