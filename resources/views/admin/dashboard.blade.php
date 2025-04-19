@extends('layouts.admin')

@section('title', 'Tableau de bord administrateur')

@section('content')
<div class="container mx-auto px-4">
    <!-- En-tête -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Tableau de bord administrateur</h1>
    </div>
    
    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
            <div class="text-gray-500 font-medium text-sm">Commandes totales</div>
            <div class="text-2xl font-bold">{{ $stats['total_orders'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-500">
            <div class="text-gray-500 font-medium text-sm">En attente</div>
            <div class="text-2xl font-bold">{{ $stats['pending_orders'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-orange-500">
            <div class="text-gray-500 font-medium text-sm">En production</div>
            <div class="text-2xl font-bold">{{ $stats['processing_orders'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
            <div class="text-gray-500 font-medium text-sm">Complétées</div>
            <div class="text-2xl font-bold">{{ $stats['completed_orders'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-purple-500">
            <div class="text-gray-500 font-medium text-sm">Clients</div>
            <div class="text-2xl font-bold">{{ $stats['clients_count'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-indigo-500">
            <div class="text-gray-500 font-medium text-sm">Modèles</div>
            <div class="text-2xl font-bold">{{ $stats['templates_count'] }}</div>
        </div>
    </div>
    
    <!-- Graphique des commandes par statut -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow">
            <div class="border-b px-6 py-3">
                <h2 class="text-xl font-semibold">Répartition des commandes</h2>
            </div>
            <div class="p-4">
                <div class="flex flex-col space-y-4">
                    @foreach($ordersByStatus as $status)
                        <div class="flex items-center">
                            <div class="w-32 text-sm">{{ $status->name }}</div>
                            <div class="flex-1 bg-gray-200 rounded-full h-4">
                                @php
                                    $percentage = $stats['total_orders'] > 0 
                                        ? round(($status->orders_count / $stats['total_orders']) * 100) 
                                        : 0;
                                    $color = 'bg-blue-500';
                                    
                                    if ($status->name == 'Nouvelle') $color = 'bg-blue-500';
                                    elseif ($status->name == 'En traitement') $color = 'bg-yellow-500';
                                    elseif ($status->name == 'En production') $color = 'bg-orange-500';
                                    elseif ($status->name == 'Expédié') $color = 'bg-green-300';
                                    elseif ($status->name == 'Complété') $color = 'bg-green-500';
                                    elseif ($status->name == 'Annulé') $color = 'bg-red-500';
                                @endphp
                                
                                <div class="{{ $color }} rounded-full h-4" style="width: {{ $percentage }}%"></div>
                            </div>
                            <div class="w-16 text-right text-sm">{{ $status->orders_count }}</div>
                            <div class="w-16 text-right text-sm text-gray-500">{{ $percentage }}%</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow">
            <div class="border-b px-6 py-3">
                <h2 class="text-xl font-semibold">Actions rapides</h2>
            </div>
            <div class="p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <a href="{{ route('admin.orders.index', ['status' => 1]) }}" class="flex items-center p-4 border rounded hover:bg-gray-50">
                        <div class="rounded-full bg-blue-100 p-3 mr-4">
                            <i class="fas fa-inbox text-blue-500"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold">Nouvelles commandes</h3>
                            <p class="text-sm text-gray-600">Traiter les commandes entrantes</p>
                        </div>
                    </a>
                    
                    <a href="{{ route('admin.orders.index', ['status' => 2]) }}" class="flex items-center p-4 border rounded hover:bg-gray-50">
                        <div class="rounded-full bg-yellow-100 p-3 mr-4">
                            <i class="fas fa-spinner text-yellow-500"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold">En traitement</h3>
                            <p class="text-sm text-gray-600">Gérer les commandes en cours</p>
                        </div>
                    </a>
                    
                    <a href="{{ route('admin.templates.index') }}" class="flex items-center p-4 border rounded hover:bg-gray-50">
                        <div class="rounded-full bg-indigo-100 p-3 mr-4">
                            <i class="fas fa-id-card text-indigo-500"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold">Modèles</h3>
                            <p class="text-sm text-gray-600">Gérer les modèles de cartes</p>
                        </div>
                    </a>
                    
                    <a href="{{ route('admin.orders.index') }}" class="flex items-center p-4 border rounded hover:bg-gray-50">
                        <div class="rounded-full bg-green-100 p-3 mr-4">
                            <i class="fas fa-list text-green-500"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold">Toutes les commandes</h3>
                            <p class="text-sm text-gray-600">Voir l'historique complet</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Commandes récentes -->
    <div class="bg-white rounded-lg shadow mb-8">
        <div class="border-b px-6 py-3 flex justify-between items-center">
            <h2 class="text-xl font-semibold">Commandes récentes</h2>
            <a href="{{ route('admin.orders.index') }}" class="text-blue-500 hover:text-blue-700 text-sm">
                Voir toutes <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        <div class="p-4">
            @if($recentOrders->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead>
                            <tr class="bg-gray-100 text-gray-600 uppercase text-sm leading-normal">
                                <th class="py-3 px-6 text-left">N° Commande</th>
                                <th class="py-3 px-6 text-left">Client</th>
                                <th class="py-3 px-6 text-left">Utilisateur</th>
                                <th class="py-3 px-6 text-left">Date</th>
                                <th class="py-3 px-6 text-center">Statut</th>
                                <th class="py-3 px-6 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 text-sm">
                            @foreach($recentOrders as $order)
                                <tr class="border-b border-gray-200 hover:bg-gray-100">
                                    <td class="py-3 px-6 text-left whitespace-nowrap">
                                        #{{ $order->id }}
                                    </td>
                                    <td class="py-3 px-6 text-left">
                                        {{ $order->client->name }}
                                    </td>
                                    <td class="py-3 px-6 text-left">
                                        {{ $order->user->name }}
                                    </td>
                                    <td class="py-3 px-6 text-left">
                                        {{ $order->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="py-3 px-6 text-center">
                                        <span class="px-2 py-1 rounded-full text-xs 
                                            @if($order->orderStatus->name == 'Nouvelle')
                                                bg-blue-100 text-blue-800
                                            @elseif($order->orderStatus->name == 'En traitement')
                                                bg-yellow-100 text-yellow-800
                                            @elseif($order->orderStatus->name == 'En production')
                                                bg-orange-100 text-orange-800
                                            @elseif($order->orderStatus->name == 'Expédié')
                                                bg-green-100 text-green-800
                                            @elseif($order->orderStatus->name == 'Complété')
                                                bg-green-100 text-green-800
                                            @elseif($order->orderStatus->name == 'Annulé')
                                                bg-red-100 text-red-800
                                            @else
                                                bg-gray-100 text-gray-800
                                            @endif
                                        ">
                                            {{ $order->orderStatus->name }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-6 text-center">
                                        <a href="{{ route('admin.orders.show', $order->id) }}" class="text-blue-500 hover:text-blue-700 mx-1" title="Voir détails">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4">
                    <p>Aucune commande n'a encore été passée.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection