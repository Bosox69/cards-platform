@extends('layouts.admin')

@section('title', 'Gestion des modèles de cartes')

@section('content')
<div class="bg-white shadow-md rounded-lg overflow-hidden">
    <div class="p-6 bg-gray-50 border-b flex justify-between items-center">
        <h1 class="text-2xl font-bold">Gestion des modèles de cartes</h1>
        
        <a href="{{ route('admin.templates.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded inline-flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Nouveau modèle
        </a>
    </div>

    <div class="p-6">
        <!-- Filtres -->
        <div class="mb-6">
            <form action="{{ route('admin.templates.index') }}" method="GET" class="flex flex-wrap gap-4 items-end">
                <div>
                    <label for="client_id" class="block text-sm font-medium text-gray-700 mb-1">Client</label>
                    <select id="client_id" name="client_id" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" onchange="loadDepartments(this.value)">
                        <option value="">Tous les clients</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                {{ $client->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="department_id" class="block text-sm font-medium text-gray-700 mb-1">Département</label>
                    <select id="department_id" name="department_id" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="">Tous les départements</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                        Filtrer
                    </button>
                    <a href="{{ route('admin.templates.index') }}" class="ml-2 text-indigo-600 hover:text-indigo-800">
                        Réinitialiser
                    </a>
                </div>
            </form>
        </div>
        
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif
        
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif
        
        <!-- Tableau des modèles -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Nom
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Client / Département
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aperçu
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Statut
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($templates as $template)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $template->name }}</div>
                                <div class="text-xs text-gray-500">ID: {{ $template->id }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $template->department->client->name }}</div>
                                <div class="text-sm text-gray-500">{{ $template->department->name }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($template->background_front)
                                    <a href="{{ Storage::url($template->background_front) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900">
                                        <img src="{{ Storage::url($template->background_front) }}" alt="Aperçu" class="h-16 border">
                                    </a>
                                @else
                                    <span class="text-gray-500 text-sm">Pas d'aperçu</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $template->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $template->is_active ? 'Actif' : 'Inactif' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('admin.templates.edit', $template->id) }}" class="text-indigo-600 hover:text-indigo-900">
                                        Modifier
                                    </a>
                                    
                                    <form action="{{ route('admin.templates.destroy', $template->id) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce modèle?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 ml-2">
                                            Supprimer
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-gray-500">
                                Aucun modèle trouvé
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="mt-6">
            {{ $templates->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function loadDepartments(clientId) {
        if (!clientId) {
            // Si aucun client n'est sélectionné, vider la liste des départements
            document.getElementById('department_id').innerHTML = '<option value="">Tous les départements</option>';
            return;
        }
        
        // Appel AJAX pour récupérer les départements du client
        fetch(`/api/clients/${clientId}/departments`)
            .then(response => response.json())
            .then(departments => {
                let options = '<option value="">Tous les départements</option>';
                departments.forEach(dept => {
                    options += `<option value="${dept.id}">${dept.name}</option>`;
                });
                document.getElementById('department_id').innerHTML = options;
            })
            .catch(error => console.error('Erreur lors du chargement des départements:', error));
    }
</script>
@endpush
