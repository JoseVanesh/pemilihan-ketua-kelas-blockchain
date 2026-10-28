<?php

namespace Database\Seeders;

use App\Models\Election;
use App\Services\BlockchainService;
use Illuminate\Database\Seeder;

class ElectionSeeder extends Seeder
{
    public function run(): void
    {
        $election = Election::updateOrCreate(
            ['title' => 'Pemilihan Ketua Kelas XI RPL 1'],
            [
                'class_name' => 'XI RPL 1',
                'status' => 'open',
                'starts_at' => now()->subHour(),
                'ends_at' => now()->addDays(7),
            ],
        );

        app(BlockchainService::class)->createGenesis($election);

        $this->command?->info('Pemilihan: '.$election->title.' (status: '.$election->status.')');
    }
}
