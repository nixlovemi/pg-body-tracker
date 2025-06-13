<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ChangeVisceralFatAvaliations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
            ALTER TABLE avaliations CHANGE visceral_fat_kg visceral_fat_level double(5,1) unsigned DEFAULT NULL;
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("
            ALTER TABLE avaliations CHANGE visceral_fat_level visceral_fat_kg double(5,1) unsigned DEFAULT NULL;
        ");
    }
}
