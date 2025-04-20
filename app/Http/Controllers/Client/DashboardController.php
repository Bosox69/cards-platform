<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Affiche le tableau de bord client
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        
        // Récupération des commandes récentes
        $recentOrders = Order::where('client_id', $user->client_id)
            ->where('user_id', $user->id)
            ->with(['orderStatus', 'orderItems'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // Statistiques de commandes
        $stats = [
            'total' => Order::where('client_id', $user->client_id)->count(),
            'pending' => Order::where('client_id', $user->client_id)
                ->whereHas('orderStatus', function($query) {
                    $query->whereIn('name', ['Nouvelle', 'En traitement']);
                })->count(),
            'completed' => Order::where('client_id', $user->client_id)
                ->whereHas('orderStatus', function($query) {
                    $query->where('name', 'Complété');
                })->count(),
            'total_cards' => Order::where('client_id', $user->client_id)
                ->with('orderItems')
                ->get()
                ->sum(function ($order) {
                    return $order->orderItems->sum('quantity');
                })
        ];
        
        // Récupération des départements pour la création rapide d'une commande
        $departments = Department::where('client_id', $user->client_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        return view('client.dashboard', compact('recentOrders', 'stats', 'departments'));
    }
}
