@extends('layouts.app')

@section('title', 'Rekap Absensi Bulanan')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h3>Rekap Absensi Bulanan - {{ $classRoom->name }}</h3>
                <div class="small text-muted">Periode: {{ $start->translatedFormat('F Y') }}</div>
            </div>
            <div>
                <a href="{{ route('teacher.class.detail', $classRoom->id) }}" class="btn btn-outline-secondary">Kembali</a>
                <a href="{{ route('teacher.class.attendance', $classRoom->id) }}" class="btn btn-primary">Lihat Harian</a>
            </div>
        </div>

        <div class="card">
            <div class="card-body table-responsive">
                <form method="GET" class="mb-3 row g-2 align-items-center">
                    <input type="hidden" name="mode" value="monthly">
                    <div class="col-auto">
                        <input type="month" name="month" value="{{ $start->format('Y-m') }}" class="form-control">
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
                </form>

                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            @foreach($dates as $d)
                                <th class="text-center" style="min-width:40px">{{ \Carbon\Carbon::parse($d)->format('j') }}</th>
                            @endforeach
                            <th class="text-center">H</th>
                            <th class="text-center">A</th>
                            <th class="text-center">S</th>
                            <th class="text-center">I</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($classRoom->students as $student)
                            <tr>
                                <td>{{ $student->user->name }}</td>
                                @php
                                    $present = $absent = $sick = $excused = 0;
                                @endphp
                                @foreach($dates as $d)
                                    @php $st = $map[$student->id][$d] ?? null; @endphp
                                    <td class="text-center">
                                        @if(is_array($st))
                                            @foreach($st as $s)
                                                @if($s === 'present')
                                                    <span class="text-success">P</span>
                                                    @php $present++; @endphp
                                                @elseif($s === 'absent')
                                                    <span class="text-danger">A</span>
                                                    @php $absent++; @endphp
                                                @elseif($s === 'sick')
                                                    <span class="text-warning">S</span>
                                                    @php $sick++; @endphp
                                                @elseif($s === 'excused')
                                                    <span class="text-info">I</span>
                                                    @php $excused++; @endphp
                                                @endif
                                            @endforeach
                                        @else
                                            @if($st === 'present')
                                                <span class="text-success">P</span>
                                                @php $present++; @endphp
                                            @elseif($st === 'absent')
                                                <span class="text-danger">A</span>
                                                @php $absent++; @endphp
                                            @elseif($st === 'sick')
                                                <span class="text-warning">S</span>
                                                @php $sick++; @endphp
                                            @elseif($st === 'excused')
                                                <span class="text-info">I</span>
                                                @php $excused++; @endphp
                                            @else
                                                -
                                            @endif
                                        @endif
                                    </td>
                                @endforeach
                                <td class="text-center">{{ $present }}</td>
                                <td class="text-center">{{ $absent }}</td>
                                <td class="text-center">{{ $sick }}</td>
                                <td class="text-center">{{ $excused }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection