<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('event_tables', function (Blueprint $table) {
            $table->boolean('manual_assignment')->default(false)->after('chair_count');
        });
    }

    public function down()
    {
        Schema::table('event_tables', function (Blueprint $table) {
            $table->dropColumn('manual_assignment');
        });
    }
};