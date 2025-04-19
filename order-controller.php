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
     */
    public function create()
    {
        $user = Auth::user();
        $departments = $user->client->departments;
        
        return view('client.orders.create', compact('departments'));
    }

    /**
     * Ajoute un article au panier (stocké en session)
     */
    public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'template_id' => 'required|exists:templates,id',
            'quantity' => 'required|in:100,200,400',
            'is_double_sided' => 'required|boolean',
            'card_data' => 'required|array'
        ]);
        
        $template = Template::findOrFail($validated['template_id']);
        
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
     */
    public function showCart()
    {
        $cart = session()->get('cart', []);
        return view('client.orders.cart', compact('cart'));
    }

    /**
     * Supprime un article du panier
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
     * Enregistre la commande en base de données
     */
    public function store(Request $request)
    {
        $cart = session()->get('cart', []);
        
        if(empty($cart)) {
            return redirect()->back()->with('error', 'Votre panier est vide');
        }
        
        $user = Auth::user();
        
        // Création de la commande
        $order = new Order();
        $order->client_id = $user->client_id;
        $order->user_id = $user->id;
        $order->order_status_id = OrderStatus::where('name', 'En attente')->first()->id;
        $order->comment = $request->comment;
        $order->save();
        
        // Création des items de la commande
        foreach($cart as $item) {
            $orderItem = new OrderItem();
            $orderItem->order_id = $order->id;
            $orderItem->template_id = $item['template_id'];
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
        Mail::to($user->email)->send(new OrderConfirmation($order));
        
        // Notification aux administrateurs
        $admins = \App\Models\User::where('is_admin', true)->get();
        foreach($admins as $admin) {
            Mail::to($admin->email)->send(new NewOrderNotification($order));
        }
        
        // Vider le panier
        session()->forget('cart');
        
        return redirect()->route('client.orders.show', $order->id)
            ->with('success', 'Votre commande a été enregistrée avec succès');
    }

    /**
     * Affiche les détails d'une commande
     */
    public function show(Order $order)
    {
        $user = Auth::user();
        
        // Vérifier que l'utilisateur a accès à cette commande
        if($user->is_admin || $order->client_id == $user->client_id) {
            $order->load(['orderItems.template', 'orderItems.cardData', 'orderStatus']);
            return view('client.orders.show', compact('order'));
        }
        
        return abort(403);
    }

    /**
     * Répète une commande précédente (copie dans le panier)
     */
    public function repeat(Order $order)
    {
        $user = Auth::user();
        
        // Vérifier que l'utilisateur a accès à cette commande
        if($order->client_id != $user->client_id) {
            return abort(403);
        }
        
        $cart = [];
        
        foreach($order->orderItems as $item) {
            $itemId = uniqid();
            $cart[$itemId] = [
                'template_id' => $item->template_id,
                'template_name' => $item->template->name,
                'department_id' => $item->template->department_id,
                'department_name' => $item->template->department->name,
                'quantity' => $item->quantity,
                'is_double_sided' => $item->is_double_sided,
                'card_data' => json_decode($item->cardData->data, true)
            ];
        }
        
        session()->put('cart', $cart);
        
        return redirect()->route('client.orders.cart')
            ->with('success', 'La commande a été copiée dans votre panier');
    }

    /**
     * Méthodes pour les administrateurs
     */
    
    /**
     * Liste toutes les commandes (admin)
     */
    public function adminIndex()
    {
        $orders = Order::with(['client', 'user', 'orderStatus'])
                    ->orderBy('created_at', 'desc')
                    ->paginate(15);
                    
        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Mise à jour du statut d'une commande (admin)
     */
    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'order_status_id' => 'required|exists:order_statuses,id'
        ]);
        
        $oldStatus = $order->orderStatus->name;
        $order->order_status_id = $validated['order_status_id'];
        $order->save();
        
        $newStatus = OrderStatus::find($validated['order_status_id'])->name;
        
        // Notification au client du changement de statut
        Mail::to($order->user->email)->send(new \App\Mail\OrderStatusUpdate($order, $oldStatus, $newStatus));
        
        return redirect()->back()
            ->with('success', 'Le statut de la commande a été mis à jour');
    }
}
