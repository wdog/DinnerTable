<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\DinnerGroup;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class DinnerGroupSeeder extends Seeder
{
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

        // Distribuisci 100 utenti tra le 4 città (25 per città)
        $usersPerCity = 25;
        $userIndex    = 1;

        foreach ($this->cities as $cityName => $cityData) {
            $this->command->info("📍 Creazione utenti per {$cityName}...");

            // Distribuisci gli utenti tra i CAP della città
            $postalCodes        = $cityData['postal_codes'];
            $usersPerPostalCode = (int) ceil($usersPerCity / count($postalCodes));

            foreach ($postalCodes as $postalCode) {
                $count = min($usersPerPostalCode, 100 - $userIndex + 1);

                for ($i = 0; $i < $count && $userIndex <= 100; $i++) {
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
                        'max_guests'          => rand(2, 8),
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
        }

        $this->command->info('✅ Creati 100 utenti con profili completi');

        // Crea gruppi cena per ogni combinazione città/CAP
        $this->command->info('🍽️  Creazione gruppi cena...');

        $groupsCreated  = 0;
        $usedGroupNames = [];

        foreach ($usersByLocation as $locationKey => $users) {
            [$city, $postalCode] = explode('_', $locationKey);

            // Crea 1-2 gruppi per ogni combinazione città/CAP se ci sono abbastanza utenti
            $numGroups = count($users) >= 6 ? rand(1, 2) : 1;

            for ($g = 0; $g < $numGroups; $g++) {
                if (empty($users)) {
                    break;
                }

                // Scegli un nome gruppo univoco
                do {
                    $groupName     = $this->groupNames[array_rand($this->groupNames)];
                    $fullGroupName = "{$groupName} - {$city}";
                } while (in_array($fullGroupName, $usedGroupNames));

                $usedGroupNames[] = $fullGroupName;

                // Numero di membri per questo gruppo (minimo 1, massimo metà degli utenti disponibili)
                $maxMembers = max(1, (int) floor(count($users) / max(1, $numGroups - $g)));
                $numMembers = rand(1, min(8, $maxMembers));

                // Seleziona il creatore (primo utente del gruppo)
                $creator = array_shift($users);

                // Crea il gruppo
                $group = DinnerGroup::create([
                    'name'       => $fullGroupName,
                    'slogan'     => $this->slogans[array_rand($this->slogans)],
                    'group_code' => strtoupper(Str::random(14)),
                    'created_by' => $creator->id,
                ]);

                // Assegna il creatore al gruppo
                $creator->update(['dinner_group_id' => $group->id]);

                $members = [$creator];

                // Aggiungi altri membri se necessario
                for ($m = 1; $m < $numMembers && ! empty($users); $m++) {
                    $member = array_shift($users);
                    $member->update(['dinner_group_id' => $group->id]);
                    $members[] = $member;
                }

                $groupsCreated++;
                $this->command->info("  ✓ Gruppo '{$group->name}' creato con {$numMembers} membri (CAP: {$postalCode})");
            }
        }

        $this->command->newLine();
        $this->command->info('🎉 Seeding completato!');
        $this->command->info('   📊 Utenti creati: 100');
        $this->command->info("   👥 Gruppi creati: {$groupsCreated}");
        $this->command->info('   🏙️  Città: ' . count($this->cities));

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
