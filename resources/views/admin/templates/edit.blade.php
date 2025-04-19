@extends('layouts.admin')

@section('title', 'Modifier un modèle')

@section('content')
<div class="bg-white shadow-md rounded-lg overflow-hidden">
    <div class="p-6 bg-gray-50 border-b flex justify-between items-center">
        <h1 class="text-2xl font-bold">Modifier un modèle</h1>
        
        <a href="{{ route('admin.templates.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded inline-flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Retour
        </a>
    </div>

    <div class="p-6">
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                <strong class="font-bold">Erreur!</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <form action="{{ route('admin.templates.update', $template->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h2 class="text-lg font-semibold mb-4">Informations de base</h2>
                    
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nom du modèle</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $template->name) }}" required class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring focus:border-blue-300">
                    </div>
                    
                    <div class="mb-4">
                        <label for="client_id" class="block text-sm font-medium text-gray-700 mb-1">Client</label>
                        <select id="client_id" class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring focus:border-blue-300" onchange="loadDepartments(this.value)">
                            <option value="">Sélectionnez un client</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ $template->department->client_id == $client->id ? 'selected' : '' }}>
                                    {{ $client->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label for="department_id" class="block text-sm font-medium text-gray-700 mb-1">Département</label>
                        <select id="department_id" name="department_id" required class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring focus:border-blue-300">
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}" {{ $template->department_id == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea id="description" name="description" rows="3" class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring focus:border-blue-300">{{ old('description', $template->description) }}</textarea>
                    </div>
                    
                    <div class="mb-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" value="1" {{ $template->is_active ? 'checked' : '' }} class="mr-2">
                            <span class="text-sm font-medium text-gray-700">Modèle actif</span>
                        </label>
                    </div>
                </div>
                
                <div>
                    <h2 class="text-lg font-semibold mb-4">Images et mise en page</h2>
                    
                    <div class="mb-4">
                        <label for="background_front" class="block text-sm font-medium text-gray-700 mb-1">Arrière-plan recto</label>
                        <input type="file" id="background_front" name="background_front" class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring focus:border-blue-300">
                        @if($template->background_front)
                            <div class="mt-2">
                                <img src="{{ Storage::url($template->background_front) }}" alt="Aperçu recto" class="h-24 border">
                            </div>
                        @endif
                    </div>
                    
                    <div class="mb-4">
                        <label for="background_back" class="block text-sm font-medium text-gray-700 mb-1">Arrière-plan verso</label>
                        <input type="file" id="background_back" name="background_back" class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring focus:border-blue-300">
                        @if($template->background_back)
                            <div class="mt-2">
                                <img src="{{ Storage::url($template->background_back) }}" alt="Aperçu verso" class="h-24 border">
                            </div>
                        @endif
                    </div>
                    
                    <div class="mb-4">
                        <label for="back_content" class="block text-sm font-medium text-gray-700 mb-1">Contenu du verso</label>
                        <textarea id="back_content" name="back_content" rows="3" class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring focus:border-blue-300">{{ old('back_content', $template->back_content) }}</textarea>
                    </div>
                    
                    <h3 class="font-medium text-gray-700 mb-2 mt-4">Positionnement des éléments</h3>
                    
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label for="logo_x" class="block text-sm font-medium text-gray-700 mb-1">Position X du logo</label>
                            <input type="number" step="0.1" id="logo_x" name="logo_x" value="{{ old('logo_x', $template->logo_x) }}" class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring focus:border-blue-300">
                        </div>
                        <div>
                            <label for="logo_y" class="block text-sm font-medium text-gray-700 mb-1">Position Y du logo</label>
                            <input type="number" step="0.1" id="logo_y" name="logo_y" value="{{ old('logo_y', $template->logo_y) }}" class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring focus:border-blue-300">
                        </div>
                        <div>
                            <label for="logo_width" class="block text-sm font-medium text-gray-700 mb-1">Largeur du logo</label>
                            <input type="number" step="0.1" id="logo_width" name="logo_width" value="{{ old('logo_width', $template->logo_width) }}" class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring focus:border-blue-300">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 mt-2">
                        <div>
                            <label for="text_start_x" class="block text-sm font-medium text-gray-700 mb-1">Début X du texte</label>
                            <input type="number" step="0.1" id="text_start_x" name="text_start_x" value="{{ old('text_start_x', $template->text_start_x) }}" class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring focus:border-blue-300">
                        </div>
                        <div>
                            <label for="text_start_y" class="block text-sm font-medium text-gray-700 mb-1">Début Y du texte</label>
                            <input type="number" step="0.1" id="text_start_y" name="text_start_y" value="{{ old('text_start_y', $template->text_start_y) }}" class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring focus:border-blue-300">
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end">
                <a href="{{ route('admin.templates.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded mr-2">
                    Annuler
                </a>
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded">
                    Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function loadDepartments(clientId) {
        if (!clientId) {
            document.getElementById('department_id').innerHTML = '<option value="">Sélectionnez un département</option>';
            return;
        }
        
        fetch(`/api/clients/${clientId}/departments`)
            .then(response => response.json())
            .then(departments => {
                let options = '<option value="">Sélectionnez un département</option>';
                departments.forEach(dept => {
                    options += `<option value="${dept.id}">${dept.name}</option>`;
                });
                document.getElementById('department_id').innerHTML = options;
            })
            .catch(error => console.error('Erreur lors du chargement des départements:', error));
    }
</script>
@endpush
