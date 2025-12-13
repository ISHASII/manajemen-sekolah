@extends('layouts.admin')

@section('title', 'Jadwal')

@section('admin-content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Jadwal</h3>
            <a href="{{ route('admin.schedules.create') }}" class="btn btn-primary">Buat Jadwal</a>
        </div>

        <div class="card">
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                @if($schedules && $schedules->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Kelas</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Guru</th>
                                    <th>Hari</th>
                                    <th>Waktu</th>
                                    <th>Ruang</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($schedules as $schedule)
                                    <tr>
                                        <td>{{ optional($schedule->classRoom)->name ?? '-' }}</td>
                                        <td>{{ optional($schedule->subject)->name ?? '-' }}</td>
                                        <td>{{ optional($schedule->teacher)->name ?? '-' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($schedule->day_of_week)->translatedFormat('l') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} -
                                            {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</td>
                                        <td>{{ $schedule->room ?? '-' }}</td>
                                        <td>{{ $schedule->is_active ? 'Aktif' : 'Nonaktif' }}</td>
                                        <td>
                                            <a href="{{ route('admin.schedules.edit', $schedule->id) }}"
                                                class="btn btn-sm btn-outline-primary">Edit</a>
                                            <form method="POST" action="{{ route('admin.schedules.toggle', $schedule->id) }}" class="d-inline-block ms-1">
                                                @csrf
                                                @method('PATCH')
                                                <button class="btn btn-sm {{ $schedule->is_active ? 'btn-outline-success' : 'btn-outline-secondary' }}">
                                                    {{ $schedule->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.schedules.destroy', $schedule->id) }}"
                                                class="d-inline-block" onsubmit="return confirm('Hapus jadwal ini?');">
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
                    <div class="mt-3">
                        {{ $schedules->links() }}
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-calendar3 text-muted" style="font-size: 3rem"></i>
                        <p class="text-muted mt-2">Belum ada jadwal terdaftar.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
