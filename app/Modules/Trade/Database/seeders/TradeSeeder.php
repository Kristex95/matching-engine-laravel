<?php

namespace App\Modules\Trade\Database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting realistic trade historical generation...');

        // Clear existing data to ensure a clean chart canvas layout
        DB::table('trades')->truncate();

        $totalDaysToSeed = 31;
        $currentPrice = 50000.0;
        $insertData = [];

        for ($day = $totalDaysToSeed; $day >= 0; $day--) {
            for ($hour = 0; $hour < 24; $hour++) {
                
                if ($day <= 2) {
                    for ($minute = 0; $minute < 60; $minute += rand(1, 4)) {
                        
                        // Simulate a slight price fluctuation (-1.5% to +1.5%)
                        $percentChange = (rand(-150, 150) / 10000); 
                        $currentPrice = $currentPrice * (1 + $percentChange);
                        
                        // Prevent the price from dropping to or below 0
                        if ($currentPrice <= 100) {
                            $currentPrice = rand(500, 1000);
                        }

                        $timestamp = Carbon::now()
                            ->subDays($day)
                            ->setHour($hour)
                            ->setMinute($minute)
                            ->setSecond(rand(0, 59));

                        $insertData[] = [
                            'taker_account_id' => 1,
                            'maker_account_id' => 1,
                            'taker_order_id'   => Str::uuid()->toString(),
                            'maker_order_id'   => Str::uuid()->toString(),
                            'price'            => round($currentPrice, 8),
                            'amount'           => round(rand(1, 500) / 100, 8),
                            'created_at'       => $timestamp,
                            'updated_at'       => $timestamp,
                        ];

                        if (count($insertData) >= 1000) {
                            DB::table('trades')->insert($insertData);
                            $insertData = [];
                        }
                    }
                } else {
                    for ($tradeCount = 0; $tradeCount < rand(1, 3); $tradeCount++) {
                        $percentChange = (rand(-250, 250) / 10000); 
                        $currentPrice = $currentPrice * (1 + $percentChange);

                        $timestamp = Carbon::now()
                            ->subDays($day)
                            ->setHour($hour)
                            ->setMinute(rand(0, 59))
                            ->setSecond(rand(0, 59));

                        $insertData[] = [
                            'taker_account_id' => 1,
                            'maker_account_id' => 1,
                            'taker_order_id'   => Str::uuid()->toString(),
                            'maker_order_id'   => Str::uuid()->toString(),
                            'price'            => round($currentPrice, 8),
                            'amount'           => round(rand(5, 1000) / 100, 8),
                            'created_at'       => $timestamp,
                            'updated_at'       => $timestamp,
                        ];

                        if (count($insertData) >= 1000) {
                            DB::table('trades')->insert($insertData);
                            $insertData = [];
                        }
                    }
                }
            }
        }

        if (count($insertData) > 0) {
            DB::table('trades')->insert($insertData);
        }

        $this->command->info('Success! Generated trades seamlessly.');
    }
}