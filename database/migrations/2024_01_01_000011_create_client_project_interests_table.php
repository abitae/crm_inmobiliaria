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
        Schema::create('client_project_interests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->enum('interest_level', ['bajo', 'medio', 'alto', 'muy_alto']);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['client_id', 'project_id']);
            $table->index(['client_id', 'interest_level']);
            $table->index(['project_id', 'interest_level']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_project_interests');
    }
};
