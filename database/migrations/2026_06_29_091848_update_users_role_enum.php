<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL ne supporte pas ALTER COLUMN directement sur les ENUM
        // On modifie via une requête brute
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('passager','agent','bagagiste','admin') NOT NULL DEFAULT 'passager'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('passager','agent','admin') NOT NULL DEFAULT 'passager'");
    }
};
