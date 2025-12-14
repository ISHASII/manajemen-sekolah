@extends('layouts.app')

@section('title', 'Rekap Absensi Kelas')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h3>Rekap Absensi - {{ $classRoom->name }}</h3>
                <div class="small text-muted">Tanggal: {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('d F Y') }}
                </div>
                @if(isset($selectedSubject) && $selectedSubject)
                    <div class="small text-muted">Pelajaran:
                        {{ optional($subjects->firstWhere('id', $selectedSubject))->name ?? '-' }}</div>
                @endif
            </div>
            <div>
                <a href="{{ route('teacher.class.detail', $classRoom->id) }}" class="btn btn-outline-secondary">Kembali</a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form method="GET" class="mb-3">
                    <input type="hidden" name="mode" value="daily">
                    <div class="row g-2 align-items-center">
                        <div class="col-auto">
                            <input type="date" name="date" value="{{ $selectedDate }}" class="form-control">
                        </div>
                        <div class="col-auto">
                            <select name="subject_id" class="form-select">
                                <option value="">-- Semua Mata Pelajaran --</option>
                                @foreach($subjects as $sub)
                                    <option value="{{ $sub->id }}" {{ (isset($selectedSubject) && $selectedSubject == $sub->id) ? 'selected' : '' }}>{{ $sub->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-primary">Tampilkan</button>
                        </div>
                    </div>
                </form>

                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama</th>
                            <th>Status</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($classRoom->students as $idx => $student)
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td>{{ $student->user->name }}</td>
                                <td>
                                    @php $att = $attendances[$student->id] ?? null; @endphp
                                    @if($att)
                                        <span class="badge bg-info">{{ ucfirst($att->status) }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $att?->notes ?? '' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection