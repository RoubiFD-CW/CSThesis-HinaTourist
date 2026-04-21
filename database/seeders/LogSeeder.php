<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\VisitorLog;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class LogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear old logs so previous runs (like 'Leisure or Vacation') don't pile up
        VisitorLog::truncate();

        $destinationWeights = [
            'Enchanted River' => 15,
            'Harip Oceanside Beach' => 6,
            'Lodestone Shores Resort' => 6,
            'Baculin Amazing Sand Bar' => 1,
            'Davince Hidden Paradise' => 1,
            'Rock Island Resort' => 1,
            'Amparitas Integrated Nature Farm' => 1,
            'Sibadan Fish Cage and Resort' => 1,
            'Hinatuan Adventure Park' => 1,
            'Mamaon Beach Resort' => 1,
            'Landong Bay' => 1,
            'Tarusan Cold Spring' => 1,
            'Llamas Beach Resort' => 1,
            'Puro Brigida’s Beach' => 1,
            'Bunsadan Falls' => 1,
        ];

        // Create weighted array for quick random selection
        $weightedDestinations = [];
        foreach ($destinationWeights as $dest => $weight) {
            for ($j = 0; $j < $weight; $j++) {
                $weightedDestinations[] = $dest;
            }
        }

        $startDate = Carbon::create(2026, 2, 27);
        $endDate = Carbon::create(2026, 4, 2);


        $origins = ['Within the province', 'Other province', 'Foreign country residence'];
        $reasons = ['Vacation or Leisure', 'Business', 'Others'];
        $otherReasonsList = ['Research', 'Educational Tour', 'Family Reunion', 'Event', 'Photography'];

        // Get attendants once to speed up seeding
        $attendants = User::whereNotNull('dedicated_area')->get()->keyBy('dedicated_area');

        $this->command->getOutput()->progressStart(500);

        for ($i = 0; $i < 500; $i++) {
            $destination = $weightedDestinations[array_rand($weightedDestinations)];
            $attendant = $attendants->get($destination);

            // Randomly distributing roughly ~3000 people over 500 logs (average 6 per log)
            $groupSize = rand(1, 11);
            $maleCount = rand(0, $groupSize);
            $femaleCount = $groupSize - $maleCount;

            $visitReason = $reasons[array_rand($reasons)];
            $visitReasonOther = null;
            if ($visitReason === 'Others') {
                $visitReasonOther = $otherReasonsList[array_rand($otherReasonsList)];
            }

            $origin = $origins[array_rand($origins)];

            // Logically couple visitor_type with origin
            if ($origin === 'Foreign country residence') {
                $visitorType = 'Foreign Tourist';
            } else {
                $visitorType = 'Local'; // Applies to 'Within the province' and 'Other province'
            }

            VisitorLog::create([
                'visitor_type' => $visitorType,
                'group_size' => $groupSize,
                'male_count' => $maleCount,
                'female_count' => $femaleCount,
                'origin' => $origin,
                'visit_reason' => $visitReason,
                'visit_reason_other' => $visitReasonOther,
                'dedicated_area' => $destination,
                'attendant_id' => $attendant ? $attendant->id : null,
                'visit_date' => Carbon::createFromTimestamp(rand($startDate->timestamp, $endDate->timestamp)),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->command->getOutput()->progressAdvance();
        }

        $this->command->getOutput()->progressFinish();
        $this->command->info('✅ 500 visitor logs generating ~3,000 visitors successfully seeded!');
    }
}
