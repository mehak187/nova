@component('mail::message')
# Cher·ère Client·e,

Le status de votre demande de support "{{ $task->title }}" est maintenant **{{ ['in_progress' => 'en cours', 'closed' => 'terminée'][$task->status] ?? '' }}**

L'Equipe Calliopée

mail@calliopee.ch<br>
+41 22 564 20 30
@endcomponent
