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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->enum('document_type', ['DNI', 'RUC', 'CE', 'PASAPORTE'])->nullable();
            $table->string('document_number')->nullable();
            $table->text('address')->nullable();
            $table->string('district')->nullable();
            $table->string('province')->nullable();
            $table->string('region')->nullable();
            $table->string('country')->nullable();
            $table->enum('client_type', ['inversor', 'comprador', 'empresa', 'constructor']);
            $table->enum('source', ['redes_sociales', 'ferias', 'referidos', 'formulario_web', 'publicidad']);
            $table->enum('status', ['nuevo', 'contacto_inicial', 'en_seguimiento', 'cierre', 'perdido'])->default('nuevo');
            $table->integer('score')->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('assigned_advisor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['email', 'document_number']);
            $table->index(['status', 'client_type']);
            $table->index(['assigned_advisor_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
