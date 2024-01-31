@component('mail::message')
# Nouvelle demande support

Un demande de support a été créée par un client:
- Prénom: {{ $task->client->first_name }}
- Nom: {{ $task->client->last_name }}
- E-mail: {{ $task->client->email }}
- N° mobile: {{ $task->client->mobile_phone }}

<x-mail::panel>
**{{ $task->title }}**

{{ $task->description }}
</x-mail::panel>

@component('mail::button', ['url' => url('/cp/resources/tasks/' . $task->id)])
Voir la tâche
@endcomponent

{{ config('app.name') }}
@endcomponent
