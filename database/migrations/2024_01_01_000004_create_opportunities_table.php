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
        Schema::create('opportunities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('unit_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('advisor_id')->constrained('users')->onDelete('cascade');
            $table->enum('stage', ['calificado', 'visita', 'cierre']);
            $table->enum('status', ['registrado', 'reservado', 'cuotas', 'pagado', 'transferido', 'cancelado'])->default('registrado');
            $table->integer('probability'); // porcentaje de probabilidad de cierre
            $table->decimal('expected_value', 12, 2); // valor esperado de la venta
            $table->date('expected_close_date')->nullable(); // fecha esperada de cierre
            $table->date('actual_close_date')->nullable(); // fecha real de cierre
            $table->decimal('close_value', 12, 2)->nullable(); // valor real de la venta
            $table->string('close_reason')->nullable(); // razón del cierre
            $table->string('lost_reason')->nullable(); // razón de la pérdida
            $table->text('notes')->nullable();
            $table->string('source')->nullable(); // origen de la oportunidad
            $table->string('campaign')->nullable(); // campaña asociada
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'stage']);
            $table->index(['advisor_id', 'status']);
            $table->index(['client_id', 'status']);
            $table->index(['project_id', 'status']);
            $table->index(['expected_close_date', 'status']);
            $table->index(['probability', 'expected_value']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opportunities');
    }
};
