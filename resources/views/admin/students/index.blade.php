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
                            <label class="form-label">Pelatihan</label>
                            <select name="training_class_id" class="form-select">
                                <option value="">Semua</option>
                                @if(isset($trainingClasses))
                                    @foreach($trainingClasses as $tc)
                                        <option value="{{ $tc->id }}" {{ request('training_class_id') == $tc->id ? 'selected' : '' }}>
                                            {{ $tc->title }}</option>
                                    @endforeach
                                @endif
                            </select>
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
                                                <a href="{{ route('admin.students.edit', $st->student->id) }}"
                                                    class="btn btn-sm btn-outline-primary btn-detail">Detail</a>
                                                <a href="{{ route('admin.students.education-history', $st->student->id) }}"
                                                    class="btn btn-sm btn-outline-info">Riwayat Pendidikan</a>
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
@endsection