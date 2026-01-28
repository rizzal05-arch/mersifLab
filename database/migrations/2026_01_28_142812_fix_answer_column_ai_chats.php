<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        Schema::table('ai_chats', function (Blueprint $table) {
            $table->longText('answer_new')->nullable();
        });

        DB::statement('UPDATE ai_chats SET answer_new = answer');

        Schema::table('ai_chats', function (Blueprint $table) {
            $table->dropColumn('answer');
        });

        Schema::table('ai_chats', function (Blueprint $table) {
            $table->renameColumn('answer_new', 'answer');
        });
    }

    public function down()
    {
        Schema::table('ai_chats', function (Blueprint $table) {
            $table->string('answer_old', 255)->nullable();
        });

        DB::statement('UPDATE ai_chats SET answer_old = answer');

        Schema::table('ai_chats', function (Blueprint $table) {
            $table->dropColumn('answer');
            $table->renameColumn('answer_old', 'answer');
        });
    }
};