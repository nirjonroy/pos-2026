<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('address')->nullable();
            $table->boolean('status')->default(true)->index();
            $table->timestamps();

            $table->index('branch_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouses');
    }
};
