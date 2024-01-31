@component('mail::message')
# Cher·ère Client·e,

Votre réservation du {{ $shift->started_at->format('d.m.Y') }} a bien été annulée.

L'Equipe Calliopée

mail@calliopee.ch<br>
+41 22 564 20 30
@endcomponent
