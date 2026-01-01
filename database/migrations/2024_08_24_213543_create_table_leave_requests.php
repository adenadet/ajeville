<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hrms_leave_requests', function (Blueprint $table) {
            $table->id();
            $table->integer('employee_id'); 
            $table->integer('leave_type_id'); 
            $table->date('from_date'); 
            $table->date('to_date'); 
            $table->text('reason')->nullable(); 
            $table->text('remarks')->nullable(); 
            $table->integer('status'); 
            $table->boolean('is_half_day'); 
            $table->text('leave_attachment');
            $table->integer('created_by');
            $table->integer('updated_by'); 
            $table->integer('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hrms_leave_requests');
    }
};
