@extends('layouts.app')

@section('title', $announcement->title ?? 'Pengumuman')

@section('content')
    <div class="bg-white py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-10">

                    <div class="card shadow-sm border-0 bg-white">
                        @if($announcement->image)
                            <img src="{{ Storage::url($announcement->image) }}" alt="{{ $announcement->title }}"
                                class="card-img-top" style="height: 320px; object-fit: cover;" />
                        @endif

                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">

                                <div>
                                    <h2 class="mb-1">{{ $announcement->title }}</h2>
                                    <small class="text-muted">
                                        {{ $announcement->publish_date->format('d M Y') }}
                                    </small>
                                </div>

                                <div>
                                    <span class="badge bg-{{
        $announcement->type == 'event'
        ? 'success'
        : ($announcement->type == 'urgent'
            ? 'danger'
            : 'primary')
                                                    }}">
                                        {{ ucfirst($announcement->type) }}
                                    </span>
                                </div>

                            </div>

                            <div class="mb-4 text-muted">{!! nl2br(e($announcement->content)) !!}</div>

                            <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                                Kembali
                            </a>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection