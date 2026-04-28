<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
         $users = User::take(5)->get();
        foreach ($users as $user) {

        $count = 0;
        $i = 0;

        while ($count < 90) {

        $date = Carbon::now()->subDays($i);

        // 土日スキップ
        if ($date->isWeekend()) {
            $i++;
            continue;
        }

        Attendance::create([
            'user_id' => $user->id,
            'work_date' => $date->toDateString(),
            'status' => 'finished',
            'clock_in' => $date->copy()->setTime(9, 0),
            'clock_out' => $date->copy()->setTime(18, 0),
        ]);

        $count++;
        $i++;
    }
    }
}
}
