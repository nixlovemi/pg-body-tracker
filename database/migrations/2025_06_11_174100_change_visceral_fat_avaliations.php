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
            ALTER TABLE avaliations CHANGE visceral_fat_kg visceral_fat_level double(2,1) unsigned DEFAULT NULL NULL COMMENT 'Levels 1 - 30, Omron, Tanita';
            ALTER TABLE avaliations MODIFY COLUMN visceral_fat_level double(2,1) unsigned DEFAULT NULL NULL COMMENT 'Levels 1 - 30, Omron, Tanita';
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
            ALTER TABLE avaliations CHANGE visceral_fat_level visceral_fat_kg double(5,1) unsigned DEFAULT NULL NULL;
            ALTER TABLE avaliations MODIFY COLUMN visceral_fat_kg double(5,1) unsigned DEFAULT NULL NULL;
        ");
    }
}
