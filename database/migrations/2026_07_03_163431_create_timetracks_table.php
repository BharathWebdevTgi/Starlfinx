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
        Schema::create('timetracks', function (Blueprint $table) {
			
			$table->id();
			$table->string('description');
			
			$table->foreignId('project_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
			$table->foreignId('user_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();

			$table->dateTime('start_time');
			$table->dateTime('end_time');

			$table->timestamps();
			$table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timetracks');
    }
};
