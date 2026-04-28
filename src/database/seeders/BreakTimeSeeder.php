<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;


class BreakTimeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
         $attendances = Attendance::all();

        foreach ($attendances as $attendance) {

            if (rand(0, 3) === 0) {
                continue;
            }

            $breakCount = rand(1, 2);

            for ($i = 0; $i < $breakCount; $i++) {

                $start =Carbon::parse($attendance->work_date->format('Y-m-d') . ' 12:00:00')
                    ->addMinutes(rand(0, 120));

                $end = (clone $start)->addMinutes(rand(5, 60));

                BreakTime::create([
                    'attendance_id' => $attendance->id,
                    'break_start' => $start,
                    'break_end' => $end,
                ]);
            }
        }
    }
}
