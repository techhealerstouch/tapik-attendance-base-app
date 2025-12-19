<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('event_tables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->string('table_name');
            $table->integer('chair_count');
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        Schema::create('event_table_chairs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_table_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('chair_number');
            $table->timestamps();
            
            $table->unique(['event_table_id', 'chair_number']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('event_table_chairs');
        Schema::dropIfExists('event_tables');
    }
};