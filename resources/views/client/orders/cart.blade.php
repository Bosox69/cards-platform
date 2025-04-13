@extends('layouts.client')

@section('title', 'Panier')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <h1 class="text-2xl font-bold mb-6">Mon panier</h1>
    
    @if(empty($cart))
        <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-6" role="alert">
            <p>Votre panier est vide. <a href="{{ route('client.orders.create') }}" class="underline">Commencer une nouvelle commande</a>.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 uppercase text-sm leading-normal">
                        <th class="py-3 px-6 text-left">Modèle</th>
                        <th class="py-3 px-6 text-left">Département</th>
                        <th class="py-3 px-6 text-left">Nom</th>
                        <th class="py-3 px-6 text-center">Quantité</th>
                        <th class="py-3 px-6 text-center">Recto/Verso</th>
                        <th class="py-3 px-6 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm">
                    @foreach($cart as $itemId => $item)
                        <tr class="border-b border-gray-200 hover:bg-gray-100">
                            <td class="py-3 px-6 text-left whitespace-nowrap">
                                {{ $item['template_name'] }}
                            </td>
                            <td class="py-3 px-6 text-left">
                                {{ $item['department_name'] }}
                            </td>
                            <td class="py-3 px-6 text-left">
                                {{ $item['card_data']['fullName'] }}
                                <div class="text-xs text-gray-500">{{ $item['card_data']['jobTitle'] }}</div>
                            </td>
                            <td class="py-3 px-6 text-center">
                                {{ $item['quantity'] }} ex.
                            </td>
                            <td class="py-3 px-6 text-center">
                                @if($item['is_double_sided'])
                                    <span class="bg-green-200 text-green-700 py-1 px-3 rounded-full text-xs">
                                        Recto-verso
                                    </span>
                                @else
                                    <span class="bg-gray-200 text-gray-700 py-1 px-3 rounded-full text-xs">
                                        Recto seul
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-6 text-center">
                                <div class="flex item-center justify-center">
                                    <a href="{{ route('client.orders.preview', ['itemId' => $itemId]) }}" class="w-4 mr-3 transform hover:text-blue-500 hover:scale-110" title="Prévisualiser">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('client.orders.edit', ['itemId' => $itemId]) }}" class="w-4 mr-3 transform hover:text-yellow-500 hover:scale-110" title="Modifier">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('client.orders.remove-from-cart') }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="itemId" value="{{ $itemId }}">
                                        <button type="submit" class="w-4 transform hover:text-red-500 hover:scale-110" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet article du panier?')">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="mt-8">
            <h2 class="text-xl font-semibold mb-4">Récapitulatif de la commande</h2>
            
            <div class="bg-gray-50 p-4 rounded mb-4">
                <div class="flex justify-between mb-2">
                    <span>Nombre total d'articles:</span>
                    <span>{{ count($cart) }}</span>
                </div>
                <div class="flex justify-between mb-2">
                    <span>Nombre total de cartes:</span>
                    <span>
                        {{ array_sum(array_map(function($item) { return $item['quantity']; }, $cart)) }} exemplaires
                    </span>
                </div>
            </div>
            
            <form action="{{ route('client.orders.store') }}" method="POST" class="mt-6">
                @csrf
                <div class="mb-6">
                    <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">Commentaires sur la commande (optionnel)</label>
                    <textarea id="comment" name="comment" rows="3" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"></textarea>
                </div>
                
                <div class="flex justify-between">
                    <a href="{{ route('client.orders.create') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Ajouter d'autres cartes
                    </a>
                    
                    <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Valider la commande
                    </button>
                </div>
            </form>
        </div>
    @endif
</div>
@endsection
