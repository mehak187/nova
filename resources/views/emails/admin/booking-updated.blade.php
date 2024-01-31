@component('mail::message')
# Bonjour

Une Réservation a été modifiée:

- **Client:** {{ $shift->client->full_name }}
- **Date:** {{ $shift->started_at->format('d.m.Y') }} de {{ $shift->started_at->format('H\hi') }} à {{ $shift->ended_at->format('H\hi') }}
- **Espace:** {{ $shift->workspace->name }}

{{ config('app.name') }}
@endcomponent
