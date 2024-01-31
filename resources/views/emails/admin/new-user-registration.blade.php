@component('mail::message')
# Nouvel utilisateur inscrit

Un nouvel utilisateur s'est inscrit sur la plateforme:
- Prénom: {{ $client->first_name }}
- Nom: {{ $client->last_name }}
- E-mail: {{ $client->email }}
- N° mobile: {{ $client->mobile_phone }}

@component('mail::button', ['url' => url('/cp/resources/clients/' . $client->id)])
Voir le client
@endcomponent

{{ config('app.name') }}
@endcomponent
