@extends('layouts.admin')

@section('title', 'Daftar Pengguna')

@section('admin-content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0">Semua Pengguna</h3>
        </div>
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.users.index') }}" class="mb-3">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">Cari nama atau email</label>
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                                placeholder="Nama atau email...">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Role</label>
                            <select name="role" class="form-select">
                                <option value="">Semua</option>
                                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="teacher" {{ request('role') === 'teacher' ? 'selected' : '' }}>Guru</option>
                                <option value="student" {{ request('role') === 'student' ? 'selected' : '' }}>Siswa</option>
                                <option value="kejuruan" {{ request('role') === 'kejuruan' ? 'selected' : '' }}>Kejuruan
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status Aktif</label>
                            <select name="is_active" class="form-select">
                                <option value="">Semua</option>
                                <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Aktif</option>
                                <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Non-aktif</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Reset</a>
                        </div>
                    </div>
                </form>

                @if($users && $users->count())
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if($user->role == 'admin')
                                            <span class="badge bg-primary">Admin</span>
                                        @elseif($user->role == 'teacher')
                                            <span class="badge bg-success">Guru</span>
                                        @elseif($user->role == 'student')
                                            <span class="badge bg-info">Siswa</span>
                                        @elseif($user->role == 'kejuruan')
                                            <span class="badge bg-warning">Kejuruan</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($user->role) }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->is_active ? 'Aktif' : 'Non-aktif' }}</td>
                                    <td>
                                        <a href="{{ route('admin.users.edit', $user->id) }}"
                                            class="btn btn-sm btn-outline-primary">Edit</a>
                                        @if(auth()->id() !== $user->id)
                                            <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}"
                                                class="d-inline-block" onsubmit="return confirm('Hapus pengguna ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger">Hapus</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{ $users->links() }}
                @else
                    <div class="text-center p-4">Tidak ada pengguna.</div>
                @endif
            </div>
        </div>
    </div>
@endsection