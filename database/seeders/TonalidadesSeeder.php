<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TonalidadesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tonalidades = [
            'C', 'C#', 'Db', 'D', 'D#', 'Eb', 'E', 'F', 'F#', 'Gb',
            'G', 'G#', 'Ab', 'A', 'A#', 'Bb', 'B'
        ];

        foreach ($tonalidades as $tonalidade) {
            DB::table('tonalidades')->insert([
                'nome' => $tonalidade,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
