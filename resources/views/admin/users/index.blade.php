@extends('layouts.admin')

@section('title', 'Daftar Pengguna')

@section('admin-content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0">Semua Pengguna</h3>
            <div>
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary">Buat Pengguna</a>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
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
                                    <td>{{ $user->role }}</td>
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