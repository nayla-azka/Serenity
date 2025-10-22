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
        Schema::table('comment_reports', function (Blueprint $table) {
            // Add new fields for admin review tracking
            $table->text('admin_notes')->nullable()->after('reason');
            $table->unsignedBigInteger('reviewed_by')->nullable()->after('admin_notes');
            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
            
            // Add foreign key constraint
            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
            
            // Add index for better performance
            $table->index(['status', 'created_at']);
            $table->index('reviewed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comment_reports', function (Blueprint $table) {
            $table->dropForeign(['reviewed_by']);
            $table->dropIndex(['status', 'created_at']);
            $table->dropIndex(['reviewed_at']);
            $table->dropColumn(['admin_notes', 'reviewed_by', 'reviewed_at']);
        });
    }
};
