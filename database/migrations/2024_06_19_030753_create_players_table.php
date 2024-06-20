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
        Schema::create('players', function (Blueprint $table) {
            $table->bigIncrements('id')->unique();
            $table->string('uuid',255)->nullable(false);
            $table->string('name',255)->nullable();
            $table->string('social_id',255)->nullable();
            $table->tinyInteger('media_type', false)->length(4)->nullable();
            $table->string('media_name',255)->nullable();
            $table->string('gcm_id',255)->nullable();
            $table->string('device_id',255)->nullable();
            $table->string('device_platform',255)->nullable();
            $table->string('app_version',255)->nullable();
            $table->string('country',255)->nullable();
            $table->text('game_info')->nullable();
            $table->bigInteger('level')->default(0);
            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};
