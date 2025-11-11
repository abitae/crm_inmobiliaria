<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Modificar project_type para que solo acepte 'lotes'
            // Primero actualizamos los valores existentes que no sean 'lotes'
            DB::statement("UPDATE projects SET project_type = 'lotes' WHERE project_type != 'lotes'");
            
            // Modificar el enum para que solo acepte 'lotes'
            DB::statement("ALTER TABLE projects MODIFY COLUMN project_type ENUM('lotes') NOT NULL");
            
            // Agregar campo boolean para publicar en web
            $table->boolean('is_published')->default(false)->after('project_type');
            
            // Agregar campo enum para tipo de lote
            $table->enum('lote_type', ['normal', 'express'])->default('normal')->after('is_published');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Revertir project_type al enum original
            DB::statement("ALTER TABLE projects MODIFY COLUMN project_type ENUM('lotes', 'casas', 'departamentos', 'oficinas', 'mixto') NOT NULL");
            
            // Eliminar los campos agregados
            $table->dropColumn(['is_published', 'lote_type']);
        });
    }
};
