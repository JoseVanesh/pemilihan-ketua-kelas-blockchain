<?php

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $names = [
            'Ahmad Fauzan',
            'Bella Safitri',
            'Citra Ayu Lestari',
            'Dimas Aryo Nugroho',
            'Eka Putri Wulandari',
            'Farhan Maulana',
            'Gita Nirmala Sari',
            'Hendra Setiawan',
            'Intan Permata Sari',
            'Joko Susilo',
            'Kartika Dewi',
            'Lukman Hakim',
            'Maya Anggraini',
            'Naufal Rizky Pratama',
            'Oktavia Rahmawati',
            'Putra Bagus Saputra',
            'Qori Amalia',
            'Rangga Aditya',
            'Siti Nurhaliza',
            'Tegar Firmansyah',
        ];

        $rows = [];

        foreach ($names as $index => $name) {
            $nis = '2425'.str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT);

            $student = Student::firstOrCreate(
                ['nis' => $nis],
                [
                    'name' => $name,
                    'class_name' => 'XI RPL 1',
                    'voting_code' => $this->uniqueCode(),
                ],
            );

            $rows[] = [$student->nis, $student->name, $student->voting_code];
        }

        $this->command?->info('Siswa: '.count($rows).' data disiapkan. Kode voting sekali-pakai:');
        $this->command?->table(['NIS', 'Nama', 'Kode'], $rows);
    }

    private function uniqueCode(): string
    {
        do {
            $code = strtoupper(Str::random(6));
        } while (Student::query()->where('voting_code', $code)->exists());

        return $code;
    }
}
