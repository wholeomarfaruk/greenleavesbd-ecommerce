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
        Schema::table('categories', function (Blueprint $table) {
                $table->boolean('is_homepage_show')->default(false);
                $table->integer('display_order')->default(0);
                $table->boolean('is_show_in_menu')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
                $table->dropColumn('is_homepage_show');
                $table->dropColumn('display_order');
                $table->dropColumn('is_show_in_menu');
        });
    }
};
