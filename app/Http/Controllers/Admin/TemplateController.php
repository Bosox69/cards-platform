<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Department;
use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TemplateController extends Controller
{
    /**
     * Affiche la liste des modèles de cartes
     */
    public function index(Request $request)
    {
        $query = Template::with(['department.client']);
        
        // Filtrage par client
        if ($request->has('client_id') && $request->client_id) {
            $query->whereHas('department', function ($q) use ($request) {
                $q->where('client_id', $request->client_id);
            });
        }
        
        // Filtrage par département
        if ($request->has('department_id') && $request->department_id) {
            $query->where('department_id', $request->department_id);
        }
        
        $templates = $query->orderBy('created_at', 'desc')->paginate(10);
        $clients = Client::orderBy('name')->get();
        
        // Si un client est sélectionné, récupérer ses départements
        $departments = [];
        if ($request->has('client_id') && $request->client_id) {
            $departments = Department::where('client_id', $request->client_id)
                ->orderBy('name')
                ->get();
        }
        
        return view('admin.templates.index', compact('templates', 'clients', 'departments'));
    }
    
    /**
     * Affiche le formulaire de création d'un nouveau modèle
     */
    public function create()
    {
        $clients = Client::orderBy('name')->get();
        return view('admin.templates.create', compact('clients'));
    }
    
    /**
     * Traite le formulaire de création d'un nouveau modèle
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'department_id' => 'required|exists:departments,id',
            'description' => 'nullable|string',
            'background_front' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'background_back' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'back_content' => 'nullable|string',
            'logo_x' => 'nullable|numeric',
            'logo_y' => 'nullable|numeric',
            'logo_width' => 'nullable|numeric',
            'text_start_x' => 'nullable|numeric',
            'text_start_y' => 'nullable|numeric',
            'is_active' => 'boolean'
        ]);
        
        // Traitement des fichiers d'arrière-plan
        if ($request->hasFile('background_front')) {
            $validated['background_front'] = $this->storeTemplateImage($request->file('background_front'));
        }
        
        if ($request->hasFile('background_back')) {
            $validated['background_back'] = $this->storeTemplateImage($request->file('background_back'));
        }
        
        // Valeur par défaut pour is_active
        $validated['is_active'] = $request->has('is_active');
        
        // Création du modèle
        Template::create($validated);
        
        return redirect()->route('admin.templates.index')
            ->with('success', 'Le modèle a été créé avec succès.');
    }
    
    /**
     * Affiche le formulaire d'édition d'un modèle
     */
    public function edit(Template $template)
    {
        $clients = Client::orderBy('name')->get();
        $departments = Department::where('client_id', $template->department->client_id)
            ->orderBy('name')
            ->get();
            
        return view('admin.templates.edit', compact('template', 'clients', 'departments'));
    }
    
    /**
     * Traite le formulaire d'édition d'un modèle
     */
    public function update(Request $request, Template $template)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'department_id' => 'required|exists:departments,id',
            'description' => 'nullable|string',
            'background_front' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'background_back' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'back_content' => 'nullable|string',
            'logo_x' => 'nullable|numeric',
            'logo_y' => 'nullable|numeric',
            'logo_width' => 'nullable|numeric',
            'text_start_x' => 'nullable|numeric',
            'text_start_y' => 'nullable|numeric',
            'is_active' => 'boolean'
        ]);
        
        // Traitement des fichiers d'arrière-plan
        if ($request->hasFile('background_front')) {
            // Supprimer l'ancien fichier s'il existe
            if ($template->background_front) {
                Storage::delete('public/' . $template->background_front);
            }
            $validated['background_front'] = $this->storeTemplateImage($request->file('background_front'));
        }
        
        if ($request->hasFile('background_back')) {
            // Supprimer l'ancien fichier s'il existe
            if ($template->background_back) {
                Storage::delete('public/' . $template->background_back);
            }
            $validated['background_back'] = $this->storeTemplateImage($request->file('background_back'));
        }
        
        // Valeur par défaut pour is_active
        $validated['is_active'] = $request->has('is_active');
        
        // Mise à jour du modèle
        $template->update($validated);
        
        return redirect()->route('admin.templates.index')
            ->with('success', 'Le modèle a été mis à jour avec succès.');
    }
    
    /**
     * Supprime un modèle
     */
    public function destroy(Template $template)
    {
        // Vérifier si le modèle est utilisé dans des commandes
        $orderItemsCount = $template->orderItems()->count();
        
        if ($orderItemsCount > 0) {
            return redirect()->route('admin.templates.index')
                ->with('error', 'Ce modèle ne peut pas être supprimé car il est utilisé dans ' . $orderItemsCount . ' commande(s).');
        }
        
        // Supprimer les fichiers associés
        if ($template->background_front) {
            Storage::delete('public/' . $template->background_front);
        }
        
        if ($template->background_back) {
            Storage::delete('public/' . $template->background_back);
        }
        
        // Supprimer le modèle
        $template->delete();
        
        return redirect()->route('admin.templates.index')
            ->with('success', 'Le modèle a été supprimé avec succès.');
    }
    
    /**
     * Récupérer les départements d'un client (pour AJAX)
     */
    public function getDepartments($clientId)
    {
        $departments = Department::where('client_id', $clientId)
            ->orderBy('name')
            ->get(['id', 'name']);
            
        return response()->json($departments);
    }
    
    /**
     * Stocke une image de modèle et retourne son chemin
     */
    private function storeTemplateImage($file)
    {
        $filename = 'templates/' . Str::uuid() . '.' . $file->getClientOriginalExtension();
        $file->storeAs('public', $filename);
        return $filename;
    }
}
