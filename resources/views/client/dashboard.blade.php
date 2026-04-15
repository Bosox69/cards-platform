@extends('layouts.client')

@section('title', 'Tableau de bord')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold">Bonjour, {{ Auth::user()->name }}</h1>
    <p class="text-gray-600">Bienvenue sur votre espace de commande de cartes de visite.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-indigo-100 text-indigo-600">
                <i class="fas fa-file-invoice text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">Total des commandes</p>
                <p class="text-2xl font-semibold">{{ $stats['total'] }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                <i class="fas fa-clock text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">En cours</p>
                <p class="text-2xl font-semibold">{{ $stats['pending'] }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-600">
                <i class="fas fa-check-circle text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">Complétées</p>
                <p class="text-2xl font-semibold">{{ $stats['completed'] }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                <i class="fas fa-id-card text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">Cartes commandées</p>
                <p class="text-2xl font-semibold">{{ $stats['total_cards'] }}</p>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold">Commandes récentes</h2>
            <a href="{{ route('client.orders.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm">
                Voir toutes les commandes <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>

        @if($recentOrders->isEmpty())
            <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
                <p class="text-blue-700">
                    Vous n'avez pas encore passé de commande.
                    <a href="{{ route('client.orders.create') }}" class="underline font-medium">Créer votre première commande</a>.
                </p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-gray-100 text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-3 px-4 text-left">Référence</th>
                            <th class="py-3 px-4 text-left">Date</th>
                            <th class="py-3 px-4 text-center">Articles</th>
                            <th class="py-3 px-4 text-center">Statut</th>
                            <th class="py-3 px-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm">
                        @foreach($recentOrders as $order)
                            <tr class="border-b border-gray-200 hover:bg-gray-50">
                                <td class="py-3 px-4 text-left whitespace-nowrap font-medium">
                                    #{{ $order->reference ?? $order->id }}
                                </td>
                                <td class="py-3 px-4 text-left">
                                    {{ $order->created_at->format('d/m/Y') }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    {{ $order->orderItems->count() }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span class="bg-gray-200 text-gray-700 py-1 px-3 rounded-full text-xs">
                                        {{ $order->orderStatus->name ?? 'Inconnu' }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <a href="{{ route('client.orders.show', $order) }}" class="text-indigo-600 hover:text-indigo-800 mr-2" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('client.orders.repeat', $order) }}" class="text-green-600 hover:text-green-800" title="Renouveler">
                                        <i class="fas fa-redo"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold mb-4">Actions rapides</h2>

        <a href="{{ route('client.orders.create') }}" class="block w-full mb-3 text-center px-4 py-3 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
            <i class="fas fa-plus mr-2"></i> Nouvelle commande
        </a>

        <a href="{{ route('client.orders.cart') }}" class="block w-full mb-6 text-center px-4 py-3 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">
            <i class="fas fa-shopping-cart mr-2"></i> Voir mon panier
        </a>

        @if($departments->isNotEmpty())
            <h3 class="text-sm font-semibold text-gray-700 uppercase mb-2">Mes départements</h3>
            <ul class="divide-y divide-gray-200">
                @foreach($departments as $department)
                    <li class="py-2 flex justify-between items-center">
                        <span class="text-gray-700">{{ $department->name }}</span>
                        <a href="{{ route('client.orders.create', ['department' => $department->id]) }}" class="text-indigo-600 hover:text-indigo-800 text-sm">
                            Commander <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
@endsection
