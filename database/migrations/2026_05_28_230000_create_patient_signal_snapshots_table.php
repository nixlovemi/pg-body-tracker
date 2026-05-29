<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientSignalSnapshotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patient_signal_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')
                ->constrained('clients')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->date('snapshot_date');
            $table->string('status', 50);
            $table->decimal('risk_percent', 5, 1)->default(0.0);
            $table->unsignedTinyInteger('signal_count')->default(0);
            $table->json('summary_json')->nullable();
            $table->json('reasons_json')->nullable();
            $table->timestamps();

            $table->unique(['client_id', 'snapshot_date'], 'uk_patient_signal_snapshots_client_date');
            $table->index(['snapshot_date', 'status'], 'idx_patient_signal_snapshots_date_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('patient_signal_snapshots', function (Blueprint $table) {
            $table->dropUnique('uk_patient_signal_snapshots_client_date');
            $table->dropIndex('idx_patient_signal_snapshots_date_status');
        });

        Schema::dropIfExists('patient_signal_snapshots');
    }
}
