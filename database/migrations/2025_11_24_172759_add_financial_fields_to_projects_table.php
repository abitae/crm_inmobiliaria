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
        Schema::table('projects', function (Blueprint $table) {
            $table->enum('estado_legal', ['Derecho Posesorio', 'Compra y Venta', 'Juez de Paz', 'Titulo de propiedad'])->nullable()->after('path_documents');
            $table->enum('tipo_proyecto', ['propio', 'tercero'])->nullable()->after('estado_legal');
            $table->enum('tipo_financiamiento', ['contado', 'financiado'])->nullable()->after('tipo_proyecto');
            $table->string('banco')->nullable()->after('tipo_financiamiento');
            $table->enum('tipo_cuenta', ['cuenta corriente', 'cuenta vista', 'cuenta ahorro'])->nullable()->after('banco');
            $table->string('cuenta_bancaria')->nullable()->after('tipo_cuenta');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'estado_legal',
                'tipo_proyecto',
                'tipo_financiamiento',
                'banco',
                'tipo_cuenta',
                'cuenta_bancaria'
            ]);
        });
    }
};
