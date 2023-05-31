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
        Schema::create("Users", function (Blueprint $table) {
            $table->id();
            $table->tinyText("username")->nullable(false);
            $table->tinyText("password")->nullable(false);
            $table->string("token")->nullable(true);
            $table->integer("token_expiration")->nullable(true);
            $table->timestamp("c_date")->useCurrent();
            $table->timestamp("m_date")->nullable(true)->useCurrentOnUpdate();
            $table->tinyInteger("is_valid")->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Users');
    }
};
