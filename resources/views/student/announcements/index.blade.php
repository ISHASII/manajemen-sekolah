@extends('layouts.app')

@section('title', 'Pengumuman')

@section('content')
    <div class="bg-white"> {{-- Background utama putih --}}
        <div class="container py-4">
            <div class="row mb-4">
                <div class="col-12">

                    <div class="card border-0 shadow-sm bg-white"> {{-- Card putih --}}
                        <div class="card-header bg-warning text-white">
                            <h4 class="mb-0">Pengumuman</h4>
                        </div>

                        <div class="card-body">
                            @if($announcements && $announcements->count() > 0)
                                <div class="row g-3">

                                    @foreach($announcements as $announcement)
                                        <div class="col-md-6">
                                            <div class="card h-100 bg-white"> {{-- Card list putih --}}
                                                @if($announcement->image && Storage::disk('public')->exists($announcement->image))
                                                    <img src="{{ Storage::url($announcement->image) }}" class="card-img-top"
                                                        alt="{{ $announcement->title }}">
                                                @endif

                                                <div class="card-body">
                                                    <h5 class="card-title">{{ $announcement->title }}</h5>
                                                    <p class="card-text text-muted small mb-2">
                                                        {{ Str::limit(strip_tags($announcement->content), 150) }}
                                                    </p>
                                                    <small class="text-muted">
                                                        {{ $announcement->created_at->translatedFormat('d M Y') }}
                                                    </small>
                                                </div>

                                                <div class="card-footer bg-transparent border-0">
                                                    <a href="{{ route('announcements.show', $announcement->id) }}"
                                                        class="btn btn-outline-primary btn-sm">
                                                        Baca Selengkapnya
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach

                                </div>

                                <div class="mt-4">
                                    {{ $announcements->links() }}
                                </div>

                            @else
                                <div class="text-center py-4">
                                    <i class="bi bi-megaphone text-muted" style="font-size: 2rem;"></i>
                                    <p class="text-muted mt-2">Belum ada pengumuman</p>
                                </div>
                            @endif
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .card-img-top {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }
    </style>
@endpush