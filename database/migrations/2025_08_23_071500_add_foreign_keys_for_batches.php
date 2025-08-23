<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Skip heavy information_schema checks on SQLite (tests use in-memory SQLite)
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'sqlite') {
            return;
        }

        $database = env('DB_DATABASE');

        // stocks.batch_id -> batches.id
        if (Schema::hasTable('stocks') && Schema::hasColumn('stocks', 'batch_id')) {
            $fk = DB::select(
                "SELECT CONSTRAINT_NAME as name FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'stocks' AND COLUMN_NAME = 'batch_id' AND REFERENCED_TABLE_NAME = 'batches'",
                [$database]
            );

            if (empty($fk)) {
                Schema::table('stocks', function (Blueprint $table) {
                    $table->foreign('batch_id')->references('id')->on('batches')->nullOnDelete();
                });
            }
        }

        // stock_movements.batch_id -> batches.id
        if (Schema::hasTable('stock_movements') && Schema::hasColumn('stock_movements', 'batch_id')) {
            $fk = DB::select(
                "SELECT CONSTRAINT_NAME as name FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'stock_movements' AND COLUMN_NAME = 'batch_id' AND REFERENCED_TABLE_NAME = 'batches'",
                [$database]
            );

            if (empty($fk)) {
                Schema::table('stock_movements', function (Blueprint $table) {
                    $table->foreign('batch_id')->references('id')->on('batches')->nullOnDelete();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'sqlite') {
            return;
        }

        $database = env('DB_DATABASE');

        if (Schema::hasTable('stocks') && Schema::hasColumn('stocks', 'batch_id')) {
            $fk = DB::select(
                "SELECT CONSTRAINT_NAME as name FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'stocks' AND COLUMN_NAME = 'batch_id' AND REFERENCED_TABLE_NAME = 'batches'",
                [$database]
            );

            if (!empty($fk)) {
                $name = $fk[0]->name;
                Schema::table('stocks', function (Blueprint $table) use ($name) {
                    $table->dropForeign($name);
                });
            }
        }

        if (Schema::hasTable('stock_movements') && Schema::hasColumn('stock_movements', 'batch_id')) {
            $fk = DB::select(
                "SELECT CONSTRAINT_NAME as name FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'stock_movements' AND COLUMN_NAME = 'batch_id' AND REFERENCED_TABLE_NAME = 'batches'",
                [$database]
            );

            if (!empty($fk)) {
                $name = $fk[0]->name;
                Schema::table('stock_movements', function (Blueprint $table) use ($name) {
                    $table->dropForeign($name);
                });
            }
        }
    }
};
