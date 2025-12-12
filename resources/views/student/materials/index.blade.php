@extends('layouts.app')

@section('title', 'Materi Kelas')

@section('content')
    <div class="container py-4">
        @php $filterTrainingClassId = request()->query('training_class_id'); @endphp
        <h3>Materi Kelas @if($filterTrainingClassId) untuk Pelatihan: {{ \App\Models\TrainingClass::find($filterTrainingClassId)?->title ?? 'N/A' }} @endif</h3>
        <div class="card mt-3 p-4" style="background-color: #ffffff; color: #000;">

            @if(session('success'))
                <div class="alert alert-success" style="color:#000;">{{ session('success') }}</div>
            @endif

            @if($materials && $materials->count())
                @php
                    $student = \App\Models\Student::where('user_id', auth()->id())->with('classRoom')->first();
                @endphp
                <div class="list-group">
                    @foreach($materials as $m)
                        <div class="list-group-item d-flex justify-content-between align-items-center" style="color:#000;">

                            <div>
                                <strong style="color:#000;">{{ $m->title }}</strong>

                                <div class="small" style="color: #555 !important;">
                                    {{ $m->trainingClass?->title ?? ($m->classRoom?->name ?? 'Semua') }} —
                                    {{ $m->subject?->name ?? '-' }}
                                </div>
                            </div>

                            <div>
                                @if($m->file_type === 'link')
                                    <a class="btn btn-sm btn-outline-secondary" style="color:#000; border-color:#000;"
                                        href="{{ $m->file_path }}" target="_blank">Buka Link</a>
                                @else
                                    <a class="btn btn-sm btn-outline-secondary" style="color:#000; border-color:#000;"
                                        href="{{ Storage::url($m->file_path) }}" target="_blank">Lihat/Unduh</a>
                                @endif
                                @php
                                    $canSubmit = true;
                                    if ($m->class_id && $student && $student->class_id !== $m->class_id) {
                                        $canSubmit = false;
                                    }
                                    if ($m->training_class_id && $student) {
                                        $canSubmit = $student->trainingClasses()->where('training_classes.id', $m->training_class_id)->exists();
                                    }
                                    $mySubmission = $m->submissions->first() ?? null;
                                @endphp
                                @if($canSubmit && $m->is_visible)
                                    @if($mySubmission)
                                        <button type="button" class="btn btn-sm btn-outline-primary ms-2 edit-submission-btn"
                                            style="color:#000; border-color:#000;" data-bs-toggle="modal"
                                            data-bs-target="#submitModal"
                                            data-material-id="{{ $m->id }}"
                                            data-submission-id="{{ $mySubmission->id }}"
                                            data-submission-link="{{ $mySubmission->link ?? '' }}"
                                            data-submission-description="{{ e($mySubmission->description) }}"
                                            data-submission-fileurl="{{ $mySubmission->file_path ? Storage::url($mySubmission->file_path) : '' }}">
                                            Edit Tugas
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-sm btn-primary ms-2 submit-new-btn"
                                            style="background-color:#000; border-color:#000; color:#fff;" data-bs-toggle="modal"
                                            data-bs-target="#submitModal" data-material-id="{{ $m->id }}">
                                            Kirim Tugas
                                        </button>
                                    @endif
                                @endif
                                @if(!$m->is_visible)
                                    <span class="badge bg-secondary ms-2">Belum dipublikasikan</span>
                                @endif
                            </div>

                        </div>
                    @endforeach
                </div>

                <div class="mt-3" style="color:#000;">
                    {{ $materials->links() }}
                </div>

            @else
                @if(isset($isKejuruan) && $isKejuruan)
                    <div class="text-muted" style="color:#000 !important;">Tidak ada materi pelatihan yang tersedia untuk pelatihan
                        Anda.</div>
                @else
                    <div class="text-muted" style="color:#000 !important;">Tidak ada materi yang tersedia untuk kelas Anda.</div>
                @endif
            @endif
        </div>
    </div>
@endsection

@section('scripts')
    <!-- Submission modal -->
    <style>
        /* Make this modal appear lower on the screen (scoped) */
        #submitModal .modal-dialog {
            margin-top: 18vh;
        }

        @media (max-width: 576px) {
            #submitModal .modal-dialog {
                margin-top: 12vh;
                margin-left: 0.5rem;
                margin-right: 0.5rem;
            }
        }
    </style>
    <div class="modal fade" id="submitModal" tabindex="-1" role="dialog" aria-labelledby="submitModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                @php
                    $routePrefix = auth()->user()->role === 'kejuruan' ? 'kejuruan' : 'student';
                @endphp
                <form method="POST" action="{{ route($routePrefix . '.materials.submissions.store', ['material' => 0]) }}"
                    enctype="multipart/form-data" id="submissionForm">
                    @csrf
                    <input type="hidden" name="_method" id="submissionFormMethod" value="POST">
                    <input type="hidden" name="submission_id" id="submissionId" value="">
                    <input type="hidden" name="material_id" id="submissionMaterialId" value="">
                    <div class="modal-header">
                        <h5 class="modal-title" id="submitModalLabel">Kirim Tugas</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="currentSubmissionInfo" class="small text-muted mb-2"></div>
                        <div class="form-group">
                            <label for="file">Unggah Berkas (opsional)</label>
                            <input type="file" class="form-control" name="file" id="file" />
                        </div>
                        <div class="form-group">
                            <label for="link">atau Link (opsional)</label>
                            <input type="url" name="link" id="link" class="form-control"
                                placeholder="https://example.com" />
                        </div>
                        <div class="form-group">
                            <label for="description">Deskripsi (opsional)</label>
                            <textarea name="description" id="description" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary"
                            style="background-color:#000; border-color:#000;">Kirim</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        (function () {
            var modalEl = document.getElementById('submitModal');
            var submissionForm = document.getElementById('submissionForm');
            var defaultAction = submissionForm ? submissionForm.getAttribute('action') : '';

                function setFormActionForMaterial(materialId) {
                if (!submissionForm) return;
                var action = submissionForm.getAttribute('action') || defaultAction;
                var newAction = action.replace(/materials\/[0-9]+\//, 'materials/' + materialId + '/');
                submissionForm.setAttribute('action', newAction);
                // ensure method is POST for new submissions
                var methodInput = document.getElementById('submissionFormMethod');
                if (methodInput) methodInput.value = 'POST';
                var submissionIdInput = document.getElementById('submissionId');
                if (submissionIdInput) submissionIdInput.value = '';
                // reset fields
                var linkInput = document.getElementById('link');
                var descriptionInput = document.getElementById('description');
                var fileInput = document.getElementById('file');
                if (linkInput) linkInput.value = '';
                if (descriptionInput) descriptionInput.value = '';
                if (fileInput) fileInput.value = '';
                // set submit button label
                var submitBtn = submissionForm.querySelector('button[type="submit"]');
                if (submitBtn) submitBtn.textContent = 'Kirim';
                var modalLabel = document.getElementById('submitModalLabel');
                if (modalLabel) modalLabel.textContent = 'Kirim Tugas';
            }

            if (modalEl) {
                // Vanilla JS: Bootstrap 5 event listener
                modalEl.addEventListener('show.bs.modal', function (event) {
                    var button = event.relatedTarget;
                    if (!button) return;
                        var materialId = button.getAttribute('data-material-id');
                        var submissionId = button.getAttribute('data-submission-id') || '';
                        var submissionLink = button.getAttribute('data-submission-link') || '';
                            var submissionDescription = button.getAttribute('data-submission-description') || '';
                            var submissionFileUrl = button.getAttribute('data-submission-fileurl') || '';
                        setFormActionForMaterial(materialId);
                        if (submissionId) {
                            // Switch to update mode
                            var action = submissionForm.getAttribute('action') || defaultAction;
                            // replace existing /materials/{id}/submissions with /materials/{id}/submissions/{submissionId}
                            var newAction = action.replace(/materials\/\d+\/submissions(\/\d+)?$/, 'materials/' + materialId + '/submissions/' + submissionId);
                            submissionForm.setAttribute('action', newAction);
                            var methodInput = document.getElementById('submissionFormMethod');
                            if (methodInput) methodInput.value = 'PATCH';
                            var submissionIdInput = document.getElementById('submissionId');
                            if (submissionIdInput) submissionIdInput.value = submissionId;
                            // populate link & description
                            var linkInput = document.getElementById('link');
                            var descriptionInput = document.getElementById('description');
                            var fileInput = document.getElementById('file');
                            if (linkInput) linkInput.value = submissionLink;
                                if (linkInput) linkInput.value = submissionLink;
                                if (descriptionInput) descriptionInput.value = submissionDescription;
                                var currentInfoEl = document.getElementById('currentSubmissionInfo');
                                if (currentInfoEl) {
                                    var infoHtml = '';
                                    if (submissionFileUrl) {
                                        infoHtml += '<div>File saat ini: <a href="' + submissionFileUrl + '" target="_blank">Lihat/Unduh</a></div>';
                                    }
                                    if (submissionLink) {
                                        infoHtml += '<div>Link saat ini: <a href="' + submissionLink + '" target="_blank">' + submissionLink + '</a></div>';
                                    }
                                    if (submissionDescription) {
                                        infoHtml += '<div>Deskripsi: ' + submissionDescription + '</div>';
                                    }
                                    currentInfoEl.innerHTML = infoHtml;
                                }
                            if (fileInput) fileInput.value = '';
                            var submitBtn = submissionForm.querySelector('button[type="submit"]');
                            if (submitBtn) submitBtn.textContent = 'Perbarui';
                            var modalLabel = document.getElementById('submitModalLabel');
                            if (modalLabel) modalLabel.textContent = 'Edit Tugas';
                        }
                });

                modalEl.addEventListener('hidden.bs.modal', function () {
                    if (submissionForm) {
                        submissionForm.reset();
                        submissionForm.setAttribute('action', defaultAction);
                        var methodInput = document.getElementById('submissionFormMethod');
                        if (methodInput) methodInput.value = 'POST';
                        var submissionIdInput = document.getElementById('submissionId');
                        if (submissionIdInput) submissionIdInput.value = '';
                        var currentInfoEl = document.getElementById('currentSubmissionInfo');
                        if (currentInfoEl) currentInfoEl.innerHTML = '';
                        var modalLabel = document.getElementById('submitModalLabel');
                        if (modalLabel) modalLabel.textContent = 'Kirim Tugas';
                        var submitBtn = submissionForm.querySelector('button[type="submit"]');
                        if (submitBtn) submitBtn.textContent = 'Kirim';
                    }
                });
            }

            // jQuery fallback if loaded (for older scripts or plugins expecting jQuery)
            if (window.jQuery && typeof window.jQuery === 'function') {
                $('#submitModal').on('show.bs.modal', function (event) {
                    var button = $(event.relatedTarget);
                    var materialId = button.data('material-id');
                    var submissionId = button.data('submission-id') || '';
                    var submissionLink = button.data('submission-link') || '';
                        var submissionFileUrl = button.data('submission-fileurl') || '';
                        var submissionDescription = button.data('submission-description') || '';
                    setFormActionForMaterial(materialId);
                    if (submissionId) {
                        var action = $(submissionForm).attr('action') || defaultAction;
                        var newAction = action.replace(/materials\/\d+\/submissions(\/\d+)?$/, 'materials/' + materialId + '/submissions/' + submissionId);
                        $(submissionForm).attr('action', newAction);
                        $('#submissionFormMethod').val('PATCH');
                        $('#submissionId').val(submissionId);
                        $('#link').val(submissionLink);
                            $('#link').val(submissionLink);
                            $('#description').val(submissionDescription);
                            var infoHtml = '';
                            if (submissionFileUrl) {
                                infoHtml += '<div>File saat ini: <a href="' + submissionFileUrl + '" target="_blank">Lihat/Unduh</a></div>';
                            }
                            if (submissionLink) {
                                infoHtml += '<div>Link saat ini: <a href="' + submissionLink + '" target="_blank">' + submissionLink + '</a></div>';
                            }
                            if (submissionDescription) {
                                infoHtml += '<div>Deskripsi: ' + submissionDescription + '</div>';
                            }
                            $('#currentSubmissionInfo').html(infoHtml);
                        $(submissionForm).find('button[type="submit"]').text('Perbarui');
                        $('#submitModalLabel').text('Edit Tugas');
                    }
                });

                $('#submitModal').on('hidden.bs.modal', function () {
                    if (submissionForm) {
                        submissionForm.reset();
                        $(submissionForm).attr('action', defaultAction);
                        $('#submissionFormMethod').val('POST');
                        $('#submissionId').val('');
                        $('#currentSubmissionInfo').html('');
                        $('#submitModalLabel').text('Kirim Tugas');
                        $(submissionForm).find('button[type="submit"]').text('Kirim');
                    }
                });
            }
        })();
    </script>
@endsection
