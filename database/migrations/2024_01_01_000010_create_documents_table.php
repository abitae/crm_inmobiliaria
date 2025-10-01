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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('document_type', ['contrato', 'factura', 'recibo', 'documento_legal', 'otros'])->default('otros');
            $table->enum('category', ['venta', 'alquiler', 'legal', 'marketing', 'otros'])->default('otros');
            $table->string('file_path');
            $table->string('file_name');
            $table->bigInteger('file_size')->nullable(); // tamaño en bytes
            $table->string('file_extension')->nullable();
            $table->string('mime_type')->nullable();
            $table->integer('version')->default(1);
            $table->boolean('is_current_version')->default(true);
            $table->enum('status', ['borrador', 'revisado', 'aprobado', 'firmado', 'archivado'])->default('borrador');
            
            // Relaciones opcionales
            $table->foreignId('client_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('unit_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('opportunity_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('activity_id')->nullable()->constrained()->onDelete('cascade');
            
            // Usuarios
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('signed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('signed_at')->nullable();
            
            // Fechas y metadatos
            $table->date('expiration_date')->nullable();
            $table->json('tags')->nullable();
            $table->text('notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index(['client_id', 'status']);
            $table->index(['project_id', 'status']);
            $table->index(['unit_id', 'status']);
            $table->index(['opportunity_id', 'status']);
            $table->index(['activity_id', 'status']);
            $table->index(['document_type', 'category']);
            $table->index(['status', 'created_at']);
            $table->index('expiration_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
