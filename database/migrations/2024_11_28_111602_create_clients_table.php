<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Client;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->string('first_name', 60);
            $table->string('last_name', 80);
            $table->string('email')->nullable();
            $table->string('phone', 35)->nullable();
            $table->enum('gender', array_keys(Client::fGetGenders()));
            $table->date('birthdate');
            $table->smallInteger('height', false, true); # in cm
            $table->timestamps();

            $table->unique(
                ['user_id', 'email'],
                'uk_clients_user_id_email'
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
        DB::statement("
            ALTER TABLE clients DROP FOREIGN KEY clients_user_id_foreign;
        ");
        Schema::table('clients', function (Blueprint $table) {
            $table->dropUnique('uk_clients_user_id_email');
        });
        Schema::dropIfExists('clients');
    }
}
