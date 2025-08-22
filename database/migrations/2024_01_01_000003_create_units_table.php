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
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->string('unit_number');
            $table->enum('unit_type', ['lote', 'casa', 'departamento', 'oficina', 'local']);
            $table->integer('floor')->nullable();
            $table->string('tower')->nullable();
            $table->string('block')->nullable();
            $table->decimal('area', 8, 2); // área en m²
            $table->integer('bedrooms')->default(0);
            $table->integer('bathrooms')->default(0);
            $table->integer('parking_spaces')->default(0);
            $table->integer('storage_rooms')->default(0);
            $table->decimal('balcony_area', 8, 2)->default(0);
            $table->decimal('terrace_area', 8, 2)->default(0);
            $table->decimal('garden_area', 8, 2)->default(0);
            $table->enum('status', ['disponible', 'reservado', 'vendido', 'bloqueado', 'en_construccion'])->default('disponible');
            $table->decimal('base_price', 12, 2); // precio base por m²
            $table->decimal('total_price', 12, 2); // precio total
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('final_price', 12, 2); // precio final después de descuentos
            $table->decimal('commission_percentage', 5, 2)->default(0);
            $table->decimal('commission_amount', 12, 2)->default(0);
            $table->timestamp('blocked_until')->nullable(); // fecha hasta cuando está bloqueado
            $table->foreignId('blocked_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('blocked_reason')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['project_id', 'unit_number']);
            $table->index(['status', 'unit_type']);
            $table->index(['project_id', 'status']);
            $table->index(['final_price', 'area']);
            $table->index(['bedrooms', 'bathrooms']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
