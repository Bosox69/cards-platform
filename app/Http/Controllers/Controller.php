<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CardData;
use App\Models\Template;
use App\Models\Department;
use App\Models\OrderStatus;
use App\Services\CardPdfGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderConfirmation;
use App\Mail\NewOrderNotification;
use App\Events\OrderStatusChanged;

class OrderController extends Controller
{
    /**
     * Affiche la liste des commandes pour l'utilisateur connecté.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Order::query()->with(['orderStatus', 'orderItems']);

        // Si l'utilisateur n'est pas administrateur, filtrer par client
        if (!$user->is_admin) {
            $query->where('client_id', $user->client_id);
        } else if ($request->has('client_id')) {
            $query->where('client_id', $request->client_id);
        }
        
        // Filtrage par statut
        if ($request->has('status_id')) {
            $query->where('order_status_id', $request->status_id);
        }
        
        // Filtrage par date
        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $orders = $query->orderBy('created_at', 'desc')->paginate(10);
        
        // Retourne différentes vues selon le type d'utilisateur
        if ($user->is_admin) {
            return view('admin.orders.index', compact('orders'));
        }
        
        return view('client.orders.index', compact('orders'));
    }

    /**
     * Affiche le formulaire de création d'une nouvelle commande.
     */
    public function create()
    {
        $user = Auth::user();
        $departments = Department::where('client_id', $user->client_id)
                                ->where('is_active', true)
                                ->get();
        
        return view('client.orders.create', compact('departments'));
    }
    
    /**
     * Ajoute un article au panier.
     */
    public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'template_id' => 'required|exists:templates,id',
            'quantity' => 'required|in:100,200,400',
            'is_double_sided' => 'required|boolean',
            'card_data' => 'required|array',
            'card_data.*' => 'nullable|string|max:255',
        ]);
        
        // Récupérer le template pour s'assurer qu'il est accessible par cet utilisateur
        $template = Template::findOrFail($validated['template_id']);
        $department = $template->department;
        
        // Vérifier que l'utilisateur a accès à ce département
        $user = Auth::user();
        if (!$user->is_admin && $department->client_id !== $user->client_id) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'avez pas accès à ce département'
            ], 403);
        }
        
        // Récupérer le panier depuis la session ou créer un nouveau
        $cart = session()->get('cart', []);
        
        // Générer un ID temporaire pour cet élément du panier
        $itemId = uniqid();
        
        $cart[$itemId] = [
            'template_id' => $validated['template_id'],
            'template_name' => $template->name,
            'department_id' => $department->id,
            'department_name' => $department->name,
            'quantity' => $validated['quantity'],
            'is_double_sided' => $validated['is_double_sided'],
            'card_data' => $validated['card_data']
        ];
        
        session()->put('cart', $cart);
        
        // Générer la prévisualisation PDF si demandé
        if ($request->has('generate_preview') && $request->generate_preview) {
            try {
                $pdfGenerator = new CardPdfGenerator(
                    $template,
                    $validated['card_data'],
                    $validated['is_double_sided']
                );
                
                $pdfPath = $pdfGenerator->generate();
                $cart[$itemId]['preview_pdf'] = $pdfPath;
                session()->put('cart', $cart);
            } catch (\Exception $e) {
                // Log l'erreur mais continuer
                \Log::error('Erreur de génération de prévisualisation PDF: ' . $e->getMessage());
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Article ajouté au panier',
            'cart' => $cart,
            'cart_count' => count($cart)
        ]);
    }

    /**
     * Affiche le contenu du panier.
     */
    public function showCart()
    {
        $cart = session()->get('cart', []);
        return view('client.orders.cart', compact('cart'));
    }

    /**
     * Supprime un article du panier.
     */
    public function removeFromCart(Request $request)
    {
        $itemId = $request->input('itemId');
        $cart = session()->get('cart', []);
        
        if (isset($cart[$itemId])) {
            unset($cart[$itemId]);
            session()->put('cart', $cart);
        }
        
        return redirect()->back()->with('success', 'Article supprimé du panier');
    }

    /**
     * Enregistre la commande.
     */
    public function store(Request $request)
    {
        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('client.orders.create')
                ->with('error', 'Votre panier est vide');
        }
        
        $user = Auth::user();
        
        // Début de transaction pour garantir l'intégrité des données
        \DB::beginTransaction();
        
        try {
            // Créer la commande
            $order = new Order();
            $order->user_id = $user->id;
            $order->client_id = $user->client_id;
            $order->order_status_id = OrderStatus::where('name', 'Nouvelle')->first()->id;
            $order->comment = $request->input('comment');
            $order->save();
            
            // Créer les items de la commande
            foreach ($cart as $item) {
                $orderItem = new OrderItem();
                $orderItem->order_id = $order->id;
                $orderItem->template_id = $item['template_id'];
                $orderItem->department_id = $item['department_id'];
                $orderItem->quantity = $item['quantity'];
                $orderItem->is_double_sided = $item['is_double_sided'];
                
                // Sauvegarder la prévisualisation PDF si elle existe
                if (isset($item['preview_pdf'])) {
                    $orderItem->pdf_preview = $item['preview_pdf'];
                }
                
                $orderItem->save();
                
                // Enregistrer les données personnalisées
                $cardData = new CardData();
                $cardData->order_item_id = $orderItem->id;
                $cardData->data = json_encode($item['card_data']);
                $cardData->save();
            }
            
            // Valider la transaction
            \DB::commit();
            
            // Envoyer les emails
            Mail::to($user->email)->queue(new OrderConfirmation($order));
            
            // Notifier les administrateurs
            $admins = User::where('is_admin', true)->get();
            foreach ($admins as $admin) {
                Mail::to($admin->email)->queue(new NewOrderNotification($order));
            }
            
            // Vider le panier
            session()->forget('cart');
            
            return redirect()->route('client.orders.show', $order->id)
                ->with('success', 'Votre commande a été enregistrée avec succès');
                
        } catch (\Exception $e) {
            // Annuler la transaction en cas d'erreur
            \DB::rollBack();
            
            \Log::error('Erreur lors de la création de la commande: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de l\'enregistrement de votre commande. Veuillez réessayer.')
                ->withInput();
        }
    }

    /**
     * Affiche les détails d'une commande.
     */
    public function show(Order $order)
    {
        $user = Auth::user();
        
        // Vérifier que l'utilisateur a accès à cette commande
        if (!$user->is_admin && $order->client_id !== $user->client_id) {
            abort(403, 'Accès non autorisé');
        }
        
        $order->load(['orderItems.template', 'orderItems.cardData', 'orderStatus', 'user']);
        
        if ($user->is_admin) {
            $statuses = OrderStatus::orderBy('order')->get();
            return view('admin.orders.show', compact('order', 'statuses'));
        }
        
        return view('client.orders.show', compact('order'));
    }

    /**
     * Met à jour le statut d'une commande (admin seulement).
     */
    public function updateStatus(Request $request, Order $order)
    {
        $this->authorize('update', $order);
        
        $validated = $request->validate([
            'order_status_id' => 'required|exists:order_status,id'
        ]);
        
        $oldStatus = $order->orderStatus->name;
        $order->order_status_id = $validated['order_status_id'];
        $order->save();
        
        $newStatus = OrderStatus::find($validated['order_status_id'])->name;
        
        // Enregistrer l'historique de changement de statut
        $order->statusHistory()->create([
            'order_status_id' => $validated['order_status_id'],
            'user_id' => Auth::id(),
        ]);
        
        // Notification par email du changement de statut
        Mail::to($order->user->email)->queue(new OrderStatusUpdate($order, $oldStatus, $newStatus));
        
        // Déclencher un événement pour d'autres traitements éventuels
        event(new OrderStatusChanged($order, $oldStatus, $newStatus));
        
        return redirect()->back()
            ->with('success', 'Le statut de la commande a été mis à jour');
    }

    /**
     * Permet de répéter une commande précédente.
     */
    public function repeat(Order $order)
    {
        $user = Auth::user();
        
        // Vérifier que l'utilisateur a accès à cette commande
        if (!$user->is_admin && $order->client_id !== $user->client_id) {
            abort(403, 'Accès non autorisé');
        }
        
        $order->load(['orderItems.template', 'orderItems.cardData']);
        
        $cart = [];
        
        foreach ($order->orderItems as $item) {
            $itemId = uniqid();
            
            // Récupérer les données de personnalisation
            $cardData = json_decode($item->cardData->data, true);
            
            $cart[$itemId] = [
                'template_id' => $item->template_id,
                'template_name' => $item->template->name,
                'department_id' => $item->department_id,
                'department_name' => $item->department->name,
                'quantity' => $item->quantity,
                'is_double_sided' => $item->is_double_sided,
                'card_data' => $cardData,
                'preview_pdf' => $item->pdf_preview
            ];
        }
        
        session()->put('cart', $cart);
        
        return redirect()->route('client.orders.cart')
            ->with('success', 'La commande précédente a été copiée dans votre panier. Vous pouvez maintenant la modifier ou la valider.');
    }

    /**
     * Prévisualise une carte spécifique.
     */
    public function previewCard($orderItemId)
    {
        $orderItem = OrderItem::with(['template', 'cardData'])->findOrFail($orderItemId);
        
        $user = Auth::user();
        
        // Vérifier que l'utilisateur a accès à cette commande
        if (!$user->is_admin && $orderItem->order->client_id !== $user->client_id) {
            abort(403, 'Accès non autorisé');
        }
        
        // Rediriger vers le PDF s'il existe déjà
        if ($orderItem->pdf_preview) {
            return redirect()->to(Storage::url($orderItem->pdf_preview));
        }
        
        // Sinon générer le PDF
        $cardData = json_decode($orderItem->cardData->data, true);
        
        $pdfGenerator = new CardPdfGenerator(
            $orderItem->template,
            $cardData,
            $orderItem->is_double_sided
        );
        
        $pdfPath = $pdfGenerator->generate();
        
        // Sauvegarder le chemin du PDF dans l'item de commande
        $orderItem->pdf_preview = $pdfPath;
        $orderItem->save();
        
        return redirect()->to(Storage::url($pdfPath));
    }
}
