@extends('layouts.app')

@section('title', $training->title)

@section('content')
    <div class="container py-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h3>{{ $training->title }}</h3>
                <p>{{ $training->description }}</p>
                <p>Mulai: {{ $training->start_at ? $training->start_at->format('d M Y') : '-' }}</p>
                <p>Akhir: {{ $training->end_at ? $training->end_at->format('d M Y') : '-' }}</p>
                <p>Kuota: {{ $training->capacity ?? 'Unlimited' }} — Peserta:
                    {{ $training->students_count ?? 0 }}@if($training->capacity) — Sisa:
                    {{ max(0, ($training->capacity - ($training->students_count ?? 0))) }}@endif
                </p>
        @php
            $user = auth()->user();
            $student = \App\Models\Student::where('user_id', $user->id)->first();
        @endphp
                @php $routePrefix = auth()->user()->role === 'kejuruan' ? 'kejuruan' : 'student'; @endphp
                @php $isEnrolled = $student && $student->trainingClasses()->where('training_classes.id', $training->id)->exists(); @endphp
                @if($isEnrolled)
                    <a href="{{ route($routePrefix . '.materials') }}?training_class_id={{ $training->id }}"
                        class="btn btn-outline-secondary ms-2">Materi</a>
                    <span class="badge bg-success ms-2">Terdaftar</span>
                @endif
                @if(!$isEnrolled)
                    @php $isFull = ($training->capacity && ($training->students_count ?? 0) >= $training->capacity); @endphp
                    @if($isFull)
                        <button class="btn btn-secondary" disabled>Penuh</button>
                    @elseif(isset($hasActiveTraining) && $hasActiveTraining)
                        <button class="btn btn-warning" disabled>Selesaikan pelatihan lain dulu</button>
                    @else
                        <form method="POST" action="{{ route($routePrefix . '.training-classes.enroll', $training->id) }}">
                            @csrf
                            <button class="btn btn-primary">Daftar Pelatihan</button>
                        </form>
                    @endif
                @endif
            </div>
        </div>
    </div>
@endsection