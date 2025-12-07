@extends('layouts.app')

@section('title', 'Kelola Guru')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Daftar Guru</h3>
            <div>
                <a href="{{ route('admin.teachers.create') }}" class="btn btn-primary">Tambah Guru</a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                @if($teachers && $teachers->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Mapel</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($teachers as $t)
                                    <tr>
                                        <td>{{ $t->user->name ?? '-' }}</td>
                                        <td>{{ $t->user->email ?? '-' }}</td>
                                        <td>
                                            @if($t->subjects && is_array($t->subjects) && count($t->subjects) > 0)
                                                @foreach($t->subjects as $sid)
                                                    {{ $subjects[$sid] ?? '-' }}@if(!$loop->last), @endif
                                                @endforeach
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ ucfirst($t->status ?? 'inactive') }}</td>
                                        <td>
                                            <a href="{{ route('admin.teachers.edit', $t->id) }}"
                                                class="btn btn-sm btn-outline-primary">Detail</a>
                                            <form method="POST" action="{{ route('admin.teachers.destroy', $t->id) }}"
                                                class="d-inline-block" onsubmit="return confirm('Hapus guru ini?');">
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
                    {{ $teachers->links() }}
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-person-badge text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-2">Belum ada guru terdaftar.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection