<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WaitingRequest;
use App\Models\Event;
use App\Models\User;
use Carbon\Carbon;

class WaitingRequestSeeder extends Seeder
{
    public function run()
    {
        $events = Event::whereHas('user', function($q) {
            $q->where('role', 'organizer');
        })->get();

        if ($events->isEmpty()) {
            $this->command->error('Tidak ada event. Buat event dulu.');
            return;
        }

        $customers = User::where('role', 'customer')->get();
        if ($customers->isEmpty()) {
            for ($i = 1; $i <= 5; $i++) {
                $customers->push(User::create([
                    'name' => "Customer WL $i",
                    'email' => "customer.wl$i@test.com",
                    'password' => bcrypt('password'),
                    'role' => 'customer',
                    'is_active' => true,
                ]));
            }
        }

        $statuses = ['pending', 'invited', 'expired'];

        foreach ($events as $event) {
            for ($i = 0; $i < 6; $i++) {
                $customer = $customers->random();
                $status = $statuses[array_rand($statuses)];
                $expiresAt = ($status == 'invited') ? Carbon::now()->addHours(24) : null;
                WaitingRequest::updateOrCreate(
                    ['user_id' => $customer->id, 'event_id' => $event->id],
                    [
                        'quantity' => rand(1, 3),
                        'notes' => 'Request tiket ' . rand(1, 3),
                        'status' => $status,
                        'expires_at' => $expiresAt,
                        'created_at' => Carbon::now()->subDays(rand(0, 5)),
                    ]
                );
            }
        }

        $this->command->info('Waiting requests dummy berhasil dibuat.');
    }
}