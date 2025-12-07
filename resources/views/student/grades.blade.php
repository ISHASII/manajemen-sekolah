@extends('layouts.app')

@section('title', 'Nilai & Keterampilan')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Nilai & Keterampilan</h2>
            </div>

            <!-- Nilai Akademik -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Nilai Akademik</h5>
                </div>
                <div class="card-body">
                    @if($grades->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Mata Pelajaran</th>
                                        <th>Jenis Penilaian</th>
                                        <th>Nilai</th>
                                        <th>Grade</th>
                                        <th>Semester</th>
                                        <th>Tanggal</th>
                                        <th>Guru</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($grades as $grade)
                                        <tr>
                                            <td>{{ $grade->subject->name }}</td>
                                            <td>
                                                <span class="badge 
                                                    @if($grade->assessment_type === 'daily') bg-info
                                                    @elseif($grade->assessment_type === 'midterm') bg-warning
                                                    @elseif($grade->assessment_type === 'final') bg-danger
                                                    @else bg-success
                                                    @endif">
                                                    {{ ucfirst($grade->assessment_type) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="fw-bold 
                                                    @if($grade->score >= 90) text-success
                                                    @elseif($grade->score >= 80) text-primary
                                                    @elseif($grade->score >= 70) text-warning
                                                    @else text-danger
                                                    @endif">
                                                    {{ $grade->score }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge 
                                                    @if($grade->grade === 'A') bg-success
                                                    @elseif($grade->grade === 'B') bg-primary
                                                    @elseif($grade->grade === 'C') bg-warning
                                                    @else bg-danger
                                                    @endif">
                                                    {{ $grade->grade }}
                                                </span>
                                            </td>
                                            <td>{{ $grade->semester }}</td>
                                            <td>{{ $grade->assessment_date->format('d M Y') }}</td>
                                            <td>{{ $grade->teacher->name }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Grade Summary -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <h6>Ringkasan Nilai</h6>
                                <div class="row">
                                    @php
                                        $gradeStats = $grades->groupBy('grade')->map->count();
                                        $avgScore = $grades->avg('score');
                                    @endphp
                                    
                                    <div class="col-md-2">
                                        <div class="text-center">
                                            <div class="h3 text-success">{{ $gradeStats->get('A', 0) }}</div>
                                            <small class="text-muted">Grade A</small>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="text-center">
                                            <div class="h3 text-primary">{{ $gradeStats->get('B', 0) }}</div>
                                            <small class="text-muted">Grade B</small>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="text-center">
                                            <div class="h3 text-warning">{{ $gradeStats->get('C', 0) }}</div>
                                            <small class="text-muted">Grade C</small>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="text-center">
                                            <div class="h3 text-danger">{{ $gradeStats->get('D', 0) + $gradeStats->get('E', 0) }}</div>
                                            <small class="text-muted">Grade D/E</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="text-center">
                                            <div class="h3 text-info">{{ number_format($avgScore, 1) }}</div>
                                            <small class="text-muted">Rata-rata Nilai</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum ada data nilai</h5>
                            <p class="text-muted">Nilai akan muncul setelah guru melakukan penilaian</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Keterampilan -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-star me-2"></i>Penilaian Keterampilan</h5>
                </div>
                <div class="card-body">
                    @if($skills->count() > 0)
                        <div class="row">
                            @foreach($skills as $skill)
                                <div class="col-md-6 mb-3">
                                    <div class="card border-start border-4 
                                        @if($skill->proficiency_level === 'expert') border-success
                                        @elseif($skill->proficiency_level === 'advanced') border-primary
                                        @elseif($skill->proficiency_level === 'intermediate') border-warning
                                        @else border-secondary
                                        @endif">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="mb-1">{{ $skill->skill_name }}</h6>
                                                    <small class="text-muted">{{ ucfirst(str_replace('_', ' ', $skill->skill_category)) }}</small>
                                                </div>
                                                <span class="badge 
                                                    @if($skill->proficiency_level === 'expert') bg-success
                                                    @elseif($skill->proficiency_level === 'advanced') bg-primary
                                                    @elseif($skill->proficiency_level === 'intermediate') bg-warning
                                                    @else bg-secondary
                                                    @endif">
                                                    {{ ucfirst($skill->proficiency_level) }}
                                                </span>
                                            </div>
                                            
                                            @if($skill->description)
                                                <p class="mt-2 mb-2 small">{{ $skill->description }}</p>
                                            @endif
                                            
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted">
                                                    Dinilai: {{ $skill->assessed_date->format('d M Y') }}
                                                </small>
                                                <small class="text-muted">
                                                    Oleh: {{ $skill->assessedBy->name }}
                                                </small>
                                            </div>
                                            
                                            @if($skill->certificate_file)
                                                <a href="{{ Storage::url($skill->certificate_file) }}" class="btn btn-sm btn-outline-primary mt-2" target="_blank">
                                                    <i class="fas fa-certificate me-1"></i>Lihat Sertifikat
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Skills Summary -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <h6>Ringkasan Keterampilan</h6>
                                <div class="row">
                                    @php
                                        $skillsByCategory = $skills->groupBy('skill_category');
                                        $skillsByLevel = $skills->groupBy('proficiency_level');
                                    @endphp
                                    
                                    <div class="col-md-6">
                                        <h6 class="small">Per Kategori:</h6>
                                        @foreach($skillsByCategory as $category => $categorySkills)
                                            <div class="d-flex justify-content-between mb-1">
                                                <span class="small">{{ ucfirst(str_replace('_', ' ', $category)) }}</span>
                                                <span class="badge bg-light text-dark">{{ $categorySkills->count() }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <h6 class="small">Per Level:</h6>
                                        @foreach($skillsByLevel as $level => $levelSkills)
                                            <div class="d-flex justify-content-between mb-1">
                                                <span class="small">{{ ucfirst($level) }}</span>
                                                <span class="badge 
                                                    @if($level === 'expert') bg-success
                                                    @elseif($level === 'advanced') bg-primary
                                                    @elseif($level === 'intermediate') bg-warning
                                                    @else bg-secondary
                                                    @endif">{{ $levelSkills->count() }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-star fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum ada penilaian keterampilan</h5>
                            <p class="text-muted">Keterampilan akan dinilai oleh guru selama proses pembelajaran</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection