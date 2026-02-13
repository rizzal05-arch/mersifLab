<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdatePriceTierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * This will set `price_tier` to 'standard' when price > 0 and <= 50000,
     * 'premium' when price > 50000, or NULL otherwise.
     */
    public function run()
    {
        DB::statement(<<<'SQL'
UPDATE `classes`
SET `price_tier` = CASE
  WHEN IFNULL(price, 0) > 0 AND IFNULL(price, 0) <= 50000 THEN 'standard'
  WHEN IFNULL(price, 0) > 50000 THEN 'premium'
  ELSE NULL
END;
SQL
        );

        $this->command->info('Updated classes.price_tier based on price');
    }
}
