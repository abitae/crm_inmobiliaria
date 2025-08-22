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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('activity_type', ['llamada', 'reunion', 'visita', 'seguimiento', 'tarea']);
            $table->enum('status', ['programada', 'en_progreso', 'completada', 'cancelada'])->default('programada');
            $table->enum('priority', ['baja', 'media', 'alta', 'urgente'])->default('media');
            $table->timestamp('start_date');
            $table->timestamp('end_date')->nullable();
            $table->integer('duration')->nullable(); // duración en minutos
            $table->string('location')->nullable();
            $table->foreignId('client_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('project_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('unit_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('opportunity_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('advisor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null'); // ID del usuario asignado
            $table->integer('reminder_before')->nullable(); // minutos antes para recordatorio
            $table->boolean('reminder_sent')->default(false);
            $table->text('notes')->nullable();
            $table->text('result')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'activity_type']);
            $table->index(['priority', 'status']);
            $table->index(['start_date', 'status']);
            $table->index(['assigned_to', 'status']);
            $table->index(['advisor_id', 'status']);
            $table->index(['client_id', 'status']);
            $table->index(['project_id', 'status']);
            $table->index(['reminder_before', 'reminder_sent']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
