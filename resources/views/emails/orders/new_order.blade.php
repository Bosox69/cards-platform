@component('mail::message')
# Nouvelle commande

Une nouvelle commande a été passée sur la plateforme de cartes de visite.

**Numéro de commande :** {{ $order->id }}  
**Client :** {{ $order->client->name }}  
**Utilisateur :** {{ $order->user->name }}  
**Date :** {{ $order->created_at->format('d/m/Y H:i') }}

## Détails de la commande

@component('mail::table')
| Modèle | Nom | Quantité | Recto/Verso |
|:-------|:----|:---------|:------------|
@foreach($order->orderItems as $item)
@php
    $cardData = json_decode($item->cardData->data, true);
@endphp
| {{ $item->template->name }} | {{ $cardData['fullName'] ?? 'N/A' }} | {{ $item->quantity }} | {{ $item->is_double_sided ? 'Oui' : 'Non' }} |
@endforeach
@endcomponent

@if($order->comment)
## Commentaires
{{ $order->comment }}
@endif

@component('mail::button', ['url' => route('admin.orders.show', $order->id)])
Voir la commande
@endcomponent

Merci,<br>
{{ config('app.name') }}
@endcomponent
