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
        Schema::create('event_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('player_id');
            $table->foreign('player_id')->references('id')->on('players')->onDelete('cascade');
            $table->string('event_name', 255);
            $table->bigInteger('level')->default(0);
            $table->string('level_version', 255)->nullable();
            $table->tinyInteger('media_type')->nullable();
            $table->string('media_name', 255)->nullable();
            $table->string('config_name_test', 255)->nullable();
            $table->string('config_type_test', 255)->nullable();
            $table->integer('day_login');
            $table->integer('day_install');
            $table->bigInteger('section_login');
            $table->text('event_parameters')->nullable();
            $table->unsignedBigInteger('project_app_version_id');
            $table->foreign('project_app_version_id')->references('id')->on('project_app_versions')->onDelete('cascade');
            $table->timestamps(); // created_at và updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_log');
    }
};
