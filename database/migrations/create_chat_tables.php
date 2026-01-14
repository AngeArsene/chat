<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });

        Schema::create('participations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->bigInteger('messageable_id')->unsigned();
            $table->string('messageable_type');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('conversations');
        Schema::dropIfExists('participations');
    }
};
