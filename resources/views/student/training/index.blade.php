@extends('layouts.app')

@section('title', 'Pelatihan Kejuruan')

@section('content')
    <div class="container py-4">
        <h3>Pelatihan Kejuruan</h3>
        <div class="row">
            @forelse($classes as $c)
                <div class="col-md-6 mb-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h5>{{ $c->title }}</h5>
                            <p class="text-muted small">{{ Str::limit($c->description, 120) }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                @php $routePrefix = auth()->user()->role === 'kejuruan' ? 'kejuruan' : 'student';
                                     $isEnrolled = $student && $student->trainingClasses()->where('training_classes.id', $c->id)->exists();
                                @endphp
                                <div>
                                    <a href="{{ route($routePrefix . '.training-classes.show', $c->id) }}" class="btn btn-sm btn-outline-primary">Lihat</a>
                                    @if($isEnrolled)
                                        <a href="{{ route($routePrefix . '.materials') }}?training_class_id={{ $c->id }}" class="btn btn-sm btn-outline-secondary">Materi</a>
                                    @endif
                                </div>
                                <div class="text-end">
                                    @if($isEnrolled)
                                        <span class="badge bg-success">Terdaftar</span>
                                    @else
                                        @php $isFull = ($c->capacity && ($c->students_count ?? 0) >= $c->capacity); @endphp
                                        @if($isFull)
                                            <button class="btn btn-sm btn-secondary" disabled>Penuh</button>
                                        @elseif(isset($hasActiveTraining) && $hasActiveTraining)
                                            <button class="btn btn-sm btn-warning" disabled>Selesaikan pelatihan lain dulu</button>
                                        @else
                                            <form method="POST" action="{{ route($routePrefix . '.training-classes.enroll', $c->id) }}">
                                                @csrf
                                                <button class="btn btn-sm btn-primary">Daftar</button>
                                            </form>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-footer small text-muted">
                            <div class="d-flex justify-content-between">
                                <div>Kuota: {{ $c->capacity ?? 'Unlimited' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">Tidak ada pelatihan tersedia.</div>
            @endforelse
        </div>
    </div>
@endsection
