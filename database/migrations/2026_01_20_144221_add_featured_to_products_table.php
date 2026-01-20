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
        Schema::table('products', function (Blueprint $table) {
            // Si no existe, lo agregamos
            if (! Schema::hasColumn('products', 'featured')) {
                $table->boolean('featured')->default(false)->after('image');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'featured')) {
                $table->dropColumn('featured');
            }
        });
    }
};
