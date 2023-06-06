<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $now = Carbon::now();

        DB::table('currency')->insert([
            [
                'name' => 'USD',
                'prefix' => '$',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'EUR',
                'prefix' => '€',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'GBP',
                'prefix' => '£',
                'created_at' => $now,
                'updated_at' => $now
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('currency')->whereIn('name', ['USD', 'EUR', 'GBP'])->delete();
    }
};
