@extends('layouts.admin')

@section('title', 'Mata Pelajaran')

@section('admin-content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Mata Pelajaran</h3>
            <a href="{{ route('admin.subjects.create') }}" class="btn btn-primary">Buat Mata Pelajaran</a>
        </div>

        <div class="card">
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                @if($subjects && $subjects->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama</th>
                                    <th>Kode</th>
                                    <th>SKS</th>
                                    <th>Kategori</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($subjects as $subject)
                                    <tr>
                                        <td>{{ $subject->name }}</td>
                                        <td>{{ $subject->code ?? '-' }}</td>
                                        <td>{{ $subject->credit_hours ?? 0 }}</td>
                                        <td>{{ $subject->category ?? '-' }}</td>
                                        <td>{{ $subject->is_active ? 'Aktif' : 'Nonaktif' }}</td>
                                        <td>
                                            <a href="{{ route('admin.subjects.edit', $subject->id) }}"
                                                class="btn btn-sm btn-outline-primary">Edit</a>
                                            <form method="POST" action="{{ route('admin.subjects.destroy', $subject->id) }}"
                                                class="d-inline-block" onsubmit="return confirm('Hapus mata pelajaran ini?');">
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
                        {{ $subjects->links() }}
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-book text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-2">Belum ada mata pelajaran yang terdaftar.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection