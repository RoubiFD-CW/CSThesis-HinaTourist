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
        Schema::table('visitor_logs', function (Blueprint $table) {
            // Drop old columns
            $table->dropColumn(['visitor_name', 'age', 'gender', 'address', 'nationality', 'contact_number']);

            // Add new columns
            $table->string('visitor_type')->after('id'); // Local, Tourist
            $table->integer('group_size')->after('visitor_type');
            $table->integer('male_count')->after('group_size');
            $table->integer('female_count')->after('male_count');
            $table->string('origin')->after('female_count'); // "Where are you from?"
            $table->string('visit_reason')->after('origin'); // Vacation, Business, Other
            $table->string('visit_reason_other')->nullable()->after('visit_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visitor_logs', function (Blueprint $table) {
            // Revert changes
            $table->dropColumn([
                'visitor_type',
                'group_size',
                'male_count',
                'female_count',
                'origin',
                'visit_reason',
                'visit_reason_other'
            ]);

            // Add back old columns (make nullable to avoid strict errors during rollback if data exists)
            $table->string('visitor_name')->nullable();
            $table->integer('age')->nullable();
            $table->string('gender')->nullable();
            $table->string('address')->nullable();
            $table->string('nationality')->default('Filipino');
            $table->string('contact_number')->nullable();
        });
    }
};
