<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_infos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->string('logo_url', 255)->nullable();
            $table->string('title', 60)->nullable(); // like medic, personal trainer, etc
            $table->string('license_text', 60)->nullable(); // CRM 123432
            $table->string('whatsapp_phone', 35)->nullable();
            $table->string('link_telegram', 100)->nullable();
            $table->string('link_facebook', 100)->nullable();
            $table->string('link_instagram', 100)->nullable();
            $table->string('link_twitter', 100)->nullable();
            $table->string('link_youtube', 100)->nullable();
            $table->string('link_website', 100)->nullable();

            $table->unique(
                ['user_id'],
                'uk_user_infos_user_id'
            );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_infos', function (Blueprint $table) {
            $table->dropUnique('uk_user_infos_user_id');
        });
        Schema::dropIfExists('user_infos');
    }
}
