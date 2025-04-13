@component('mail::message')
# Confirmation de commande

Bonjour {{ $order->user->name }},

Nous vous confirmons la réception de votre commande de cartes de visite.

**Numéro de commande :** {{ $order->id }}  
**Date :** {{ $order->created_at->format('d/m/Y H:i') }}  
**Statut :** {{ $order->orderStatus->name }}

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

Vous pouvez suivre l'état de votre commande à tout moment sur notre plateforme.

@component('mail::button', ['url' => route('client.orders.show', $order->id)])
Voir ma commande
@endcomponent

Merci pour votre confiance,<br>
{{ config('app.name') }}
@endcomponent
