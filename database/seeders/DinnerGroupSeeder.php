<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\DinnerGroup;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class DinnerGroupSeeder extends Seeder
{
    /**
     * ============================================
     * CONFIGURAZIONE CENTRALE GRUPPI E UTENTI
     * ============================================
     */
    private const TARGET_GROUPS = 1;        // Numero di gruppi da creare

    private const MEMBERS_PER_GROUP = 5;    // Membri massimi per gruppo

    /**
     * Dati delle città con relativi CAP e indirizzi tipici.
     */
    private array $cities = [
        'Roma' => [
            'postal_codes' => ['00100', '00118', '00153', '00186'],
            'streets'      => ['Via del Corso', 'Via Nazionale', 'Viale Trastevere', 'Via Veneto', 'Via Cola di Rienzo'],
        ],
        'Milano' => [
            'postal_codes' => ['20121', '20122', '20123', '20124'],
            'streets'      => ['Corso Buenos Aires', 'Via Dante', 'Corso Vittorio Emanuele', 'Via Torino', 'Corso Magenta'],
        ],
        'Napoli' => [
            'postal_codes' => ['80121', '80122', '80133', '80134'],
            'streets'      => ['Via Toledo', 'Corso Umberto I', 'Via Chiaia', 'Via dei Tribunali', 'Spaccanapoli'],
        ],
        'Firenze' => [
            'postal_codes' => ['50121', '50122', '50123', '50125'],
            'streets'      => ['Via de\' Tornabuoni', 'Via Roma', 'Borgo San Lorenzo', 'Via Cavour', 'Via de\' Calzaiuoli'],
        ],
    ];

    /**
     * Nomi italiani per i gruppi cena.
     */
    private array $groupNames = [
        'Amici del Gusto',
        'Cene in Compagnia',
        'Tavola Rotonda',
        'Gourmet Friends',
        'Sapori di Quartiere',
        'Dinner Club',
        'Convivio',
        'Mangiari Insieme',
        'La Tavolata',
        'Buona Forchetta',
        'Cenacolo',
        'Degustatori Felici',
    ];

    /**
     * Slogan per i gruppi cena.
     */
    private array $slogans = [
        'Dove il cibo unisce le persone',
        'Buon cibo, buona compagnia',
        'Condividiamo sapori e amicizia',
        'La gioia di cenare insieme',
        'Ogni cena è una festa',
        'Il piacere della tavola condivisa',
        'Cucinare e condividere',
        'Insieme si mangia meglio',
        'La felicità è un piatto da condividere',
        'Sapori autentici, amicizie vere',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌱 Inizio seeding utenti e gruppi cena...');

        // Array per tenere traccia degli utenti creati per città e CAP
        $usersByLocation = [];

        // Calcolo automatico basato su TARGET_GROUPS e MEMBERS_PER_GROUP
        // -1 perché l'admin verrà aggiunto al primo gruppo
        $totalUsers   = (self::TARGET_GROUPS * self::MEMBERS_PER_GROUP) - 1;
        $numCities    = count($this->cities);
        $usersPerCity = (int) ceil($totalUsers / $numCities);
        $userIndex    = 1;

        $this->command->info("📊 Configurazione: {$totalUsers} utenti per " . self::TARGET_GROUPS . ' gruppi da ' . self::MEMBERS_PER_GROUP . ' membri');

        foreach ($this->cities as $cityName => $cityData) {
            $this->command->info("📍 Creazione utenti per {$cityName}...");

            // Usa solo il primo CAP per semplicità
            $postalCode = $cityData['postal_codes'][0];

            for ($i = 0; $i < $usersPerCity && $userIndex <= $totalUsers; $i++) {
                $firstName   = $this->getRandomItalianFirstName();
                $lastName    = $this->getRandomItalianLastName();
                $street      = $cityData['streets'][array_rand($cityData['streets'])];
                $houseNumber = rand(1, 150);

                // Crea l'utente
                $user = User::create([
                    'name'              => "{$firstName} {$lastName}",
                    'email'             => strtolower(Str::slug($firstName . '.' . $lastName . $userIndex)) . '@example.com',
                    'password'          => bcrypt('password'),
                    'email_verified_at' => now(),
                ]);

                // Completa il profilo
                $user->profile->update([
                    'city'                => $cityName,
                    'address'             => $street,
                    'house_number'        => (string) $houseNumber,
                    'postal_code'         => $postalCode,
                    'max_guests'          => rand(4, 8),
                    'privacy_accepted_at' => now(),
                ]);

                // Aggiungi l'utente all'array per città e CAP
                $key = "{$cityName}_{$postalCode}";
                if ( ! isset($usersByLocation[$key])) {
                    $usersByLocation[$key] = [];
                }
                $usersByLocation[$key][] = $user;

                $userIndex++;
            }
        }

        $actualUsersCreated = $userIndex - 1;
        $this->command->info("✅ Creati {$actualUsersCreated} utenti con profili completi");

        // Crea gruppi con configurazione centralizzata
        $this->command->info('🍽️  Creazione ' . self::TARGET_GROUPS . ' gruppi con ' . self::MEMBERS_PER_GROUP . ' membri ciascuno...');

        $allUsers      = collect($usersByLocation)->flatten(1)->shuffle();
        $groupsCreated = 0;

        // Trova l'utente admin
        $admin = User::where('email', 'admin@example.com')->first();

        for ($g = 0; $g < self::TARGET_GROUPS; $g++) {
            // Per il primo gruppo, includi l'admin
            if ($g === 0 && $admin) {
                // Prendi (MEMBERS_PER_GROUP - 1) utenti normali + admin
                $normalUsers  = $allUsers->splice(0, self::MEMBERS_PER_GROUP - 1);
                $groupMembers = $normalUsers->prepend($admin);
                $creator      = $admin;
            } else {
                if ($allUsers->count() < self::MEMBERS_PER_GROUP) {
                    $this->command->warn('⚠️  Utenti insufficienti per creare il gruppo ' . ($g + 1) . ". Gruppi creati: {$groupsCreated}");
                    break;
                }
                // Prendi MEMBERS_PER_GROUP utenti per il gruppo
                $groupMembers = $allUsers->splice(0, self::MEMBERS_PER_GROUP);
                $creator      = $groupMembers->first();
            }

            $city = $creator->profile->city;

            // Scegli un nome gruppo univoco
            $groupName     = $this->groupNames[$g % count($this->groupNames)];
            $fullGroupName = "{$groupName} - {$city}";

            // Crea il gruppo
            $group = DinnerGroup::create([
                'name'       => $fullGroupName,
                'slogan'     => $this->slogans[array_rand($this->slogans)],
                'group_code' => strtoupper(Str::random(14)),
                'created_by' => $creator->id,
            ]);

            // Assegna tutti i membri al gruppo
            foreach ($groupMembers as $member) {
                $member->update(['dinner_group_id' => $group->id]);
            }

            $groupsCreated++;
            $actualMembers = $groupMembers->count();
            $this->command->info("  ✓ Gruppo '{$group->name}' creato con {$actualMembers} membri");
        }

        $this->command->newLine();
        $this->command->info('🎉 Seeding completato!');
        $this->command->info("   📊 Utenti creati: {$actualUsersCreated}");
        $this->command->info("   👥 Gruppi creati: {$groupsCreated}");
        $this->command->info('   👤 Membri per gruppo: ' . self::MEMBERS_PER_GROUP);

        // Statistiche finali
        $usersInGroups      = User::whereNotNull('dinner_group_id')->count();
        $usersWithoutGroups = User::whereNull('dinner_group_id')->count();

        $this->command->newLine();
        $this->command->info("   ✅ Utenti in gruppi: {$usersInGroups}");
        $this->command->info("   ⭕ Utenti senza gruppo: {$usersWithoutGroups}");
    }

    /**
     * Ottiene un nome italiano casuale.
     */
    private function getRandomItalianFirstName(): string
    {
        $names = [
            'Marco', 'Luca', 'Giovanni', 'Andrea', 'Francesco',
            'Alessandro', 'Matteo', 'Lorenzo', 'Gabriele', 'Davide',
            'Sofia', 'Giulia', 'Chiara', 'Francesca', 'Martina',
            'Elena', 'Alessia', 'Sara', 'Laura', 'Anna',
            'Roberto', 'Paolo', 'Giuseppe', 'Antonio', 'Stefano',
            'Valentina', 'Silvia', 'Simone', 'Tommaso', 'Federico',
            'Beatrice', 'Elisa', 'Giorgia', 'Camilla', 'Alice',
        ];

        return $names[array_rand($names)];
    }

    /**
     * Ottiene un cognome italiano casuale.
     */
    private function getRandomItalianLastName(): string
    {
        $surnames = [
            'Rossi', 'Russo', 'Ferrari', 'Esposito', 'Bianchi',
            'Romano', 'Colombo', 'Ricci', 'Marino', 'Greco',
            'Bruno', 'Gallo', 'Conti', 'De Luca', 'Costa',
            'Giordano', 'Mancini', 'Rizzo', 'Lombardi', 'Moretti',
            'Barbieri', 'Fontana', 'Santoro', 'Mariani', 'Rinaldi',
            'Caruso', 'Ferrara', 'Galli', 'Martini', 'Leone',
            'Longo', 'Gentile', 'Martinelli', 'Vitale', 'Serra',
        ];

        return $surnames[array_rand($surnames)];
    }
}
