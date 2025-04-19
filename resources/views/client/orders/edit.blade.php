@extends('layouts.client')

@section('title', 'Modifier une carte')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Modifier une carte</h1>
        <a href="{{ route('client.orders.cart') }}" class="text-blue-500 hover:text-blue-700">
            <i class="fas fa-arrow-left mr-1"></i> Retour au panier
        </a>
    </div>
    
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif
    
    <!-- Personnalisation de la carte -->
    <div id="card-customizer" 
         data-department-id="{{ $department->id }}" 
         data-template-id="{{ $template->id }}"
         data-item-id="{{ $itemId }}"
         data-edit-mode="true"
         data-card-data="{{ json_encode($item['card_data']) }}"
         data-is-double-sided="{{ $item['is_double_sided'] ? 'true' : 'false' }}"
         data-quantity="{{ $item['quantity'] }}">
        <!-- Le composant React sera monté ici -->
        <div class="text-center p-8">
            <div class="spinner-border text-blue-500" role="status">
                <span class="sr-only">Chargement...</span>
            </div>
            <p class="mt-2">Chargement du personnalisateur de carte...</p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/app.js') }}"></script>
@endpush
