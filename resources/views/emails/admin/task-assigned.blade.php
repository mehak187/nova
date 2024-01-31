@component('mail::message')
# Bonjour,

Une tâche nommée "{{ $task->title }}" vous a été assignée.

@component('mail::button', ['url' => url('/cp/resources/tasks/' . $task->id)])
Voir la tâche
@endcomponent

<br>
L'Equipe Calliopée
@endcomponent
