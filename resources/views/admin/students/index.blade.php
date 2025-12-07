@extends('layouts.app')

@section('title', 'Kelola Siswa')

@section('content')
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
                                        <td>{{ $st->user->name ?? '-' }}</td>
                                        <td>{{ $st->user->email ?? '-' }}</td>
                                        <td>{{ optional($st->classRoom)->name ?? '-' }}</td>
                                        <td>{{ ucfirst($st->status ?? 'inactive') }}</td>
                                        <td>
                                            <a href="{{ route('admin.students.edit', $st->id) }}"
                                                class="btn btn-sm btn-outline-primary">Detail</a>
                                            <form method="POST" action="{{ route('admin.students.destroy', $st->id) }}"
                                                class="d-inline-block" onsubmit="return confirm('Hapus siswa ini?');">
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