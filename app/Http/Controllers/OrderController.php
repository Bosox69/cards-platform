<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CardData;
use App\Models\Template;
use App\Models\Department;
use App\Models\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderConfirmation;
use App\Mail\NewOrderNotification;
use App\Services\CardPdfGenerator;

class OrderController extends Controller
{
    /**
     * Affiche la liste des commandes pour l'utilisateur authentifié
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        $orders = Order::where('client_id', $user->client_id)
                    ->orderBy('created_at', 'desc')
                    ->with(['orderStatus', 'orderItems'])
                    ->paginate(10);
                    
        return view('client.orders.index', compact('orders'));
    }

    /**
     * Affiche le formulaire de création d'une nouvelle commande
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        
        // Si un département est spécifié dans l'URL
        $selectedDepartment = null;
        if ($request->has('department_id')) {
            $selectedDepartment = Department::where('client_id', $user->client_id)
                ->where('id', $request->department_id)
                ->where('is_active', true)
                ->first();
        }
        
        // Récupération de tous les départements du client
        $departments = Department::where('client_id', $user->client_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        return view('client.orders.create', compact('departments', 'selectedDepartment'));
    }

    /**
     * Ajoute un article au panier (stocké en session)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'template_id' => 'required|exists:templates,id',
            'quantity' => 'required|in:100,200,400',
            'is_double_sided' => 'required|boolean',
            'card_data' => 'required|array',
            'card_data.fullName' => 'required|string|max:100',
            'card_data.jobTitle' => 'required|string|max:100',
            'card_data.email' => 'nullable|email|max:100',
            'card_data.phone' => 'nullable|string|max:20',
            'card_data.mobile' => 'nullable|string|max:20',
            'card_data.address' => 'nullable|string|max:255',
        ]);
        
        $template = Template::with('department')->findOrFail($validated['template_id']);
        
        // Vérifier que le template appartient bien au client
        $user = Auth::user();
        if ($template->department->client_id != $user->client_id) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'avez pas accès à ce modèle'
            ], 403);
        }
        
        // Récupération du panier depuis la session ou création d'un nouveau
        $cart = session()->get('cart', []);
        
        // Générer un ID temporaire pour cet élément du panier
        $itemId = uniqid();
        
        $cart[$itemId] = [
            'template_id' => $validated['template_id'],
            'template_name' => $template->name,
            'department_id' => $template->department_id,
            'department_name' => $template->department->name,
            'quantity' => $validated['quantity'],
            'is_double_sided' => $validated['is_double_sided'],
            'card_data' => $validated['card_data']
        ];
        
        session()->put('cart', $cart);
        
        return response()->json([
            'success' => true,
            'message' => 'Article ajouté au panier',
            'cart' => $cart
        ]);
    }

    /**
     * Affiche le contenu du panier
     *
     * @return \Illuminate\View\View
     */
    public function showCart()
    {
        $cart = session()->get('cart', []);
        return view('client.orders.cart', compact('cart'));
    }

    /**
     * Supprime un article du panier
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeFromCart(Request $request)
    {
        $itemId = $request->itemId;
        $cart = session()->get('cart', []);
        
        if(isset($cart[$itemId])) {
            unset($cart[$itemId]);
            session()->put('cart', $cart);
        }
        
        return redirect()->back()->with('success', 'Article supprimé du panier');
    }

    /**
     * Prévisualise une carte dans le panier
     *
     * @param string $itemId
     * @return \Illuminate\View\View
     */
    public function previewCard($itemId)
    {
        $cart = session()->get('cart', []);
        
        if (!isset($cart[$itemId])) {
            return redirect()->route('client.orders.cart')
                ->with('error', 'Article non trouvé dans le panier');
        }
        
        $item = $cart[$itemId];
        $template = Template::findOrFail($item['template_id']);
        
        // Générer le PDF de prévisualisation
        $pdfGenerator = new CardPdfGenerator(
            $template,
            $item['card_data'],
            $item['is_double_sided']
        );
        
        $pdfPath = $pdfGenerator->generate();
        $pdfUrl = CardPdfGenerator::getPublicUrl($pdfPath);
        
        return view('client.orders.preview', compact('item', 'pdfUrl'));
    }

    /**
     * Édite un élément du panier
     *
     * @param string $itemId
     * @return \Illuminate\View\View
     */
    public function editCartItem($itemId)
    {
        $cart = session()->get('cart', []);
        
        if (!isset($cart[$itemId])) {
            return redirect()->route('client.orders.cart')
                ->with('error', 'Article non trouvé dans le panier');
        }
        
        $item = $cart[$itemId];
        $template = Template::with('department')->findOrFail($item['template_id']);
        $department = $template->department;
        
        return view('client.orders.edit', compact('item', 'template', 'department', 'itemId'));
    }

    /**
     * Enregistre la commande en base de données
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $cart = session()->get('cart', []);
        
        if(empty($cart)) {
            return redirect()->back()->with('error', 'Votre panier est vide');
        }
        
        $user = Auth::user();
        
        // Trouver le statut "Nouvelle" pour les commandes
        $newStatus = OrderStatus::where('name', 'Nouvelle')->first();
        if (!$newStatus) {
            // Si le statut n'existe pas, utiliser le premier statut disponible
            $newStatus = OrderStatus::orderBy('order')->first();
        }
        
        // Création de la commande
        $order = new Order();
        $order->client_id = $user->client_id;
        $order->user_id = $user->id;
        $order->order_status_id = $newStatus->id;
        $order->comment = $request->comment;
        $order->save();
        
        // Création des items de la commande
        foreach($cart as $item) {
            $template = Template::findOrFail($item['template_id']);
            
            $orderItem = new OrderItem();
            $orderItem->order_id = $order->id;
            $orderItem->template_id = $item['template_id'];
            $orderItem->department_id = $template->department_id;
            $orderItem->quantity = $item['quantity'];
            $orderItem->is_double_sided = $item['is_double_sided'];
            $orderItem->save();
            
            // Enregistrement des données personnalisées
            $cardData = new CardData();
            $cardData->order_item_id = $orderItem->id;
            $cardData->data = json_encode($item['card_data']);
            $cardData->save();
        }
        
        // Envoi des emails de confirmation
        try {
            Mail::to($user->email)->send(new OrderConfirmation($order));
            
            // Notification aux administrateurs
            $admins = \App\Models\User::where('is_admin', true)->get();
            foreach($admins as $admin) {
                Mail::to($admin->email)->send(new NewOrderNotification($order));
            }
        } catch (\Exception $e) {
            // Log l'erreur d'envoi d'email mais continuer le processus
            \Log::error('Error sending order emails: ' . $e->getMessage());
        }
        
        // Vider le panier
        session()->forget('cart');
        
        return redirect()->route('client.orders.show', $order->id)
            ->with('success', 'Votre commande a été enregistrée avec succès');
    }

    /**
     * Affiche les détails d'une commande
     *
     * @param Order $order
     * @return \Illuminate\View\View
     */
    public function show(Order $order)
    {
        $user = Auth::user();
        
        // Vérifier que l'utilisateur a accès à cette commande
        if ($order->client_id != $user->client_id) {
            abort(403, 'Vous n\'avez pas accès à cette commande');
        }
        
        $order->load(['orderItems.template', 'orderItems.cardData', 'orderStatus']);
        
        return view('client.orders.show', compact('order'));
    }

    /**
     * Répète une commande précédente (copie dans le panier)
     *
     * @param Order $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function repeat(Order $order)
    {
        $user = Auth::user();
        
        // Vérifier que l'utilisateur a accès à cette commande
        if ($order->client_id != $user->client_id) {
            abort(403, 'Vous n\'avez pas accès à cette commande');
        }
        
        $cart = [];
        
        foreach($order->orderItems as $item) {
            // Skip si les données de carte ou le template sont manquants
            if (!$item->cardData || !$item->template) {
                continue;
            }
            
            $itemId = uniqid();
            $cart[$itemId] = [
                'template_id' => $item->template_id,
                'template_name' => $item->template->name,
                'department_id' => $item->department_id,
                'department_name' => $item->department->name,
                'quantity' => $item->quantity,
                'is_double_sided' => $item->is_double_sided,
                'card_data' => json_decode($item->cardData->data, true)
            ];
        }
        
        session()->put('cart', $cart);
        
        return redirect()->route('client.orders.cart')
            ->with('success', 'La commande a été copiée dans votre panier');
    }
}
