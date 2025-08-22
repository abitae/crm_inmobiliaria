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
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('advisor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('unit_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('opportunity_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('commission_type', ['venta', 'reserva', 'seguimiento', 'bono']);
            $table->decimal('base_amount', 12, 2); // monto base para el cálculo
            $table->decimal('commission_percentage', 5, 2); // porcentaje de comisión
            $table->decimal('commission_amount', 12, 2); // monto de comisión calculado
            $table->decimal('bonus_amount', 12, 2)->default(0); // monto de bono adicional
            $table->decimal('total_commission', 12, 2); // comisión total (comisión + bono)
            $table->enum('status', ['pendiente', 'aprobada', 'pagada', 'cancelada'])->default('pendiente');
            $table->date('payment_date')->nullable(); // fecha de pago
            $table->string('payment_method')->nullable(); // método de pago
            $table->string('payment_reference')->nullable(); // referencia de pago
            $table->text('notes')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('paid_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['advisor_id', 'status']);
            $table->index(['project_id', 'status']);
            $table->index(['commission_type', 'status']);
            $table->index(['status', 'payment_date']);
            $table->index(['base_amount', 'total_commission']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commissions');
    }
};
