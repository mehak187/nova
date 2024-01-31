@component('mail::message')
# Cher·ère Client·e,

Nous avons bien enregistré votre réservation du {{ $shift->started_at->format('d.m.Y') }}, de {{ $shift->started_at->format('H\hi') }} à {{ $shift->ended_at->format('H\hi') }}.

Nous vous remercions pour votre confiance et nous réjouissons de vous accueillir à Calliopée Business Center.

L'Equipe Calliopée

mail@calliopee.ch<br>
+41 22 564 20 30
@endcomponent
