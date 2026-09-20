<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sale_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->restrictOnDelete();
            $table->string('return_no')->unique();
            $table->foreignId('branch_id')->constrained()->restrictOnDelete();
            $table->dateTime('return_date');
            $table->decimal('total_amount', 15, 2);
            $table->text('reason')->nullable();
            $table->enum('status', ['completed', 'cancelled']);
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();

            $table->index('sale_id');
            $table->index('branch_id');
            $table->index('return_date');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_returns');
    }
};
