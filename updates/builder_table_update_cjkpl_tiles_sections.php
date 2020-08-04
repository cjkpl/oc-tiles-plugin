<?php namespace Cjkpl\Tiles\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateCjkplTilesSections extends Migration
{
    public function up()
    {
        Schema::table('cjkpl_tiles_sections', function($table)
        {
            $table->text('layout')->nullable();
            $table->text('custom_labels')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('cjkpl_tiles_sections', function($table)
        {
            $table->dropColumn('layout');
            $table->dropColumn('custom_labels');
        });
    }
}
