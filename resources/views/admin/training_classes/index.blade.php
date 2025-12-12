@extends('layouts.app')

@section('title', 'Kelas Pelatihan')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Kelas Pelatihan</h3>
            <a href="{{ route('admin.training-classes.create') }}" class="btn btn-primary">Buat Kelas</a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                @if($classes->count())
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Judul</th>
                                    <th>Trainer</th>
                                    <th>Mulai</th>
                                    <th>Akhir</th>
                                    <th>Kuota</th>
                                    <th>Terdaftar</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($classes as $c)
                                    <tr>
                                        <td>{{ $c->title }}</td>
                                        <td>{{ $c->trainer?->user?->name ?? '-' }}</td>
                                        <td>{{ $c->start_at ? $c->start_at->format('d M Y') : '-' }}</td>
                                        <td>{{ $c->end_at ? $c->end_at->format('d M Y') : '-' }}</td>
                                        <td>{{ $c->capacity ?? '-' }}</td>
                                        <td>{{ $c->students()->count() }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.training-classes.show', $c->id) }}"
                                                    class="btn btn-sm btn-outline-info text-dark"
                                                    style="color:#000!important;">Detail</a>
                                                <a href="{{ route('admin.training-classes.edit', $c->id) }}"
                                                    class="btn btn-sm btn-outline-primary text-dark"
                                                    style="color:#000!important;">Edit</a>
                                                <form method="POST" action="{{ route('admin.training-classes.destroy', $c->id) }}"
                                                    class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-sm btn-outline-danger text-dark"
                                                        style="color:#000!important;"
                                                        onclick="return confirm('Hapus kelas ini?')">Hapus</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">Belum ada kelas pelatihan</div>
                @endif
            </div>
        </div>
    </div>
@endsection