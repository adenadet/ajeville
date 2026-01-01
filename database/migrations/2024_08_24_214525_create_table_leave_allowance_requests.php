<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hrms_leave_allowance_requests', function (Blueprint $table) {
            $table->id();
            $table->integer('request_id');
            $table->double('amount')->nullable();
            $table->integer('processed_by')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->integer('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hrms_leave_allowance_requests');
    }
};
