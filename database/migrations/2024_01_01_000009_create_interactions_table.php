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
        Schema::create('interactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('opportunity_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('interaction_type', ['llamada', 'email', 'mensaje', 'visita', 'reunion', 'otros']);
            $table->enum('channel', ['telefonico', 'email', 'whatsapp', 'presencial', 'redes_sociales']);
            $table->enum('direction', ['entrada', 'salida', 'bidireccional']);
            $table->string('subject')->nullable();
            $table->text('content')->nullable();
            $table->integer('duration')->nullable(); // duración en minutos (para llamadas)
            $table->enum('status', ['programada', 'en_progreso', 'completada', 'cancelada'])->default('programada');
            $table->enum('priority', ['baja', 'media', 'alta', 'urgente'])->default('media');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('result')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['client_id', 'interaction_type']);
            $table->index(['opportunity_id', 'status']);
            $table->index(['status', 'priority']);
            $table->index(['scheduled_at', 'status']);
            $table->index(['channel', 'interaction_type']);
            $table->index(['created_by', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interactions');
    }
};
