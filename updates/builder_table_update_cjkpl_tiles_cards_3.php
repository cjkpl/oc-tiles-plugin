<?php namespace Cjkpl\Tiles\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateCjkplTilesCards3 extends Migration
{
    public function up()
    {
        Schema::table('cjkpl_tiles_cards', function($table)
        {
            $table->boolean('is_seo')->default(1);
        });
    }
    
    public function down()
    {
        Schema::table('cjkpl_tiles_cards', function($table)
        {
            $table->dropColumn('is_seo');
        });
    }
}
