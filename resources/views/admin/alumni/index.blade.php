@extends('layouts.admin')

@section('title', 'Alumni')

@section('admin-content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Data Alumni</h3>
            <div>
                <a href="{{ route('admin.alumni.create') }}" class="btn btn-primary">Tambah Alumni</a>
            </div>
        </div>
        <div class="card mt-3 p-4">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if($alumni && $alumni->count())
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Kelulusan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($alumni as $al)
                            <tr>
                                <td>{{ optional(optional($al->student)->user)->name ?? 'N/A' }}</td>
                                <td>{{ $al->graduation_date ? $al->graduation_date->translatedFormat('Y') : '-' }}</td>
                                <td>
                                    <a href="{{ route('admin.alumni.edit', $al->id) }}"
                                        class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form action="{{ route('admin.alumni.destroy', $al->id) }}" method="POST"
                                        class="d-inline-block ms-2" onsubmit="return confirm('Hapus data alumni ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $alumni->links() }}
            @else
                <div class="text-center p-3">Belum ada data alumni.</div>
            @endif
        </div>
    </div>
@endsection