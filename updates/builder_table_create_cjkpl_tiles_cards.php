<?php namespace Cjkpl\Tiles\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateCjkplTilesCards extends Migration
{
    public function up()
    {
        Schema::create('cjkpl_tiles_cards', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('language')->nullable();
            $table->string('image')->nullable();
            $table->string('url')->nullable();
            $table->string('target')->nullable();
            $table->string('tags')->nullable();
            $table->string('classes')->nullable();
            $table->boolean('is_visible')->default(0);
            $table->integer('sort_order')->default(0);
            $table->integer('section_id')->nullable()->unsigned();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('cjkpl_tiles_cards');
    }
}
