<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Actualiza el enum de status en la tabla units para incluir 'transferido'
     * y eliminar 'bloqueado' y 'en_construccion'
     */
    public function up(): void
    {
        // Primero, actualizar los registros existentes con estados que serán eliminados
        // Convertir 'bloqueado' a 'transferido'
        DB::table('units')
            ->where('status', 'bloqueado')
            ->update(['status' => 'transferido']);
        
        // Convertir 'en_construccion' a 'transferido'
        DB::table('units')
            ->where('status', 'en_construccion')
            ->update(['status' => 'transferido']);
        
        // Modificar el enum usando ALTER TABLE
        // Nota: Esto funciona en MySQL/MariaDB
        DB::statement("ALTER TABLE units MODIFY COLUMN status ENUM('disponible', 'reservado', 'vendido', 'transferido', 'cuotas') DEFAULT 'disponible'");
    }

    /**
     * Reverse the migrations.
     * 
     * Revierte el enum a su estado anterior
     */
    public function down(): void
    {
        // Convertir 'transferido' de vuelta a 'disponible' antes de revertir el enum
        DB::table('units')
            ->where('status', 'transferido')
            ->update(['status' => 'disponible']);
        
        // Revertir el enum a su estado anterior
        // Primero convertir 'cuotas' y 'transferido' a 'disponible'
        DB::table('units')
            ->whereIn('status', ['cuotas', 'transferido'])
            ->update(['status' => 'disponible']);
        
        DB::statement("ALTER TABLE units MODIFY COLUMN status ENUM('disponible', 'reservado', 'vendido', 'bloqueado', 'en_construccion') DEFAULT 'disponible'");
    }
};
