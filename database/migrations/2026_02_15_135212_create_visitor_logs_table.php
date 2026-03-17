<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('visitor_logs', function (Blueprint $table) {
            $table->id();
            $table->string('visitor_name');
            $table->integer('age')->nullable();
            $table->string('gender')->nullable(); // Male, Female, Other
            $table->string('address')->nullable();
            $table->string('nationality')->default('Filipino');
            $table->string('contact_number')->nullable();
            $table->string('dedicated_area')->nullable(); // The area this log belongs to
            $table->foreignId('attendant_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('visit_date')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitor_logs');
    }
};
