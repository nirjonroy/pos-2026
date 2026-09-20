<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_transfers', function (Blueprint $table) {
            $table->id();
            $table->string('transfer_no')->unique();
            $table->foreignId('from_branch_id')->constrained('branches')->restrictOnDelete();
            $table->foreignId('to_branch_id')->constrained('branches')->restrictOnDelete();
            $table->foreignId('from_warehouse_id')->nullable()->constrained('warehouses')->restrictOnDelete();
            $table->foreignId('to_warehouse_id')->nullable()->constrained('warehouses')->restrictOnDelete();
            $table->enum('status', ['draft', 'pending', 'completed', 'cancelled']);
            $table->date('transfer_date');
            $table->text('note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['from_branch_id', 'to_branch_id']);
            $table->index('status');
            $table->index('transfer_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_transfers');
    }
};
