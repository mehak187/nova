@component('mail::message')
# Réinitialisation du mot de passe

Bonjour,

Une demande de réinitialisation du mot de passe a été effectuée pour votre compte. Si vous n'êtes pas à l'origine de cette demande, veuillez ignorer cet e-mail.

@component('mail::button', ['url' => $url])
Changer mon mot de passe
@endcomponent

Merci,<br>
{{ config('app.name') }}
@endcomponent
