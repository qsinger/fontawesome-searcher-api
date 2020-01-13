<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class IconsSearchTermsJoin extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('icons_search_terms_join', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('icon_id')->nullable();
            $table->foreign('icon_id')->references('id')->on('icons')->onDelete('set null');
            $table->unsignedBigInteger('search_term_id')->nullable();
            $table->foreign('search_term_id')->references('id')->on('search_terms')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('icons_search_terms_join');
    }
}
