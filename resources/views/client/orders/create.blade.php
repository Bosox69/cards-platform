@extends('layouts.client')

@section('title', 'Nouvelle commande')

@section('content')
<div class="page-header mb-4">
    <h1>Nouvelle commande</h1>
    <p class="lead">Sélectionnez un département puis personnalisez votre carte de visite.</p>
</div>

<div class="card mb-4">
    <div class="card-header py-3">
        <h5 class="mb-0">
            <span class="badge bg-primary rounded-circle me-2" style="width:28px;height:28px;line-height:20px;">1</span>
            Choisissez un département
        </h5>
    </div>
    <div class="card-body">
        @if($departments->isEmpty())
            <div class="alert alert-warning mb-0">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Aucun département n'est disponible. Veuillez contacter l'administrateur.
            </div>
        @else
            <div class="row g-3">
                @foreach($departments as $department)
                    @php $isSelected = isset($selectedDepartment) && $selectedDepartment->id == $department->id; @endphp
                    <div class="col-md-6 col-lg-4">
                        <a href="{{ route('client.orders.create', ['department_id' => $department->id]) }}"
                           class="card card-hover h-100 text-decoration-none border {{ $isSelected ? 'border-primary bg-primary bg-opacity-10' : '' }}">
                            <div class="card-body d-flex align-items-start">
                                <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3">
                                    <i class="fas fa-building"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 d-flex align-items-center">
                                        {{ $department->name }}
                                        @if($isSelected)
                                            <i class="fas fa-check-circle text-primary ms-2"></i>
                                        @endif
                                    </h6>
                                    @if($department->description)
                                        <p class="small text-muted mb-0">{{ $department->description }}</p>
                                    @endif
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

@if(isset($selectedDepartment))
    <div class="card">
        <div class="card-header py-3">
            <h5 class="mb-0">
                <span class="badge bg-primary rounded-circle me-2" style="width:28px;height:28px;line-height:20px;">2</span>
                Personnalisez votre carte — {{ $selectedDepartment->name }}
            </h5>
        </div>
        <div class="card-body">
            <div id="card-customizer" data-department-id="{{ $selectedDepartment->id }}"
                 @if(request()->has('template_id')) data-template-id="{{ request('template_id') }}" @endif>
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Chargement...</span>
                    </div>
                    <p class="text-muted mt-3 mb-0">Chargement du personnalisateur...</p>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection
