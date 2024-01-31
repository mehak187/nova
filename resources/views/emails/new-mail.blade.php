@component('mail::message')
**Bonjour {{ $firstName }} {{ $lastName }},**

Votre courrier du jour a été traité par nos soins et est disponible dans votre boîte aux lettres. Si vous avez opté pour la formule de renvoi, votre courrier vous sera transmis par La Poste conformément à votre contrat de domiciliation.

**Description du courrier :**

@foreach ($content as $item)
- {{ $item['qty'] }} x {{ $item['description'] ?? $item['type'] }}<br>
@endforeach
<br>

Avec nos cordiales salutations,<br>
L'Équipe Calliopée
@endcomponent
