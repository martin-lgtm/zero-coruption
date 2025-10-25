<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
 Schema::create('reports', function (Blueprint $table) {
            $table->id();


            $table->foreignId('municipality_id')->constrained()->cascadeOnDelete();

            $table->enum('gender', ['Женски', 'Машки']);

            $table->enum('bribery_experience', [
                'Да, ми беше побарано мито',
                'Да, сум понудил/a мито',
                'Не'
            ])->comment('Дали сте биле во ситуација каде што некој ви побарал или сами сте понудиле мито?');

            $table->enum('felt_choice', ['Да', 'Не', 'Преферирам да не одговорам']);

            $table->enum('admin_rank', [
                'Машки - висок ранг (раководна или позиција на одговорно лице)',
                'Женски - висок ранг (раководна или позиција на одговорно лице)',
                'Машки - низок ранг (вработен во јавна администрација на секоја друга позиција)',
                'Женски - низок ранг (вработена во јавна администрација на секоја друга позиција)',
                'Преферирам да не одговорам',
            ]);

            $table->text('story')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reports');
    }
};
