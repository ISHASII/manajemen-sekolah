@extends('layouts.admin')

@section('title', 'Kelola Kelas')

@section('admin-content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Daftar Kelas</h3>
            <div>
                <a href="{{ route('admin.classes.create') }}" class="btn btn-primary">Buat Kelas</a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                @if($classes && $classes->count() > 0)
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama</th>
                                    <th>Wali Kelas</th>
                                    <th>Jumlah Siswa</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($classes as $class)
                                    <tr>
                                        <td>{{ $class->name }}</td>
                                        <td>{{ optional($class->homeroomTeacher)->name ?? '-' }}</td>
                                        <td>{{ $class->current_students ?? 0 }}</td>
                                        <td>
                                            <span class="badge {{ $class->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $class->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                        </td>
                                            <td>
                                                <a href="{{ route('admin.classes.edit', $class->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                                <form method="POST" action="{{ route('admin.classes.destroy', $class->id) }}" class="d-inline-block" onsubmit="return confirm('Hapus kelas ini? Pastikan tidak ada siswa pada kelas ini.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-sm btn-outline-danger">Hapus</button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.classes.toggle', $class->id) }}" class="d-inline-block ms-1">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button class="btn btn-sm {{ $class->is_active ? 'btn-outline-success' : 'btn-outline-secondary' }}">
                                                        {{ $class->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-building text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-2">Belum ada kelas terdaftar.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
