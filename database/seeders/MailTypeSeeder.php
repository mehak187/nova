<?php

namespace Database\Seeders;

use App\Models\MailType;
use Illuminate\Database\Seeder;

class MailTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = [
            'AFC',
            'Assurance',
            'Banque',
            'Confédération Suisse',
            'Enveloppe en Port Payé',
            'Etat de Vaud',
            'FER / CIEP',
            'Groupe mutuel',
            'Magazine',
            'Non spécifié',
            'OCPM',
            'Office des poursuites',
            'SIG',
            'Ville de Genève',
        ];

        foreach ($types as $type) {
            MailType::create(['title' => $type]);
        }
    }
}
