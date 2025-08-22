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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->foreignId('advisor_id')->constrained('users')->onDelete('cascade');
            $table->string('reservation_number')->unique();
            $table->enum('reservation_type', ['pre_reserva', 'reserva_firmada', 'reserva_confirmada']);
            $table->enum('status', ['activa', 'confirmada', 'cancelada', 'vencida', 'convertida_venta'])->default('activa');
            $table->timestamp('reservation_date');
            $table->timestamp('expiration_date')->nullable();
            $table->decimal('reservation_amount', 12, 2); // monto de la reserva
            $table->decimal('reservation_percentage', 5, 2); // porcentaje del precio total
            $table->string('payment_method')->nullable();
            $table->enum('payment_status', ['pendiente', 'pagado', 'parcial'])->default('pendiente');
            $table->string('payment_reference')->nullable();
            $table->text('notes')->nullable();
            $table->text('terms_conditions')->nullable();
            $table->boolean('client_signature')->default(false);
            $table->boolean('advisor_signature')->default(false);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'reservation_type']);
            $table->index(['client_id', 'status']);
            $table->index(['project_id', 'status']);
            $table->index(['unit_id', 'status']);
            $table->index(['advisor_id', 'status']);
            $table->index(['expiration_date', 'status']);
            $table->index(['reservation_date', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
