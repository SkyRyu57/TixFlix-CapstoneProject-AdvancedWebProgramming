<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\Transaction;
use App\Models\Eticket;
use App\Models\Category;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PaymentConfirmationSeeder extends Seeder
{
    public function run()
    {
        // 1. Buat customer jika belum ada
        $customer = User::where('role', 'customer')->first();
        if (!$customer) {
            $customer = User::create([
                'name' => 'Budi Santoso',
                'email' => 'budi@example.com',
                'password' => bcrypt('password'),
                'role' => 'customer',
                'is_active' => true,
            ]);
        }

        // 2. Buat organizer jika belum ada
        $organizer = User::where('role', 'organizer')->first();
        if (!$organizer) {
            $organizer = User::create([
                'name' => 'Organizer Event',
                'email' => 'organizer@event.com',
                'password' => bcrypt('password'),
                'role' => 'organizer',
                'is_active' => true,
            ]);
        }

        // 3. Buat kategori jika belum ada
        $category = Category::first();
        if (!$category) {
            $category = Category::create([
                'name' => 'Konser',
                'slug' => 'konser',
            ]);
        }

        // 4. Buat event jika belum ada
        $event = Event::where('status', 'published')->first();
        if (!$event) {
            $event = Event::create([
                'user_id' => $organizer->id,
                'category_id' => $category->id,
                'title' => 'Event Payment Test',
                'slug' => 'event-payment-test',
                'description' => 'Event untuk testing konfirmasi pembayaran',
                'location' => 'Jakarta',
                'start_date' => Carbon::now()->addDays(10),
                'end_date' => Carbon::now()->addDays(11),
                'status' => 'published',
            ]);
        }

        // 5. Buat tiket jika belum ada
        $ticket = Ticket::where('event_id', $event->id)->first();
        if (!$ticket) {
            $ticket = Ticket::create([
                'event_id' => $event->id,
                'name' => 'Regular',
                'price' => 150000,
                'stock' => 100,
            ]);
        }

        // 6. Buat transaksi pending dengan bukti pembayaran dummy
        for ($i = 1; $i <= 3; $i++) {
            $transaction = Transaction::create([
                'user_id' => $customer->id,
                'total_price' => $ticket->price * rand(1, 2),
                'status' => 'pending',
                'reference_number' => 'TRX-PAY-' . strtoupper(Str::random(8)),
                'payment_proof' => 'https://picsum.photos/id/' . (20 + $i) . '/400/300',
                'payment_notes' => 'Transfer via BCA, no referensi: ' . rand(100000, 999999),
                'created_at' => Carbon::now()->subDays(rand(1, 5)),
            ]);

            // Buat eticket dummy (belum diterbitkan)
            Eticket::create([
                'transaction_id' => $transaction->id,
                'ticket_id' => $ticket->id,
                'user_id' => $customer->id,
                'ticket_code' => 'TIX-' . strtoupper(Str::random(8)),
                'is_scanned' => false,
            ]);
        }

        $this->command->info('3 transaksi pending dengan bukti pembayaran dummy berhasil dibuat.');
    }
}