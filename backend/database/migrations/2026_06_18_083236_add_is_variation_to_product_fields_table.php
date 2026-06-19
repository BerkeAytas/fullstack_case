<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_fields', function (Blueprint $table) {
            $table->boolean('is_variation')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('product_fields', function (Blueprint $table) {
            $table->dropColumn('is_variation');
        });
    }
};