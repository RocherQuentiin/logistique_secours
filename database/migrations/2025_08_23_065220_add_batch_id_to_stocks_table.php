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
        if (!Schema::hasColumn('stocks', 'batch_id')) {
            Schema::table('stocks', function (Blueprint $table) {
                // add batch_id as unsignedBigInteger nullable; add FK in a later migration if desired
                $table->unsignedBigInteger('batch_id')->nullable()->after('location_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('stocks', 'batch_id')) {
            Schema::table('stocks', function (Blueprint $table) {
                $table->dropColumn('batch_id');
            });
        }
    }
};
