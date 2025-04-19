@extends('layouts.client')

@section('title', 'Nouvelle commande')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <h1 class="text-2xl font-bold mb-6">Créer une nouvelle commande</h1>
    
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif
    
    <!-- Sélection du département -->
    <div class="mb-8">
        <h2 class="text-lg font-semibold mb-4">1. Sélectionnez un département</h2>
        
        @if($departments->isEmpty())
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6">
                <p>Aucun département n'est disponible. Veuillez contacter l'administrateur.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($departments as $department)
                    <div class="border rounded-lg p-4 hover:bg-gray-50 cursor-pointer {{ isset($selectedDepartment) && $selectedDepartment->id == $department->id ? 'border-blue-500 bg-blue-50' : '' }}"
                         onclick="window.location.href='{{ route('client.orders.create', ['department_id' => $department->id]) }}'">
                        <h3 class="font-semibold text-lg mb-1">{{ $department->name }}</h3>
                        @if($department->description)
                            <p class="text-gray-600 text-sm">{{ $department->description }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
    
    <!-- Personnalisation de la carte (affiché uniquement si un département est sélectionné) -->
    @if(isset($selectedDepartment))
        <div class="mt-8">
            <h2 class="text-lg font-semibold mb-4">2. Personnalisez votre carte</h2>
            
            <div id="card-customizer" data-department-id="{{ $selectedDepartment->id }}" 
                 @if(request()->has('template_id')) data-template-id="{{ request('template_id') }}" @endif>
                <!-- Le composant React sera monté ici -->
                <div class="text-center p-8">
                    <div class="spinner-border text-blue-500" role="status">
                        <span class="sr-only">Chargement...</span>
                    </div>
                    <p class="mt-2">Chargement du personnalisateur de carte...</p>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/app.js') }}"></script>
@endpush
