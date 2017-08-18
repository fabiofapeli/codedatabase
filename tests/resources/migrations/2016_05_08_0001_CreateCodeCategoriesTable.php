<?php
use Illuminate\Database\Schema\Blueprint;

class CreateCodeCategoriesTable
{
    public function up(){
        Schema::create('codepress_categories',function(Blueprint $table){
            $table->increments('id');
            $table->string('name');
            $table->string('description');
            $table->timestamps();
        });
    }

    public function down(){
        Schema::drop('codepress_categories');
    }
}