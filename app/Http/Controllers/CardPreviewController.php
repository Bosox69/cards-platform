<?php

namespace App\Http\Controllers;

use App\Models\Template;
use App\Services\CardPdfGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CardPreviewController extends Controller
{
    /**
     * Générer une prévisualisation de carte de visite
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function generate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'template_id' => 'required|exists:templates,id',
            'is_double_sided' => 'boolean',
            'card_data' => 'required|array',
            'card_data.fullName' => 'required|string|max:100',
            'card_data.jobTitle' => 'required|string|max:100',
            'card_data.email' => 'nullable|email|max:100',
            'card_data.phone' => 'nullable|string|max:20',
            'card_data.mobile' => 'nullable|string|max:20',
            'card_data.address' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Récupérer le modèle
        $template = Template::findOrFail($request->template_id);
        
        // Vérifier que l'utilisateur a accès à ce modèle
        $user = $request->user();
        if (!$user->is_admin && $template->department->client_id !== $user->client_id) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'avez pas accès à ce modèle'
            ], 403);
        }

        // Générer le PDF
        $pdfGenerator = new CardPdfGenerator(
            $template,
            $request->card_data,
            $request->is_double_sided ?? false
        );

        try {
            $pdfPath = $pdfGenerator->generate();
            
            // Retourner l'URL du PDF généré
            return response()->json([
                'success' => true,
                'preview_url' => Storage::url($pdfPath),
                'pdf_path' => $pdfPath
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la génération du PDF: ' . $e->getMessage()
            ], 500);
        }
    }
}
