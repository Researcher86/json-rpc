<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $from = (new \DateTime())->modify('-6 month');
        $to = (new \DateTime())->modify('+1 day');

        $period = new \DatePeriod($from, new \DateInterval('P1D'), $to);

        $arrayOfDates = array_map(
            function ($item) {
                return $item->format('Y-m-d');
            },
            iterator_to_array($period)
        );

        foreach ($arrayOfDates as $date) {
            DB::table('history')->insert([
                'temp' => random_int(-30, 30),
                'date_at' => $date,
            ]);
        }
    }
}
