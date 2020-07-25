<?php namespace Cjkpl\Tiles\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateCjkplTilesSections extends Migration
{
    public function up()
    {
        Schema::create('cjkpl_tiles_sections', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('name');
            $table->boolean('is_visible')->default(0);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('cjkpl_tiles_sections');
    }
}
