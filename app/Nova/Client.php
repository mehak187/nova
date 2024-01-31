<?php

namespace App\Nova;

use App\Nova\Deposit;
use Eminiarts\Tabs\Tab;
use Laravel\Nova\Panel;
use Eminiarts\Tabs\Tabs;
use Laravel\Nova\Fields\Line;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Stack;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Country;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\Textarea;
use Eminiarts\Tabs\Traits\HasTabs;
use Illuminate\Support\Facades\Date;
use Laravel\Nova\Fields\BooleanGroup;
use FirstpointCh\OpenInvoices\OpenInvoices;
use Laravel\Nova\Http\Requests\NovaRequest;
use Ebess\AdvancedNovaMediaLibrary\Fields\Files;
use App\Nova\Actions\SendYouHaveNewMailNotification;

class Client extends Resource
{
    use HasTabs;

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Client::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'full_name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'company', 'first_name', 'last_name', 'email', 'mobile_phone'
    ];

    public static $group = 'Clients';

    public static function label()
    {
        return __('Clients');
    }

    public static function singularLabel()
    {
        return __('Client');
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            OpenInvoices::make(),

            Tabs::make($this->full_name, [
                Tab::make('Informations', [
                    Heading::make('<div class="bg-gray-50 font-bold" style="margin:-1rem -1.5rem -1rem -1.5rem;padding:1rem 1.5rem 1rem 1.5rem">Entreprise</div>')->asHtml(),

                    Text::make('Entreprise', 'company'),

                    Textarea::make('Description de l\'entreprise', 'company_description')
                        ->nullable(),

                    Text::make('Type d\'entreprise', 'company_type')
                        ->nullable(),

                    Text::make('N° RC', 'company_registration_number')
                        ->nullable(),

                    Text::make('N° affiliation OCAS / FER', 'company_ocas_affiliation_number')
                        ->nullable(),

                    Heading::make('<div class="bg-gray-50 font-bold" style="margin:-1rem -1.5rem -1rem -1.5rem;padding:1rem 1.5rem 1rem 1.5rem">Coordonnées</div>')->asHtml(),

                    Text::make('Prénom', 'first_name')
                        ->required(),

                    Text::make('Nom', 'last_name')
                        ->required(),

                    Text::make('Tél. mobile', 'mobile_phone')
                        ->nullable(),

                    Text::make('Email')
                        ->rules('required', 'email', 'max:254')
                        ->creationRules('unique:clients,email')
                        ->updateRules('unique:clients,email,{{resourceId}}'),

                    Text::make('Adresse', 'address')
                        ->nullable(),

                    Text::make('Code postal', 'zip')
                        ->nullable(),

                    Text::make('Ville', 'city')
                        ->nullable(),

                    Select::make('Pays', 'country')
                        ->options([
                            'CH' => 'Suisse',
                            'FR' => 'France',
                        ])
                        ->displayUsingLabels()
                        ->nullable(),

                    Country::make('Nationalité', 'nationality'),

                    DateTime::make('Date expiration ID', 'id_card_expiry_date')
                        ->displayUsing(function ($value) {
                            return $value?->format('d.m.Y');
                        })
                        ->nullable(),

                    Select::make('Permis de séjour', 'residence_permit')
                        ->options([
                            'b' => 'Livret B (autorisation de séjour)',
                            'c' => 'Livret C (autorisation d’établissement)',
                            'ci' => 'Livret Ci (autorisation de séjour avec activité lucrative pour les internationaux)',
                            'g' => 'Livret G (autorisation frontalière)',
                            'l' => 'Livret L (autorisation de courte durée)',
                            'f' => 'Livret F (pour étrangers admis provisoirement)',
                            'n' => 'Livret N (pour requérants d‘asile)',
                            's' => 'Livret S (protection provisoire aux personnes à protéger)',
                        ])
                        ->displayUsingLabels()
                        ->nullable(),

                    DateTime::make('Date expiration permis de séjour', 'residence_permit_expiry_date')
                        ->displayUsing(function ($value) {
                            return $value?->format('d.m.Y');
                        })
                        ->nullable(),

                    Heading::make('<div class="bg-gray-50 font-bold" style="margin:-1rem -1.5rem -1rem -1.5rem;padding:1rem 1.5rem 1rem 1.5rem">Paramètres de connexion</div>')
                        ->asHtml()
                        ->onlyOnForms(),

                    Password::make('Mot de passe', 'password')
                        ->onlyOnForms()
                        ->creationRules('nullable', 'string', 'min:8')
                        ->updateRules('nullable', 'string', 'min:8'),

                    Heading::make('<div class="bg-gray-50 font-bold" style="margin:-1rem -1.5rem -1rem -1.5rem;padding:1rem 1.5rem 1rem 1.5rem">Paramètres de l\'App</div>')->asHtml(),

                    Boolean::make('Abonné ?', 'has_subscription'),
                    Boolean::make('Domicilié ?', 'is_resident'),
                    Boolean::make('235 ?', 'is_235'),
                    Boolean::make('Accès porte', 'door_access_enabled'),

                    Number::make('Minutes restantes (tables)', 'purchased_minutes_table')
                        ->default(0)
                        ->rules('required'),

                    Number::make('Minutes restantes (bureaux)', 'purchased_minutes_office')
                        ->default(0)
                        ->rules('required'),

                    Number::make('Minutes 235 restantes (tables)', 'included_minutes_table')
                        ->default(0)
                        ->rules('required'),

                    Number::make('Minutes 235 restantes (bureaux)', 'included_minutes_office')
                        ->default(0)
                        ->rules('required'),

                    Select::make('Type 235', 'refill_type')->options([
                        'table' => 'Tables',
                        'office' => 'Bureaux',
                    ])->displayUsingLabels(),

                    Text::make('ID Sumup', 'sumup_id')
                        ->nullable(),

                    Heading::make('<div class="bg-gray-50 font-bold" style="margin:-1rem -1.5rem -1rem -1.5rem;padding:1rem 1.5rem 1rem 1.5rem">Informations Calliopée</div>')->asHtml(),

                    Text::make('Code boîte aux lettres', 'mailbox_code')
                        ->nullable(),

                    Text::make('Type de domiciliation', 'domiciliation_type')
                        ->nullable(),

                    BooleanGroup::make('Prestations', 'services')
                        ->options([
                            'formule-235' => 'Formule 235',
                            'formule-a' => 'Formule A',
                            'formule-as' => 'Formule AS',
                            'formule-b' => 'Formule B',
                            'formule-bs' => 'Formule BS',
                            'sous-adresse-poste' => 'Sous-adresse poste',
                            'scanner' => 'Scanner',
                            'renvoyer-courrier-par-poste' => 'Renvoyer courrier par poste',
                            'ligne-telephonique' => 'Ligne téléphonique',
                            'scanner-renvoyer' => 'Scanner + Renvoyer',
                            'abonnement' => 'Abonnement',
                            'pack-heures-anticipees' => 'Pack heures anticipées',
                            'caution-de-domiciliation' => 'Caution de domiciliation',
                            'caution-badge' => 'Caution badge',
                            'badge' => 'Badge',
                            'cle-boite-aux-lettres' => 'Clé boîte aux lettres',
                            'soutien-comptabilite' => 'Soutien comptabilité',
                            'client-de-passage' => 'Client de passage',
                            'cv' => 'CV',
                            'coaching-minute' => 'Coaching minute',
                        ])
                        ->hideFromIndex(),

                    Text::make('N° de téléphone Calliopée', 'calliopee_phone_number')
                        ->nullable(),
                ]),
                Tab::make('Contacts', [
                    HasMany::make('Contacts', 'contacts', Contact::class),
                ]),
                Tab::make('Cautions', [
                    HasMany::make('Cautions', 'deposits', Deposit::class),
                ]),
                Tab::make('Documents', [
                    HasMany::make('Documents', 'documents', Document::class),
                ]),
                Tab::make('Tâches', [
                    HasMany::make('Tâches', 'tasks', Task::class),
                ]),
                Tab::make('Shifts', [
                    HasMany::make('Shifts', 'shifts', Shift::class),
                ]),
                Tab::make('Journalisation', [
                    Heading::make('<div class="bg-gray-50 font-bold" style="margin:-1rem -1.5rem -1rem -1.5rem;padding:1rem 1.5rem 1rem 1.5rem">Notification de courrier</div>')->asHtml()->onlyOnDetail(),
                    HasMany::make('Notification courrier', 'mailNotifications', MailNotification::class),
                    Heading::make('<div class="bg-gray-50 font-bold" style="margin:-1rem -1.5rem -1rem -1.5rem;padding:1rem 1.5rem 1rem 1.5rem">Scans</div>')->asHtml()->onlyOnDetail(),
                    HasMany::make('Scans', 'scans', ScanLog::class),
                    Heading::make('<div class="bg-gray-50 font-bold" style="margin:-1rem -1.5rem -1rem -1.5rem;padding:1rem 1.5rem 1rem 1.5rem">Variations de solde</div>')->asHtml()->onlyOnDetail(),
                    HasMany::make('Variations de solde', 'balanceChanges', ClientBalanceChange::class),
                ]),
            ])->withToolbar(),
        ];
    }

    public function fieldsForIndex(NovaRequest $request)
    {
        return [
            Stack::make('Client', [
                Line::make('Nom complet', 'full_name')->asHeading(),
                Line::make('Entreprise', 'company')->asSmall(),
            ])->onlyOnIndex(),

            Boolean::make('Abonné ?', 'has_subscription'),
            Boolean::make('Domicilié ?', 'is_resident'),
            Boolean::make('235 ?', 'is_235'),
            Boolean::make('Accès porte', 'door_access_enabled'),
            Number::make('Crédit (tables)', 'purchased_minutes_table')->textAlign('center'),
            Number::make('Crédit (bureaux)', 'purchased_minutes_office')->textAlign('center'),
            Number::make('235 (tables)', 'included_minutes_table')->textAlign('center'),
            Number::make('235 (bureaux)', 'included_minutes_office')->textAlign('center'),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function lenses(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return [
            (new SendYouHaveNewMailNotification())->showInline(),
        ];
    }
}
