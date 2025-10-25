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
        Schema::table('reports', function (Blueprint $table) {
            // Remove old column
            if (Schema::hasColumn('reports', 'bribery_experience')) {
                $table->dropColumn('bribery_experience');
            }

            // Add two new separate columns
            $table->string('bribe_requested')->nullable()
                  ->comment('Дали ви било побарано мито? (Да / Не)');
            $table->string('bribe_offered')->nullable()
                  ->comment('Дали сте понудиле мито? (Да / Не)');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            // Rollback changes
            $table->dropColumn(['bribe_requested', 'bribe_offered']);
            $table->string('bribery_experience')->nullable();
        });
    }
};
