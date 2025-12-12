@extends('layouts.app')

@section('title', 'Jadwal Mengajar')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Jadwal Mengajar</h2>
        </div>

        @if(($schedules ?? collect())->count() === 0)
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <p class="mb-0 text-muted">Belum ada jadwal mengajar.</p>
                </div>
            </div>
        @else
            @foreach($schedules as $day => $daySchedules)
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-warning border-bottom">
                        <h5 class="mb-0 text-capitalize">{{ $day }}</h5>
                    </div>
                    <div class="card-body">
                        @foreach($daySchedules as $sch)
                            <div class="d-flex align-items-center mb-2">
                                <div class="flex-grow-1">
                                    <strong>{{ optional($sch->classRoom)->name ?? '-' }} -
                                        {{ optional($sch->subject)->name ?? '-' }}</strong>
                                    <div class="small text-muted">{{ \Carbon\Carbon::parse($sch->start_time)->format('H:i') }} -
                                        {{ \Carbon\Carbon::parse($sch->end_time)->format('H:i') }} • {{ $sch->room ?? '-' }}
                                    </div>
                                </div>
                                <div class="text-end">
                                    <a href="{{ route('teacher.students') }}" class="btn btn-outline-primary btn-sm">Daftar Siswa</a>
                                    <a href="{{ route('teacher.class.materials', ['classId' => optional($sch->classRoom)->id]) }}"
                                        class="btn btn-sm btn-primary ms-2">
                                        <i class="bi bi-folder me-1"></i>Materi Kelas
                                    </a>
                                </div>
                            </div>
                            <div>
                                @php
                                    $classId = optional($sch->classRoom)->id;
                                    $materialsForClass = collect();
                                    if (isset($materialsByClass) && is_object($materialsByClass)) {
                                        $materialsForClass = $materialsByClass->get($classId) ?? collect();
                                    }
                                @endphp
                                @if($materialsForClass && $materialsForClass->count() > 0)
                                    <div class="mt-2">
                                        <strong>Materi:</strong>
                                        <ul class="list-unstyled small mb-0">
                                            @foreach($materialsForClass as $mat)
                                                <li class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        @if($mat->file_type === 'link')
                                                            <a href="{{ $mat->file_path }}" target="_blank">{{ $mat->title }}</a>
                                                        @else
                                                            <a href="{{ Storage::url($mat->file_path) }}" target="_blank">{{ $mat->title }}</a>
                                                        @endif
                                                        <small class="text-muted"> - {{ optional($mat->subject)->name ?? '' }}</small>
                                                    </div>
                                                    <div>
                                                        <a href="{{ route('teacher.materials.edit', $mat->id) }}"
                                                            class="btn btn-sm btn-outline-secondary me-1">Edit</a>
                                                        <form action="{{ route('teacher.materials.destroy', $mat->id) }}" method="POST"
                                                            style="display:inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button class="btn btn-sm btn-outline-danger"
                                                                onclick="return confirm('Yakin hapus materi ini?')">Hapus</button>
                                                        </form>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                            <hr />
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endif
    </div>
@endsection