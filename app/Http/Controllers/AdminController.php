<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Client;
use App\Models\Template;
use App\Models\OrderStatus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderStatusUpdate;

class AdminController extends Controller
{
    /**
     * Affiche la liste des commandes (interface admin)
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function orders(Request $request)
    {
        $query = Order::with(['client', 'user', 'orderStatus', 'orderItems']);
        
        // Filtres
        if ($request->has('status') && $request->status) {
            $query->where('order_status_id', $request->status);
        }
        
        if ($request->has('client') && $request->client) {
            $query->where('client_id', $request->client);
        }
        
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Tri
        $query->orderBy('created_at', 'desc');
        
        $orders = $query->paginate(15);
        $statuses = OrderStatus::orderBy('order')->get();
        $clients = Client::orderBy('name')->get();
        
        return view('admin.orders.index', compact('orders', 'statuses', 'clients'));
    }
    
    /**
     * Affiche les détails d'une commande (interface admin)
     *
     * @param Order $order
     * @return \Illuminate\View\View
     */
    public function showOrder(Order $order)
    {
        $order->load([
            'orderItems.template', 
            'orderItems.cardData', 
            'orderStatus',
            'client',
            'user',
            'statusHistory' => function($query) {
                $query->orderBy('created_at', 'desc');
            },
            'statusHistory.status',
            'statusHistory.user'
        ]);
        
        $statuses = OrderStatus::orderBy('order')->get();
        
        return view('admin.orders.show', compact('order', 'statuses'));
    }
    
    /**
     * Met à jour le statut d'une commande
     *
     * @param Request $request
     * @param Order $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateOrderStatus(Request $request, Order $order)
    {
        $request->validate([
            'order_status_id' => 'required|exists:order_status,id'
        ]);
        
        $oldStatusName = $order->orderStatus->name;
        $order->order_status_id = $request->order_status_id;
        $order->save();
        
        $newStatus = OrderStatus::find($request->order_status_id);
        
        // Enregistrer l'historique du changement de statut
        $order->statusHistory()->create([
            'order_status_id' => $request->order_status_id,
            'user_id' => auth()->id(),
            'comment' => $request->status_comment
        ]);
        
        // Envoyer un email au client pour l'informer du changement de statut
        try {
            Mail::to($order->user->email)
                ->send(new OrderStatusUpdate($order, $oldStatusName, $newStatus->name));
        } catch (\Exception $e) {
            // Log l'erreur mais continuer le processus
            \Log::error('Error sending status update email: ' . $e->getMessage());
        }
        
        return redirect()->back()
            ->with('success', 'Le statut de la commande a été mis à jour avec succès');
    }
    
    /**
     * Affiche la liste des modèles (templates) dans l'interface admin
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function templates(Request $request)
    {
        return redirect()->route('admin.templates.index');
    }
    
    /**
     * Affiche le tableau de bord administrateur
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        // Statistiques générales
        $stats = [
            'total_orders' => Order::count(),
            'pending_orders' => Order::whereHas('orderStatus', function($query) {
                $query->whereIn('name', ['Nouvelle', 'En traitement']);
            })->count(),
            'processing_orders' => Order::whereHas('orderStatus', function($query) {
                $query->where('name', 'En production');
            })->count(),
            'completed_orders' => Order::whereHas('orderStatus', function($query) {
                $query->where('name', 'Complété');
            })->count(),
            'clients_count' => Client::count(),
            'templates_count' => Template::count()
        ];
        
        // Commandes récentes
        $recentOrders = Order::with(['client', 'user', 'orderStatus'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
            
        // Commandes par statut
        $ordersByStatus = OrderStatus::withCount('orders')
            ->orderBy('order')
            ->get();
            
        return view('admin.dashboard', compact('stats', 'recentOrders', 'ordersByStatus'));
    }
}
