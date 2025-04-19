<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Template;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    // ... autres méthodes ...
    
    /**
     * Récupère les modèles disponibles pour un département donné
     *
     * @param Department $department
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTemplatesForDepartment(Department $department)
    {
        $user = auth()->user();
        
        // Vérifier que l'utilisateur a accès à ce département
        if (!$user->isAdmin() && $department->client_id !== $user->client_id) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'avez pas accès à ce département'
            ], 403);
        }
        
        $templates = Template::where('department_id', $department->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
            
        return response()->json($templates);
    }
}
