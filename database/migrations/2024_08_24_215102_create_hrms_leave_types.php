<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hrms_leave_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->length(191);
            $table->string('leave_category')->length(191);
            $table->integer('no_of_days'); 
            $table->integer('status'); 
            $table->date('start_date'); 
            $table->date('end_date'); 
            $table->integer('created_by'); 
            $table->integer('updated_by'); 
            $table->integer('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hrms_leave_types');
    }
};
