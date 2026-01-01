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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id')->unique();
            $table->integer('customer_id');
            $table->date('date');
            $table->date('due_date');
            $table->string('reference')->nullable();
            $table->text('tandc')->nullable();
            $table->double('sub_total');
            $table->double('discount')->default(0);
            $table->double('logistics')->default(0);
            $table->double('taxes')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
