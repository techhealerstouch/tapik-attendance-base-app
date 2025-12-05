<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIdentifierScansTable extends Migration
{
    public function up()
    {
        Schema::create('identifier_scans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('reference_code'); // The activate_code or RFID that was scanned
            $table->unsignedInteger('scan_count')->default(1);
            $table->timestamp('first_scanned_at');
            $table->timestamp('last_scanned_at');
            $table->timestamps();

            // Ensure one record per user per event per reference code
            $table->unique(['event_id', 'user_id', 'reference_code'], 'unique_event_user_code');
            
            // Index for faster lookups
            $table->index(['event_id', 'reference_code']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('identifier_scans');
    }
}