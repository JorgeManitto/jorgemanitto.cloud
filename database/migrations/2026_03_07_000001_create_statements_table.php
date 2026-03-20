<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('statements', function (Blueprint $table) {
            $table->id();
            $table->string('holder_name');
            $table->string('cvu')->nullable();
            $table->string('cuit')->nullable();
            $table->string('period');
            $table->decimal('saldo_inicial', 15, 2)->default(0);
            $table->decimal('entradas', 15, 2)->default(0);
            $table->decimal('salidas', 15, 2)->default(0);
            $table->decimal('saldo_final', 15, 2)->default(0);
            $table->string('pdf_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('statements');
    }
};
