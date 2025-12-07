@extends('layouts.admin')

@section('title', 'Edit Info Sekolah')

@section('admin-content')
    <div class="container py-4">
        <h3>Edit Info Sekolah</h3>
        <div class="card mt-3 p-4">
            <form action="{{ route('admin.school.update') }}" method="POST">
                @csrf
                @method('PUT')

                @php
                    // Helper to safely stringify a field that may be scalar, array/collection, or array of objects
                    $stringify = function ($val) {
                        if (is_null($val))
                            return '';
                        if (!is_iterable($val))
                            return (string) $val;
                        $parts = [];
                        foreach ($val as $k => $v) {
                            if (is_scalar($v)) {
                                $parts[] = (string) $v;
                            } else {
                                // Use JSON encode for complex items
                                $parts[] = json_encode($v, JSON_UNESCAPED_UNICODE);
                            }
                        }
                        return implode(', ', $parts);
                    };

                    $facilitiesValue = old('facilities');
                    if ($facilitiesValue === null && isset($school->facilities)) {
                        $facilitiesValue = $stringify($school->facilities);
                    }
                    $programsValue = old('programs');
                    if ($programsValue === null && isset($school->programs)) {
                        $programsValue = $stringify($school->programs);
                    }
                    $socialMediaValue = old('social_media');
                    if ($socialMediaValue === null && isset($school->social_media)) {
                        // For social media, prefer JSON representation for key:value mapping
                        if (is_iterable($school->social_media)) {
                            $socialMediaValue = json_encode((array) $school->social_media, JSON_UNESCAPED_UNICODE);
                        } else {
                            $socialMediaValue = (string) $school->social_media;
                        }
                    }
                @endphp
                @php
                    // Helper to render a single facility/program item as a string (for input values)
                    $itemToString = function ($it) {
                        if (is_null($it))
                            return '';
                        if (is_scalar($it))
                            return (string) $it;
                        if (is_array($it)) {
                            if (isset($it['name']))
                                return (string) $it['name'];
                            if (isset($it['title']))
                                return (string) $it['title'];
                            // Fallback to JSON for structured arrays
                            return json_encode($it, JSON_UNESCAPED_UNICODE);
                        }
                        if (is_object($it)) {
                            if (property_exists($it, 'name'))
                                return (string) $it->name;
                            if (property_exists($it, 'title'))
                                return (string) $it->title;
                            if (method_exists($it, '__toString'))
                                return (string) $it;
                            return json_encode($it, JSON_UNESCAPED_UNICODE);
                        }
                        return (string) $it;
                    };
                @endphp

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama Sekolah</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $school->name ?? '') }}"
                            required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control"
                            value="{{ old('email', $school->email ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Telepon</label>
                        <input type="text" name="phone" class="form-control"
                            value="{{ old('phone', $school->phone ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Website</label>
                        <input type="text" name="website" class="form-control"
                            value="{{ old('website', $school->website ?? '') }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Alamat</label>
                        <textarea name="address" class="form-control"
                            rows="2">{{ old('address', $school->address ?? '') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" class="form-control"
                            rows="3">{{ old('description', $school->description ?? '') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Visi</label>
                        <textarea name="vision" class="form-control"
                            rows="3">{{ old('vision', $school->vision ?? '') }}</textarea>
                        <div class="form-text">Tulis visi sekolah di sini. Untuk tampilan yang konsisten, gunakan satu
                            paragraf.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Misi</label>
                        <textarea name="mission" class="form-control"
                            rows="3">{{ old('mission', $school->mission ?? '') }}</textarea>
                        <div class="form-text">Tulis setiap poin misi pada baris baru. (Contoh: tekan Enter untuk memisahkan
                            setiap item misi)</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Fasilitas</label>
                        <div id="facilities-wrapper">
                            @php
                                $facilitiesArray = [];
                                if (old('facilities') !== null) {
                                    $oldFacilities = old('facilities');
                                    if (is_array($oldFacilities)) {
                                        $facilitiesArray = $oldFacilities;
                                    } elseif (is_string($oldFacilities)) {
                                        $facilitiesArray = array_filter(array_map('trim', explode(',', $oldFacilities)));
                                    }
                                } elseif (isset($school->facilities)) {
                                    if (is_iterable($school->facilities)) {
                                        $facilitiesArray = (array) $school->facilities;
                                    } elseif (is_string($school->facilities)) {
                                        $facilitiesArray = array_filter(array_map('trim', explode(',', $school->facilities)));
                                    }
                                }
                                if (empty($facilitiesArray)) {
                                    $facilitiesArray = [''];
                                }
                            @endphp
                            @foreach($facilitiesArray as $facItem)
                                <div class="input-group mb-2 facilities-item">
                                    <input type="text" name="facilities[]" class="form-control"
                                        value="{{ $itemToString($facItem) }}">
                                    <button class="btn btn-outline-danger remove-facilities" type="button">&times;</button>
                                </div>
                            @endforeach
                        </div>
                        <button id="add-facility" class="btn btn-outline-primary btn-sm" type="button">Tambah
                            Fasilitas</button>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Program</label>
                        <div id="programs-wrapper">
                            @php
                                $programsArray = [];
                                if (old('programs') !== null) {
                                    $oldPrograms = old('programs');
                                    if (is_array($oldPrograms)) {
                                        $programsArray = $oldPrograms;
                                    } elseif (is_string($oldPrograms)) {
                                        $programsArray = array_filter(array_map('trim', explode(',', $oldPrograms)));
                                    }
                                } elseif (isset($school->programs)) {
                                    if (is_iterable($school->programs)) {
                                        $programsArray = (array) $school->programs;
                                    } elseif (is_string($school->programs)) {
                                        $programsArray = array_filter(array_map('trim', explode(',', $school->programs)));
                                    }
                                }
                                if (empty($programsArray)) {
                                    $programsArray = [''];
                                }
                            @endphp
                            @foreach($programsArray as $progItem)
                                <div class="input-group mb-2 programs-item">
                                    <input type="text" name="programs[]" class="form-control"
                                        value="{{ $itemToString($progItem) }}">
                                    <button class="btn btn-outline-danger remove-programs" type="button">&times;</button>
                                </div>
                            @endforeach
                        </div>
                        <button id="add-program" class="btn btn-outline-primary btn-sm" type="button">Tambah
                            Program</button>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Social Media</label>
                        @php
                            $socialDefaults = ['instagram' => '', 'facebook' => '', 'twitter' => ''];
                            $socialOld = old('social_media');
                            if ($socialOld && is_array($socialOld)) {
                                $socialValues = array_merge($socialDefaults, $socialOld);
                            } elseif (isset($school->social_media) && is_iterable($school->social_media)) {
                                $socialValues = array_merge($socialDefaults, (array) $school->social_media);
                            } else {
                                $socialValues = $socialDefaults;
                            }
                        @endphp
                        <div class="mb-2">
                            <label class="form-label">Instagram</label>
                            <input type="url" name="social_media[instagram]" class="form-control"
                                value="{{ $socialValues['instagram'] ?? '' }}">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Facebook</label>
                            <input type="url" name="social_media[facebook]" class="form-control"
                                value="{{ $socialValues['facebook'] ?? '' }}">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Twitter</label>
                            <input type="url" name="social_media[twitter]" class="form-control"
                                value="{{ $socialValues['twitter'] ?? '' }}">
                        </div>
                    </div>
                </div>

                <div class="mt-4 text-end">
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">Batal</a>
                    <button class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            function initList(wrapperId, addBtnId, removeSelector) {
                const wrapper = document.getElementById(wrapperId);
                const addBtn = document.getElementById(addBtnId);

                if (!wrapper || !addBtn) return;

                addBtn.addEventListener('click', function () {
                    const newGroup = document.createElement('div');
                    newGroup.className = wrapper.querySelector('.' + removeSelector + '-item')?.className || 'input-group mb-2';
                    newGroup.classList.add(removeSelector + '-item');
                    newGroup.innerHTML = `\
                                                <input type="text" name="${removeSelector}[]" class="form-control">\
                                                <button class="btn btn-outline-danger remove-${removeSelector}" type="button">&times;</button>\
                                            `;
                    wrapper.appendChild(newGroup);
                    attachRemoveListeners(wrapper, removeSelector);
                });
                attachRemoveListeners(wrapper, removeSelector);
            }

            function attachRemoveListeners(wrapper, removeSelector) {
                wrapper.querySelectorAll('.remove-' + removeSelector).forEach(function (btn) {
                    btn.removeEventListener('click', handleRemove);
                    btn.addEventListener('click', handleRemove);
                });
            }

            function handleRemove(e) {
                const target = e.target;
                const group = target.closest('.' + target.className.split(' ').find(c => c.startsWith('remove-'))?.replace('remove-', '') + '-item');
                if (group && group.parentNode) {
                    group.parentNode.removeChild(group);
                }
            }

            initList('facilities-wrapper', 'add-facility', 'facilities');
            initList('programs-wrapper', 'add-program', 'programs');
        })();
    </script>
@endpush