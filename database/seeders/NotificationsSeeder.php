<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeder per generare notifiche di test per l'utente admin.
 */
class NotificationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@example.com')->first();

        if ( ! $admin) {
            $this->command->warn('Admin user not found. Skipping notifications seeding.');

            return;
        }

        $this->command->info('Creating 20 random notifications for admin...');

        $notificationTypes = [
            [
                'title'     => 'Nuova Cena Disponibile!',
                'body'      => 'Pizza Napoletana del sabato 15 gennaio 2026 - 8 posti',
                'icon'      => 'tabler-chef-hat',
                'iconColor' => 'success',
                'color'     => 'success',
            ],
            [
                'title'     => 'Cena Cancellata',
                'body'      => 'La cena del domenica 22 gennaio 2026 è stata cancellata dall\'host.',
                'icon'      => 'tabler-chef-hat-off',
                'iconColor' => 'danger',
                'color'     => 'danger',
            ],
            [
                'title'     => 'Prenotazione Confermata',
                'body'      => 'La tua prenotazione per la cena del 10 gennaio è stata confermata!',
                'icon'      => 'tabler-check',
                'iconColor' => 'success',
                'color'     => 'success',
            ],
            [
                'title'     => 'Nuova Prenotazione Ricevuta',
                'body'      => 'Mario Rossi ha prenotato per la tua cena del 18 gennaio - 3 ospiti',
                'icon'      => 'tabler-user-plus',
                'iconColor' => 'info',
                'color'     => 'info',
            ],
            [
                'title'     => 'Reminder: Cena Domani!',
                'body'      => 'Non dimenticare la cena da Luca domani sera alle 20:00',
                'icon'      => 'tabler-clock-hour-8',
                'iconColor' => 'warning',
                'color'     => 'warning',
            ],
        ];

        for ($i = 0; $i < 20; $i++) {
            $type        = fake()->randomElement($notificationTypes);
            $isRead      = fake()->boolean(30); // 30% lette
            $createdDays = fake()->numberBetween(0, 30);

            DB::table('notifications')->insert([
                'id'   => (string) Str::uuid(),
                'type' => fake()->randomElement([
                    'App\\Notifications\\DinnerCreatedNotification',
                    'App\\Notifications\\DinnerCancelledByHostNotification',
                ]),
                'notifiable_type' => User::class,
                'notifiable_id'   => $admin->id,
                'data'            => json_encode([
                    'actions'   => [],
                    'body'      => $type['body'],
                    'color'     => $type['color'],
                    'duration'  => 'persistent',
                    'icon'      => $type['icon'],
                    'iconColor' => $type['iconColor'],
                    'status'    => $type['color'],
                    'title'     => $type['title'],
                ]),
                'read_at'    => $isRead ? now()->subDays(fake()->numberBetween(0, $createdDays)) : null,
                'created_at' => now()->subDays($createdDays),
                'updated_at' => now()->subDays($createdDays),
            ]);
        }

        $this->command->info('✅ Created 20 notifications for admin user.');
    }
}
