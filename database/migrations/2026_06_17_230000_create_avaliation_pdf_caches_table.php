<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('avaliation_pdf_caches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('avaliation_id')
                ->constrained('avaliations')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->string('snapshot_hash', 64);
            $table->string('storage_path', 255);
            $table->string('status', 20)->default('pending');
            $table->unsignedBigInteger('file_size')->nullable();
            $table->timestamp('generated_at')->nullable();
            $table->timestamps();

            $table->unique(['avaliation_id', 'snapshot_hash'], 'uk_avaliation_pdf_cache_hash');
            $table->index(['avaliation_id', 'status'], 'idx_avaliation_pdf_cache_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('avaliation_pdf_caches');
    }
};
