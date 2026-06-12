<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::table('voyages', function (Blueprint $table) {
            $table->decimal('prix', 10, 2)->default(0)->after('capacite');
            $table->string('devise', 10)->default('XOF')->after('prix');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('voyages', function (Blueprint $table) {
            //
        });
    }
};
