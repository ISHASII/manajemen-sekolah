@extends('layouts.admin')

@section('title', 'Kelola Pengumuman')

@section('admin-content')
    <div class="container py-4">
        <div class="d-flex justify-content-between mb-3">
            <h3>Pengumuman</h3>
            <a href="{{ route('admin.announcements.create') }}" class="btn btn-primary">Buat Pengumuman</a>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="list-group">
                    @forelse($announcements as $ann)
                        <div class="list-group-item d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">{{ $ann->title }}</h6>
                                <small class="text-muted me-2">{{ $ann->created_at->format('d M Y') }}</small>
                                @if(optional($ann->creator)->name)
                                    <small class="text-muted">oleh {{ optional($ann->creator)->name }}</small>
                                @endif
                                @if($ann->publish_date)
                                    <div><small class="text-muted">Publish: {{ $ann->publish_date->format('d M Y') }}</small></div>
                                @endif
                            </div>
                            <div>
                                <a href="{{ route('admin.announcements.edit', $ann->id) }}"
                                    class="btn btn-sm btn-outline-primary">Edit</a>
                                <form action="{{ route('admin.announcements.destroy', $ann->id) }}" method="POST"
                                    class="d-inline-block ms-2">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"
                                        onclick="return confirm('Hapus pengumuman ini?')">Hapus</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-muted">Belum ada pengumuman.</div>
                    @endforelse
                </div>
                <div class="mt-3">
                    {{ $announcements->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection