@component('mail::message')
# Bonjour

La réservation suivante a été annulée:

- **Client:** {{ $shift->client->full_name }}
- **Date:** {{ $shift->started_at->format('d.m.Y') }} à {{ $shift->started_at->format('H\hi') }}
- **Espace:** {{ $shift->workspace->name }}

@if (!$shift->is_cancellable)
Attention: L'annulation a été faite moins de 72h avant le début.
@endif

{{ config('app.name') }}
@endcomponent
