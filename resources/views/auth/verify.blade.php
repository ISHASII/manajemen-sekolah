@extends('layouts.app')

@section('content')
    <div class="min-vh-100 d-flex align-items-center justify-content-center py-5"
        style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-10 col-lg-8">
                    <!-- Email Verification Card -->
                    <div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-4">
                        <div class="card-body p-5">
                            <div class="text-center mb-4">
                                <div class="mb-4">
                                    <svg width="80" height="80" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="12" cy="12" r="10" stroke="#667eea" stroke-width="2" />
                                        <path d="M12 6v6l4 2" stroke="#667eea" stroke-width="2" stroke-linecap="round" />
                                    </svg>
                                </div>
                                <h2 class="fw-bold mb-2">{{ __('Verify Your Email Address') }}</h2>
                                <p class="text-muted">{{ __('We need to verify your email to continue') }}</p>
                            </div>

                            @if (session('resent'))
                                <div class="alert alert-success border-0 rounded-3 d-flex align-items-center" role="alert">
                                    <svg class="me-2" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="12" cy="12" r="10" fill="#28a745" />
                                        <path d="M8 12l2 2 4-4" stroke="white" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </svg>
                                    <span>{{ __('A fresh verification link has been sent to your email address.') }}</span>
                                </div>
                            @endif

                            <div class="text-center mb-4">
                                <p class="mb-3">
                                    {{ __('Before proceeding, please check your email for a verification link.') }}
                                </p>
                                <p class="text-muted">{{ __('If you did not receive the email') }}</p>
                            </div>

                            <form method="POST" action="{{ route('verification.resend') }}" class="text-center">
                                @csrf
                                <button type="submit" class="btn btn-lg px-5 py-3 rounded-pill fw-semibold"
                                    style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none;">
                                    <svg class="me-2" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg" style="vertical-align: middle;">
                                        <path d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor"
                                            stroke-width="2" />
                                        <path d="M9 10l3-3m0 0l3 3m-3-3v8" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    {{ __('Resend Verification Email') }}
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- File Upload with Preview Card -->
                    <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                        <div class="card-body p-5">
                            <h4 class="fw-bold mb-4">{{ __('Upload Documents') }}</h4>

                            @if (session('success'))
                                <div class="alert alert-success border-0 rounded-3 d-flex align-items-center" role="alert">
                                    <svg class="me-2" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="12" cy="12" r="10" fill="#28a745" />
                                        <path d="M8 12l2 2 4-4" stroke="white" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </svg>
                                    <span>{{ session('success') }}</span>
                                </div>
                            @endif
                            @if (session('error'))
                                <div class="alert alert-danger border-0 rounded-3 d-flex align-items-center" role="alert">
                                    <svg class="me-2" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="12" cy="12" r="10" fill="#dc3545" />
                                        <path d="M18 6L6 18M6 6l12 12" stroke="white" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </svg>
                                    <span>{{ session('error') }}</span>
                                </div>
                            @endif

                            <!-- Image Upload Section -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold">{{ __('Upload Images') }}</label>
                                <div class="upload-area border-2 border-dashed rounded-3 p-4 text-center"
                                    style="border-color: #667eea; background-color: #f8f9ff; cursor: pointer;"
                                    onclick="document.getElementById('imageInput').click()">
                                    <svg class="mb-2" width="48" height="48" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <rect x="3" y="3" width="18" height="18" rx="2" stroke="#667eea" stroke-width="2" />
                                        <circle cx="8.5" cy="8.5" r="1.5" fill="#667eea" />
                                        <path d="M3 16l5-5 3 3 6-6 4 4" stroke="#667eea" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <p class="mb-1 fw-semibold" style="color: #667eea;">{{ __('Click to upload images') }}
                                    </p>
                                    <p class="text-muted small mb-0">PNG, JPG, GIF up to 10MB</p>
                                </div>
                                <input type="file" id="imageInput" class="d-none" accept="image/*" multiple
                                    onchange="previewImages(event)">

                                <!-- Image Preview Container -->
                                <div id="imagePreview" class="row g-3 mt-3"></div>
                            </div>

                            <!-- Document Upload Section -->
                            <div>
                                <label class="form-label fw-semibold">{{ __('Upload Documents') }}</label>
                                <div class="upload-area border-2 border-dashed rounded-3 p-4 text-center"
                                    style="border-color: #764ba2; background-color: #faf8ff; cursor: pointer;"
                                    onclick="document.getElementById('documentInput').click()">
                                    <svg class="mb-2" width="48" height="48" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6z"
                                            stroke="#764ba2" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path d="M14 2v6h6M12 11v6m-3-3h6" stroke="#764ba2" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <p class="mb-1 fw-semibold" style="color: #764ba2;">
                                        {{ __('Click to upload documents') }}
                                    </p>
                                    <p class="text-muted small mb-0">PDF, DOC, DOCX up to 10MB</p>
                                </div>
                                <input type="file" id="documentInput" class="d-none" accept=".pdf,.doc,.docx" multiple
                                    onchange="previewDocuments(event)">

                                <!-- Document Preview Container -->
                                <div id="documentPreview" class="mt-3"></div>
                            </div>
                            <!-- Desired Class Selection (Level) -->
                            <div class="mt-4 border-top pt-4">
                                @php
                                    $hasApplication = false;
                                    if (auth()->check()) {
                                        $hasApplication = \App\Models\StudentApplication::where('email', auth()->user()->email)->exists();
                                    }
                                @endphp

                                @if ($hasApplication)
                                    @php
                                        $currentPreferred = \App\Models\StudentApplication::where('email', auth()->user()->email)->value('desired_class');
                                    @endphp
                                    <form method="POST" action="{{ route('verification.preference') }}">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="desired_class"
                                                class="form-label fw-semibold required">{{ __('Kelas yang Dituju') }}</label>
                                            <select id="desired_class" name="desired_class"
                                                class="form-select @error('desired_class') is-invalid @enderror" required>
                                                <option value="">{{ __('Pilih Tingkat (SD/SMP/SMA)') }}</option>
                                                <option value="SD" {{ (old('desired_class') ?? $currentPreferred) == 'SD' ? 'selected' : '' }}>SD</option>
                                                <option value="SMP" {{ (old('desired_class') ?? $currentPreferred) == 'SMP' ? 'selected' : '' }}>SMP</option>
                                                <option value="SMA" {{ (old('desired_class') ?? $currentPreferred) == 'SMA' ? 'selected' : '' }}>SMA</option>
                                            </select>
                                            @error('desired_class')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small
                                                class="text-muted">{{ __('Catatan: Pilihan tingkat ini hanya berupa indikasi. Admin akan menempatkan siswa ke kelas atau rombel yang sesuai setelah proses seleksi.') }}</small>
                                        </div>
                                        <div class="d-flex justify-content-end">
                                            <button type="submit"
                                                class="btn btn-outline-primary btn-lg rounded-pill">{{ __('Simpan Pilihan') }}</button>
                                        </div>
                                    </form>
                                @else
                                    <div class="alert alert-info">
                                        {{ __('Anda belum mengisi formulir pendaftaran. Silakan lengkapi pendaftaran sebelum memilih tingkat. ') }}
                                        <a class="fw-semibold"
                                            href="{{ route('student.register') }}">{{ __('Isi Formulir Pendaftaran') }}</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .upload-area {
            transition: all 0.3s ease;
        }

        .upload-area:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
        }

        .preview-card {
            position: relative;
            border-radius: 0.75rem;
            overflow: hidden;
            transition: transform 0.2s ease;
        }

        .preview-card:hover {
            transform: scale(1.05);
        }

        .preview-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .remove-btn {
            position: absolute;
            top: 8px;
            right: 8px;
            background: rgba(220, 53, 69, 0.9);
            color: white;
            border: none;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .remove-btn:hover {
            background: #dc3545;
            transform: scale(1.1);
        }

        .doc-preview-item {
            display: flex;
            align-items: center;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 0.75rem;
            margin-bottom: 0.75rem;
            transition: all 0.2s ease;
        }

        .doc-preview-item:hover {
            background: #e9ecef;
            transform: translateX(4px);
        }

        .doc-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            flex-shrink: 0;
        }

        /* Required field marker for labels */
        .required:after {
            content: " *";
            color: red;
        }
    </style>

    <script>
        function previewImages(event) {
            const container = document.getElementById('imagePreview');
            const files = event.target.files;

            Array.from(files).forEach((file, index) => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();

                    reader.onload = function (e) {
                        const col = document.createElement('div');
                        col.className = 'col-md-4 col-sm-6';

                        col.innerHTML = `
                                <div class="preview-card shadow-sm">
                                    <img src="${e.target.result}" alt="Preview">
                                    <button type="button" class="remove-btn" onclick="this.closest('.col-md-4').remove()">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        </svg>
                                    </button>
                                    <div class="p-2 bg-white">
                                        <small class="text-truncate d-block">${file.name}</small>
                                        <small class="text-muted">${(file.size / 1024).toFixed(2)} KB</small>
                                    </div>
                                </div>
                            `;

                        container.appendChild(col);
                    };

                    reader.readAsDataURL(file);
                }
            });
        }

        function previewDocuments(event) {
            const container = document.getElementById('documentPreview');
            const files = event.target.files;

            Array.from(files).forEach(file => {
                const docItem = document.createElement('div');
                docItem.className = 'doc-preview-item';

                const fileExt = file.name.split('.').pop().toLowerCase();
                const fileIcon = fileExt === 'pdf' ? 'PDF' : 'DOC';

                docItem.innerHTML = `
                        <div class="doc-icon">
                            <span class="text-white fw-bold">${fileIcon}</span>
                        </div>
                        <div class="flex-grow-1">
                            <p class="mb-1 fw-semibold">${file.name}</p>
                            <small class="text-muted">${(file.size / 1024).toFixed(2)} KB</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill" onclick="this.closest('.doc-preview-item').remove()">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </button>
                    `;

                container.appendChild(docItem);
            });
        }
    </script>
@endsection