<?php

namespace App\Services;

use App\Models\Template;
use Fpdf\Fpdf;
use Illuminate\Support\Facades\Storage;

class CardPdfGenerator
{
    protected $pdf;
    protected $template;
    protected $cardData;
    protected $isDoubleSided;
    protected $cardWidth = 85;  // mm
    protected $cardHeight = 55; // mm
    
    /**
     * Créer une nouvelle instance du générateur
     *
     * @param Template $template
     * @param array $cardData
     * @param bool $isDoubleSided
     */
    public function __construct(Template $template, array $cardData, bool $isDoubleSided = false)
    {
        $this->template = $template;
        $this->cardData = $cardData;
        $this->isDoubleSided = $isDoubleSided;
        
        // Initialiser le PDF
        $this->pdf = new Fpdf('L', 'mm', array($this->cardWidth, $this->cardHeight));
        $this->pdf->SetMargins(5, 5, 5);
        $this->pdf->SetAutoPageBreak(false);
    }
    
    /**
     * Générer le PDF de la carte
     *
     * @return string Le chemin du fichier PDF généré
     */
    public function generate()
    {
        // Générer le recto
        $this->generateFront();
        
        // Générer le verso si demandé
        if ($this->isDoubleSided) {
            $this->generateBack();
        }
        
        // Générer un nom de fichier unique
        $filename = 'previews/card_' . uniqid() . '.pdf';
        
        // Sauvegarder le PDF dans le stockage
        Storage::put('public/' . $filename, $this->pdf->Output('S'));
        
        return $filename;
    }
    
    /**
     * Générer le recto de la carte
     */
    protected function generateFront()
    {
        $this->pdf->AddPage();
        
        // Appliquer le fond du modèle s'il existe
        if ($this->template->background_front) {
            $this->addBackground($this->template->background_front);
        }
        
        // Logo de l'entreprise
        if ($this->template->client->logo) {
            $this->addLogo($this->template->client->logo);
        }
        
        // Ajouter les informations de la carte
        $this->addCardInformation();
    }
    
    /**
     * Générer le verso de la carte
     */
    protected function generateBack()
    {
        $this->pdf->AddPage();
        
        // Appliquer le fond du modèle s'il existe
        if ($this->template->background_back) {
            $this->addBackground($this->template->background_back);
        }
        
        // Contenu du verso selon le modèle
        $this->addBackContent();
    }
    
    /**
     * Ajouter un fond au PDF
     *
     * @param string $backgroundPath
     */
    protected function addBackground($backgroundPath)
    {
        $fullPath = storage_path('app/public/' . $backgroundPath);
        if (file_exists($fullPath)) {
            $this->pdf->Image($fullPath, 0, 0, $this->cardWidth, $this->cardHeight);
        }
    }
    
    /**
     * Ajouter le logo au PDF
     *
     * @param string $logoPath
     */
    protected function addLogo($logoPath)
    {
        $fullPath = storage_path('app/public/' . $logoPath);
        if (file_exists($fullPath)) {
            // Positionner le logo selon le modèle
            $logoX = $this->template->logo_x ?? 5;
            $logoY = $this->template->logo_y ?? 5;
            $logoWidth = $this->template->logo_width ?? 20;
            
            $this->pdf->Image($fullPath, $logoX, $logoY, $logoWidth);
        }
    }
    
    /**
     * Ajouter les informations personnelles à la carte
     */
    protected function addCardInformation()
    {
        // Position initiale des textes
        $startX = $this->template->text_start_x ?? 5;
        $startY = $this->template->text_start_y ?? 25;
        $currentY = $startY;
        
        // Définir les polices selon le modèle
        $this->pdf->SetFont('Helvetica', 'B', 11);
        
        // Nom complet
        if (!empty($this->cardData['fullName'])) {
            $this->pdf->SetXY($startX, $currentY);
            $this->pdf->Cell(0, 6, utf8_decode($this->cardData['fullName']), 0, 1);
            $currentY += 6;
        }
        
        // Titre / Fonction
        if (!empty($this->cardData['jobTitle'])) {
            $this->pdf->SetFont('Helvetica', 'I', 9);
            $this->pdf->SetXY($startX, $currentY);
            $this->pdf->Cell(0, 5, utf8_decode($this->cardData['jobTitle']), 0, 1);
            $currentY += 7;
        }
        
        // Informations de contact
        $this->pdf->SetFont('Helvetica', '', 8);
        
        // Adresse
        if (!empty($this->cardData['address'])) {
            $this->pdf->SetXY($startX, $currentY);
            $this->pdf->MultiCell(75, 4, utf8_decode($this->cardData['address']), 0, 'L');
            $currentY += 10;
        }
        
        // Téléphone fixe
        if (!empty($this->cardData['phone'])) {
            $this->pdf->SetXY($startX, $currentY);
            $this->pdf->Cell(0, 4, 'Tel: ' . $this->cardData['phone'], 0, 1);
            $currentY += 4;
        }
        
        // Mobile
        if (!empty($this->cardData['mobile'])) {
            $this->pdf->SetXY($startX, $currentY);
            $this->pdf->Cell(0, 4, 'Mobile: ' . $this->cardData['mobile'], 0, 1);
            $currentY += 4;
        }
        
        // Email
        if (!empty($this->cardData['email'])) {
            $this->pdf->SetXY($startX, $currentY);
            $this->pdf->Cell(0, 4, 'Email: ' . $this->cardData['email'], 0, 1);
        }
    }
    
    /**
     * Ajouter le contenu au verso de la carte
     */
    protected function addBackContent()
    {
        // Contenu personnalisé selon le modèle de carte
        if ($this->template->back_content) {
            $this->pdf->SetFont('Helvetica', '', 9);
            $this->pdf->SetXY(5, 15);
            $this->pdf->MultiCell(75, 5, utf8_decode($this->template->back_content), 0, 'L');
        }
    }
    
    /**
     * Obtenir l'URL publique du PDF généré
     *
     * @param string $filePath
     * @return string
     */
    public static function getPublicUrl($filePath)
    {
        return Storage::url($filePath);
    }
}
