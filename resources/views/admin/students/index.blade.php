@extends('layouts.admin')

@section('title', 'Kelola Siswa')

@section('admin-content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Daftar Siswa</h3>
            <div>
                <a href="{{ route('admin.students.create') }}" class="btn btn-primary">Tambah Siswa</a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ $filterAction ?? route('admin.students.index') }}" class="mb-3">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">Nama</label>
                            <input type="text" name="name" value="{{ request('name') }}" class="form-control"
                                placeholder="Cari nama siswa...">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Status Yatim/Piatu</label>
                            <select name="orphan_status" class="form-select">
                                <option value="">Semua</option>
                                <option value="none" {{ request('orphan_status') === 'none' ? 'selected' : '' }}>Tidak
                                </option>
                                <option value="yatim" {{ request('orphan_status') === 'yatim' ? 'selected' : '' }}>Yatim
                                </option>
                                <option value="piatu" {{ request('orphan_status') === 'piatu' ? 'selected' : '' }}>Piatu
                                </option>
                                <option value="yatim_piatu" {{ request('orphan_status') === 'yatim_piatu' ? 'selected' : '' }}>Yatim & Piatu</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            @if(isset($isKejuruan) && $isKejuruan)
                                <label class="form-label">Pelatihan</label>
                                <select name="training_class_id" class="form-select">
                                    <option value="">Semua</option>
                                    @if(isset($trainingClasses))
                                        @foreach($trainingClasses as $tc)
                                            <option value="{{ $tc->id }}" {{ request('training_class_id') == $tc->id ? 'selected' : '' }}>
                                                {{ $tc->title }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            @else
                                <label class="form-label">Kelas</label>
                                <select name="class_id" class="form-select">
                                    <option value="">Semua</option>
                                    @if(isset($classes))
                                        @foreach($classes as $c)
                                            <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>
                                                {{ $c->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            @endif
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Punya Disabilitas</label>
                            <select name="has_disability" class="form-select">
                                <option value="">Semua</option>
                                <option value="1" {{ request('has_disability') === '1' ? 'selected' : '' }}>Ya</option>
                                <option value="0" {{ request('has_disability') === '0' ? 'selected' : '' }}>Tidak</option>
                            </select>
                        </div>

                        <div class="col-12 col-md-12 col-lg-12 text-end mt-2">
                            <button type="submit" class="btn btn-primary me-2">Filter</button>
                            <a href="{{ $filterAction ?? route('admin.students.index') }}"
                                class="btn btn-outline-secondary">Reset</a>
                        </div>
                    </div>
                </form>
                @if($students && $students->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    @if(isset($isKejuruan) && $isKejuruan)
                                        <th>Pelatihan</th>
                                    @else
                                        <th>Kelas</th>
                                    @endif
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($students as $st)
                                    <tr>
                                        <td>{{ $st->name ?? '-' }}</td>
                                        <td>{{ $st->email ?? '-' }}</td>
                                        @if(isset($isKejuruan) && $isKejuruan)
                                            <td>
                                                @php
                                                    $training = $st->student?->trainingClasses?->filter(function ($t) {
                                                        $pivotStatus = $t->pivot->status ?? null;
                                                        $now = \Carbon\Carbon::now();
                                                        $endAt = $t->end_at ? \Carbon\Carbon::parse($t->end_at) : null;
                                                        return $pivotStatus === 'enrolled' && (is_null($endAt) || $endAt->greaterThanOrEqualTo($now));
                                                    })->first();
                                                @endphp
                                                @if($training)
                                                    {{ $training->title }}
                                                @else
                                                    <span class="text-muted">Tidak Mengikuti Pelatihan</span>
                                                @endif
                                            </td>
                                        @else
                                            <td>{{ $st->student?->classRoom?->name ?? '-' }}</td>
                                        @endif
                                        <td>{{ ucfirst($st->student?->status ?? 'no-profile') }}</td>
                                        <td>
                                            @if(optional($st->student)->id)
                                                <a href="{{ route('admin.students.edit', $st->student->id) }}{{ isset($isKejuruan) && $isKejuruan ? '?return_to=kejuruan' : '' }}"
                                                    class="btn btn-sm btn-outline-primary btn-detail">Detail</a>
                                                <a href="{{ route('admin.students.education-history', $st->student->id) }}"
                                                    class="btn btn-sm btn-outline-info">Riwayat Pendidikan</a>
                                                <button type="button" class="btn btn-sm btn-outline-secondary btn-generate-id"
                                                    data-bs-toggle="modal" data-bs-target="#adminIdCardModal"
                                                    data-student-name="{{ e($st->name) }}"
                                                    data-student-number="{{ e($st->student->student_id ?? '-') }}"
                                                    data-student-id="{{ $st->student->id }}"
                                                    data-address="{{ e($st->student->address ?: ($st->student->user->address ?? '-')) }}"
                                                    data-phone="{{ e($st->student->user->phone ?: '-') }}"
                                                    data-gender="{{ e($st->student->user->gender === 'male' ? 'Laki-laki' : ($st->student->user->gender === 'female' ? 'Perempuan' : '-')) }}"
                                                    data-profile-photo="{{ $st->student->user->profile_photo ? Storage::url($st->student->user->profile_photo) : '' }}"
                                                    data-disability="{{ e(($st->student->disability_info && is_array($st->student->disability_info) && count($st->student->disability_info)) ? implode(', ', $st->student->disability_info) : 'Tidak ada') }}"
                                                    data-public-url="{{ url('/students/public/'.$st->student->id) }}">
                                                    <i class="fas fa-id-card"></i> ID Card
                                                </button>
                                            @else
                                                <a href="{{ route('admin.students.create', ['user_id' => $st->id]) }}"
                                                    class="btn btn-sm btn-outline-primary btn-detail">Buat Profil</a>
                                            @endif
                                            @if(optional($st->student)->id)
                                                <form method="POST" action="{{ route('admin.students.destroy', $st->student->id) }}"
                                                    class="d-inline-block" onsubmit="return confirm('Hapus siswa ini?');">
                                            @else
                                                    <form method="POST" action="{{ route('admin.users.destroy', $st->id) }}"
                                                        class="d-inline-block" onsubmit="return confirm('Hapus user ini?');">
                                                @endif
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-sm btn-outline-danger">Hapus</button>
                                                </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $students->links() }}
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-2">Belum ada siswa terdaftar.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Admin ID Card Modal -->
    <div class="modal fade" id="adminIdCardModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">ID Card Siswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body d-flex justify-content-center">
                    <div id="adminStudentIdCard" style="width:360px; padding:16px; border-radius:8px; background:#fff; box-shadow:0 2px 8px rgba(0,0,0,0.08); font-family:Arial, sans-serif; color:#111;">
                        <div style="display:flex; align-items:center; gap:12px;">
                            <div id="adminCardPhoto" style="width:80px; height:80px; border-radius:8px; overflow:hidden; background:#e9ecef; display:flex; align-items:center; justify-content:center;">
                            </div>
                            <div style="flex:1;">
                                <div style="font-weight:700; font-size:18px;" id="adminCardName">Nama Siswa</div>
                                <div style="font-size:12px; color:#6b7280;" id="adminCardStudentId">ID: -</div>
                            </div>
                            <div style="width:110px; text-align:center;">
                                <div id="adminCardQr" style="width:100px; height:100px;"></div>
                            </div>
                        </div>
                        <hr style="margin:12px 0; border-color:#e5e7eb;">
                        <div style="margin-top:12px; font-size:13px; color:#374151;">
                            <div style="margin-bottom:6px;"><strong>ID:</strong> <span id="adminCardStudentIdText">-</span></div>
                            <div style="margin-bottom:6px;"><strong>Nama:</strong> <span id="adminCardNameText">-</span></div>
                            <div style="margin-bottom:6px;"><strong>Alamat:</strong> <span id="adminCardAddress">-</span></div>
                            <div style="margin-bottom:6px;"><strong>No Telepon:</strong> <span id="adminCardPhone">-</span></div>
                            <div style="margin-bottom:6px;"><strong>Jenis Kelamin:</strong> <span id="adminCardGender">-</span></div>
                            <div style="margin-bottom:6px;"><strong>Disabilitas:</strong> <span id="adminCardDisability">-</span></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" id="adminDownloadIdCardBtn">Download PNG</button>
                    <button type="button" class="btn btn-outline-secondary" id="adminPrintIdCardBtn">Print</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('adminIdCardModal');
        const qrContainer = document.getElementById('adminCardQr');
        let currentPublicUrl = '';

        // Populate modal fields when clicking 'Generate ID' buttons
        document.querySelectorAll('.btn-generate-id').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const name = btn.dataset.studentName || '';
                const studentNumber = btn.dataset.studentNumber || '';
                const address = btn.dataset.address || '-';
                const phone = btn.dataset.phone || '-';
                const gender = btn.dataset.gender || '-';
                const profilePhoto = btn.dataset.profilePhoto || '';
                const disability = btn.dataset.disability || 'Tidak ada';
                currentPublicUrl = btn.dataset.publicUrl || window.location.href;

                document.getElementById('adminCardName').textContent = name;
                document.getElementById('adminCardStudentId').textContent = 'ID: ' + studentNumber;
                document.getElementById('adminCardStudentIdText').textContent = studentNumber;
                document.getElementById('adminCardNameText').textContent = name;
                document.getElementById('adminCardAddress').textContent = address;
                document.getElementById('adminCardPhone').textContent = phone;
                document.getElementById('adminCardGender').textContent = gender;
                document.getElementById('adminCardDisability').textContent = disability;
                const photoEl = document.getElementById('adminCardPhoto');
                if (profilePhoto) {
                    photoEl.innerHTML = '<img src="' + profilePhoto + '" style="width:80px; height:80px; object-fit:cover;">';
                } else {
                    photoEl.innerHTML = '<i class="fas fa-user fa-2x text-dark"></i>';
                }

                // Reset QR container and generate new QR
                if (qrContainer) {
                    qrContainer.innerHTML = '';
                    new QRCode(qrContainer, {
                        text: currentPublicUrl,
                        width: 100,
                        height: 100,
                        colorDark: '#000000',
                        colorLight: '#ffffff',
                        correctLevel: QRCode.CorrectLevel.H,
                    });
                }
            });
        });

        // Download handler
        const downloadBtn = document.getElementById('adminDownloadIdCardBtn');
        const printBtn = document.getElementById('adminPrintIdCardBtn');
        const targetEl = document.getElementById('adminStudentIdCard');

        if (downloadBtn) {
            downloadBtn.addEventListener('click', function () {
                html2canvas(targetEl).then(function (canvas) {
                    const dataUrl = canvas.toDataURL('image/png');
                    const link = document.createElement('a');
                    link.href = dataUrl;
                    const name = document.getElementById('adminCardStudentIdText').textContent || 'student-id';
                    link.download = 'student-id-' + name + '.png';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                });
            });
        }

        if (printBtn) {
            printBtn.addEventListener('click', function () {
                html2canvas(targetEl).then(function (canvas) {
                    const dataUrl = canvas.toDataURL('image/png');
                    const w = window.open('', '_blank');
                    w.document.write('<html><head><title>ID Card</title></head><body style="margin:0; padding:20px; display:flex; justify-content:center;">');
                    w.document.write('<img src="' + dataUrl + '" style="max-width:100%;">');
                    w.document.write('</body></html>');
                    w.document.close();
                    w.focus();
                    setTimeout(function(){ w.print(); w.close(); }, 500);
                });
            });
        }
    });
</script>
@endpush
