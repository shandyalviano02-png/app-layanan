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
        Schema::create('rehabilitation_cases', function (Blueprint $table) {
            $table->id();
            $table->string('case_number', 50)->unique();
            $table->foreignId('client_id')->constrained('clients');
            $table->foreignId('service_request_id')->nullable()->constrained('service_requests')->nullOnDelete();
            $table->foreignId('complaint_id')->nullable()->constrained('complaints')->nullOnDelete();
            $table->foreignId('officer_id')->constrained('users');
            $table->string('handling_type', 20)->default('direct');
            $table->string('status', 50)->index();
            $table->text('handling_result')->nullable();
            $table->timestampTz('received_at');
            $table->timestampTz('closed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['officer_id', 'received_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rehabilitation_cases');
    }
};
