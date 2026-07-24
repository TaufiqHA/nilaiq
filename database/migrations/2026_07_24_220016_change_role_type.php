<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'role')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();
        $alreadyUpdated = false;

        if ($driver === 'sqlite') {
            $sql = DB::select("SELECT sql FROM sqlite_master WHERE type='table' AND name='users'")[0]->sql ?? '';
            if (str_contains($sql, "'admin'")) {
                $alreadyUpdated = true;
            }
        } elseif ($driver === 'mysql') {
            $column = DB::select("SHOW COLUMNS FROM users WHERE Field = 'role'")[0] ?? null;
            if ($column && str_contains($column->Type, "'admin'")) {
                $alreadyUpdated = true;
            }
        }

        if (! $alreadyUpdated) {
            Schema::table('users', function (Blueprint $table) {
                $table->enum('role', ['mapel', 'wali_kelas', 'admin'])->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('users', 'role')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();
        $alreadyRolledBack = false;

        if ($driver === 'sqlite') {
            $sql = DB::select("SELECT sql FROM sqlite_master WHERE type='table' AND name='users'")[0]->sql ?? '';
            if (! str_contains($sql, "'admin'")) {
                $alreadyRolledBack = true;
            }
        } elseif ($driver === 'mysql') {
            $column = DB::select("SHOW COLUMNS FROM users WHERE Field = 'role'")[0] ?? null;
            if ($column && ! str_contains($column->Type, "'admin'")) {
                $alreadyRolledBack = true;
            }
        }

        if (! $alreadyRolledBack) {
            Schema::table('users', function (Blueprint $table) {
                $table->enum('role', ['mapel', 'wali_kelas'])->change();
            });
        }
    }
};
