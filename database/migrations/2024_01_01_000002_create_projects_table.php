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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('project_type', ['lotes', 'casas', 'departamentos', 'oficinas', 'mixto']);
            $table->enum('stage', ['preventa', 'lanzamiento', 'venta_activa', 'cierre']);
            $table->enum('legal_status', ['con_titulo', 'en_tramite', 'habilitado']);
            $table->text('address')->nullable();
            $table->string('district')->nullable();
            $table->string('province')->nullable();
            $table->string('region')->nullable();
            $table->string('country')->nullable();
            $table->string('ubicacion')->nullable();
            $table->integer('total_units')->default(0);
            $table->integer('available_units')->default(0);
            $table->integer('reserved_units')->default(0);
            $table->integer('sold_units')->default(0);
            $table->integer('blocked_units')->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->date('delivery_date')->nullable();
            $table->enum('status', ['activo', 'inactivo', 'suspendido', 'finalizado'])->default('activo');
            $table->string('path_image_portada')->nullable();//imagen de portada
            $table->string('path_video_portada')->nullable();//video de portada
            $table->json('path_images')->nullable();//imagenes
            $table->json('path_videos')->nullable();//videos
            $table->json('path_documents')->nullable();//documentos
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'project_type']);
            $table->index(['stage', 'status']);
            $table->index(['district', 'province', 'region']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
