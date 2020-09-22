<?php namespace Cjkpl\Tiles\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateCjkplTilesCards3 extends Migration
{
    public function up()
    {
        Schema::table('cjkpl_tiles_cards', function($table)
        {
            $table->renameColumn('tags_url_pattern', 'tags_url');
        });
    }
    
    public function down()
    {
        Schema::table('cjkpl_tiles_cards', function($table)
        {
            $table->renameColumn('tags_url', 'tags_url_pattern');
        });
    }
}