<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\WaitingList;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CompleteWaitingListSeeder extends Seeder
{
    public function run()
    {
        // 1. Organizer
        $organizer = User::firstOrCreate(
            ['email' => 'organizer@test.com'],
            [
                'name' => 'Organizer Test',
                'password' => bcrypt('password'),
                'role' => 'organizer',
                'is_active' => true,
            ]
        );

        // 2. Kategori
        $category = Category::firstOrCreate(
            ['slug' => 'test-category'],
            ['name' => 'Test Category']
        );

        // 3. Buat slug unik dengan timestamp dan random
        $uniqueSlug = 'event-waiting-list-' . time() . '-' . Str::random(6);
        $event = Event::create([
            'user_id' => $organizer->id,
            'category_id' => $category->id,
            'title' => 'Event untuk Waiting List',
            'slug' => $uniqueSlug,
            'description' => 'Deskripsi event',
            'location' => 'Jakarta',
            'start_date' => Carbon::now()->addDays(10),
            'end_date' => Carbon::now()->addDays(11),
            'status' => 'published',
        ]);

        // 4. Tiket (stok 0)
        $ticket = Ticket::create([
            'event_id' => $event->id,
            'name' => 'Regular',
            'price' => 100000,
            'stock' => 0,
        ]);

        // 5. Customers
        for ($i = 1; $i <= 5; $i++) {
            $customer = User::firstOrCreate(
                ['email' => "customer$i@test.com"],
                [
                    'name' => "Customer $i",
                    'password' => bcrypt('password'),
                    'role' => 'customer',
                    'is_active' => true,
                ]
            );
            // 6. Waiting list
            $statuses = ['waiting', 'invited', 'expired'];
            $status = $statuses[array_rand($statuses)];
            $expiresAt = ($status == 'invited') ? Carbon::now()->addHours(24) : null;
            WaitingList::updateOrCreate(
                ['user_id' => $customer->id, 'ticket_id' => $ticket->id],
                [
                    'event_id' => $event->id,
                    'quantity' => rand(1, 2),
                    'status' => $status,
                    'expires_at' => $expiresAt,
                    'created_at' => Carbon::now()->subDays(rand(0, 10)),
                ]
            );
        }

        $this->command->info('Event, tiket, dan waiting list dummy berhasil dibuat.');
    }
}