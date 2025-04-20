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
        Schema::create('like_commentaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('commentary_id')->constrained('commentaries')->onDelete('cascade');
            $table->boolean('like')->default(true);
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('like_commentaries', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['commentary_id']);
            $table->dropColumn('like');
            $table->timestamps();
        });
    }
};
