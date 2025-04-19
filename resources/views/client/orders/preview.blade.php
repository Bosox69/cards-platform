@extends('layouts.client')

@section('title', 'Prévisualisation de carte')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Prévisualisation de carte</h1>
        <a href="{{ route('client.orders.cart') }}" class="text-blue-500 hover:text-blue-700">
            <i class="fas fa-arrow-left mr-1"></i> Retour au panier
        </a>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Informations sur la carte -->
        <div>
            <h2 class="text-lg font-semibold mb-4">Informations</h2>
            
            <div class="bg-gray-50 p-6 rounded-lg border">
                <div class="mb-4">
                    <div class="text-sm text-gray-600">Modèle:</div>
                    <div class="font-medium">{{ $item['template_name'] }}</div>
                </div>
                
                <div class="mb-4">
                    <div class="text-sm text-gray-600">Département:</div>
                    <div class="font-medium">{{ $item['department_name'] }}</div>
                </div>
                
                <div class="mb-4">
                    <div class="text-sm text-gray-600">Nom:</div>
                    <div class="font-medium">{{ $item['card_data']['fullName'] }}</div>
                </div>
                
                <div class="mb-4">
                    <div class="text-sm text-gray-600">Fonction:</div>
                    <div class="font-medium">{{ $item['card_data']['jobTitle'] }}</div>
                </div>
                
                @if(isset($item['card_data']['email']))
                    <div class="mb-4">
                        <div class="text-sm text-gray-600">Email:</div>
                        <div class="font-medium">{{ $item['card_data']['email'] }}</div>
                    </div>
                @endif
                
                @if(isset($item['card_data']['phone']))
                    <div class="mb-4">
                        <div class="text-sm text-gray-600">Téléphone:</div>
                        <div class="font-medium">{{ $item['card_data']['phone'] }}</div>
                    </div>
                @endif
                
                @if(isset($item['card_data']['mobile']))
                    <div class="mb-4">
                        <div class="text-sm text-gray-600">Mobile:</div>
                        <div class="font-medium">{{ $item['card_data']['mobile'] }}</div>
                    </div>
                @endif
                
                @if(isset($item['card_data']['address']))
                    <div class="mb-4">
                        <div class="text-sm text-gray-600">Adresse:</div>
                        <div class="font-medium">{{ $item['card_data']['address'] }}</div>
                    </div>
                @endif
                
                <div class="mb-4">
                    <div class="text-sm text-gray-600">Quantité:</div>
                    <div class="font-medium">{{ $item['quantity'] }} exemplaires</div>
                </div>
                
                <div>
                    <div class="text-sm text-gray-600">Type d'impression:</div>
                    <div class="font-medium">{{ $item['is_double_sided'] ? 'Recto-verso' : 'Recto uniquement' }}</div>
                </div>
            </div>
            
            <div class="mt-6 flex space-x-4">
                <a href="{{ route('client.orders.edit', ['itemId' => request()->itemId]) }}" class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded">
                    Modifier
                </a>
                <a href="{{ route('client.orders.cart') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 py-2 px-4 rounded">
                    Retour au panier
                </a>
            </div>
        </div>
        
        <!-- PDF Preview -->
        <div>
            <h2 class="text-lg font-semibold mb-4">Aperçu</h2>
            
            <div class="border rounded-lg overflow-hidden">
                <iframe src="{{ $pdfUrl }}" width="100%" height="500" class="border-0"></iframe>
            </div>
            
            <div class="mt-4">
                <a href="{{ $pdfUrl }}" target="_blank" class="text-blue-500 hover:underline">
                    <i class="fas fa-external-link-alt mr-1"></i> Ouvrir dans un nouvel onglet
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
