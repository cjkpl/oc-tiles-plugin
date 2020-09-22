<?php namespace Cjkpl\Tiles\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateCjkplTilesCards3b extends Migration
{
    public function up()
    {
        Schema::table('cjkpl_tiles_cards', function($table)
        {
            $table->text('tags_url')->nullable()->unsigned(false)->default(null)->change();
        });
    }
    
    public function down()
    {
        Schema::table('cjkpl_tiles_cards', function($table)
        {
            $table->string('tags_url', 191)->nullable()->unsigned(false)->default(null)->change();
        });
    }
}
