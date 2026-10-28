<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\Election;
use Illuminate\Database\Seeder;

class CandidateSeeder extends Seeder
{
    public function run(): void
    {
        $election = Election::current();

        if (! $election) {
            return;
        }

        $candidates = [
            [
                'order_no' => 1,
                'name' => 'Aditya Pratama',
                'vision' => 'Mewujudkan kelas XI RPL 1 yang kompak, disiplin, dan berprestasi di bidang teknologi.',
                'mission' => "1. Membuat jadwal piket yang adil dan transparan.\n2. Menyediakan wadah belajar bersama sebelum ujian.\n3. Meningkatkan komunikasi antara siswa dan wali kelas.",
            ],
            [
                'order_no' => 2,
                'name' => 'Nadia Putri Ramadhani',
                'vision' => 'Menciptakan kelas yang nyaman, inklusif, dan aktif dalam kegiatan sekolah maupun lomba.',
                'mission' => "1. Mengadakan program mentoring antar siswa.\n2. Membuat grup informasi kelas yang tertib.\n3. Mengusulkan kegiatan class meeting yang seru.",
            ],
            [
                'order_no' => 3,
                'name' => 'Rizky Maulana Akbar',
                'vision' => 'Menjadikan kelas XI RPL 1 sebagai contoh kelas yang solid dan berdaya saing.',
                'mission' => "1. Meningkatkan kebersihan dan kerapian kelas.\n2. Mengoordinasikan pembagian tugas proyek kelompok.\n3. Menjadi penghubung aspirasi siswa kepada guru.",
            ],
        ];

        foreach ($candidates as $candidate) {
            Candidate::updateOrCreate(
                [
                    'election_id' => $election->id,
                    'order_no' => $candidate['order_no'],
                ],
                $candidate,
            );
        }

        $this->command?->info('Kandidat: '.count($candidates).' data disiapkan.');
    }
}
