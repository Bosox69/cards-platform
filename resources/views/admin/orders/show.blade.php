@extends('layouts.admin')

@section('title', 'Détails de la commande #' . $order->id)

@section('content')
<div class="bg-white shadow-md rounded-lg overflow-hidden">
    <div class="p-6 bg-gray-50 border-b flex justify-between items-center">
        <h1 class="text-2xl font-bold">Commande #{{ $order->id }}</h1>
        
        <div class="flex space-x-2">
            <a href="{{ route('admin.orders.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded inline-flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Retour
            </a>
            
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded inline-flex items-center">
                    <span>Modifier le statut</span>
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div x-show="open" @click.away="open = false" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-10">
                    @foreach($statuses as $status)
                        <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST" class="block w-full">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="order_status_id" value="{{ $status->id }}">
                            <button type="submit" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 w-full text-left {{ $order->order_status_id == $status->id ? 'bg-gray-100' : '' }}">
                                {{ $status->name }}
                            </button>
                        </form>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="p-6">
        <!-- Informations générales -->
        <div class="mb-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-gray-50 p-4 rounded-lg">
                <h2 class="font-semibold text-lg mb-2">Informations client</h2>
                <p><span class="font-medium">Client:</span> {{ $order->client->name }}</p>
                <p><span class="font-medium">Utilisateur:</span> {{ $order->user->name }}</p>
                <p><span class="font-medium">Email:</span> {{ $order->user->email }}</p>
            </div>
            
            <div class="bg-gray-50 p-4 rounded-lg">
                <h2 class="font-semibold text-lg mb-2">Détails de la commande</h2>
                <p><span class="font-medium">Date:</span> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                <p><span class="font-medium">Statut:</span> 
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                        @if($order->orderStatus->name == 'En attente')
                            bg-yellow-100 text-yellow-800
                        @elseif($order->orderStatus->name == 'En production')
                            bg-blue-100 text-blue-800
                        @elseif($order->orderStatus->name == 'Expédié')
                            bg-green-100 text-green-800
                        @elseif($order->orderStatus->name == 'Annulé')
                            bg-red-100 text-red-800
                        @else
                            bg-gray-100 text-gray-800
                        @endif
                    ">
                        {{ $order->orderStatus->name }}
                    </span>
                </p>
                <p><span class="font-medium">Nombre d'articles:</span> {{ $order->orderItems->count() }}</p>
                <p><span class="font-medium">Total cartes:</span> {{ $order->orderItems->sum('quantity') }} exemplaires</p>
            </div>
            
            <div class="bg-gray-50 p-4 rounded-lg">
                <h2 class="font-semibold text-lg mb-2">Historique</h2>
                <div class="space-y-2">
                    @foreach($order->statusHistory as $history)
                        <div class="text-sm">
                            <div class="font-medium">{{ $history->status->name }}</div>
                            <div class="text-gray-500">{{ $history->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        
        <!-- Articles de la commande -->
        <h2 class="font-semibold text-xl mb-4">Détails des articles</h2>
        <div class="bg-white shadow overflow-hidden border-b border-gray-200 sm:rounded-lg mb-6">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Modèle / Département
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Informations personnalisées
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Quantité
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Recto/Verso
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($order->orderItems as $item)
                        @php
                            $cardData = json_decode($item->cardData->data, true);
                        @endphp
                        <tr>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $item->template->name }}</div>
                                <div class="text-sm text-gray-500">{{ $item->template->department->name }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm">
                                    <div class="font-medium">{{ $cardData['fullName'] ?? 'N/A' }}</div>
                                    <div>{{ $cardData['jobTitle'] ?? 'N/A' }}</div>
                                    @if(isset($cardData['email']))
                                        <div>{{ $cardData['email'] }}</div>
                                    @endif
                                    @if(isset($cardData['phone']))
                                        <div>Tél: {{ $cardData['phone'] }}</div>
                                    @endif
                                    @if(isset($cardData['mobile']))
                                        <div>Mobile: {{ $cardData['mobile'] }}</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $item->quantity }} ex.
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $item->is_double_sided ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $item->is_double_sided ? 'Recto-verso' : 'Recto seul' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('admin.orders.preview-card', $item->id) }}" class="text-indigo-600 hover:text-indigo-900" target="_blank">
                                    Prévisualiser
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Commentaires -->
        @if($order->comment)
            <div class="bg-gray-50 p-4 rounded-lg">
                <h2 class="font-semibold text-lg mb-2">Commentaires</h2>
                <p class="text-gray-700">{{ $order->comment }}</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/alpinejs@2.8.2/dist/alpine.min.js" defer></script>
@endpush
