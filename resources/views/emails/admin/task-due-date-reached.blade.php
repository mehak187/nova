@component('mail::message')
# Bonjour {{ $user->first_name }},

Certaines de vos tâches ont atteint leur date d'échéance.

<table style="width:100%;">
<thead>
<tr>
<td style="font-weight:bold;">Titre</td>
<td style="font-weight:bold;">Statut</td>
<td style="font-weight:bold;">Date</td>
</tr>
</thead>
<tbody>
@foreach ($tasks as $task)
<tr>
<td>{{ $task->title }}</td>
<td style="padding:0.2rem;">{{ __('task_status.'.$task->status) }}</td>
<td>{{ $task->due_date?->format('d.m.Y') }}</td>
</tr>
@endforeach
</tbody>
</table>

@component('mail::button', ['url' => url('/cp/resources/tasks/lens/my-tasks')])
Voir mes tâches
@endcomponent

<br>
L'Equipe Calliopée
@endcomponent
