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
            $table->string('primary_language')->nullable();
            $table->string('framework')->nullable();
            $table->string('status')->default('planning');
            $table->date('start_date')->nullable();
            $table->date('estimated_end_date')->nullable();
            $table->string('git_repository')->nullable();
            $table->string('documentation_url')->nullable();
            $table->timestamps();
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
