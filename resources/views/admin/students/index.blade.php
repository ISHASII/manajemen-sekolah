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
                @if($students && $students->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Kelas</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($students as $st)
                                    <tr>
                                        <td>{{ $st->name ?? '-' }}</td>
                                        <td>{{ $st->email ?? '-' }}</td>
                                        <td>{{ $st->student?->classRoom?->name ?? '-' }}</td>
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