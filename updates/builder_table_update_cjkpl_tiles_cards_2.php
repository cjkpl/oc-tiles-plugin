<?php namespace Cjkpl\Tiles\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateCjkplTilesCards2 extends Migration
{
    public function up()
    {
        Schema::table('cjkpl_tiles_cards', function($table)
        {
            $table->text('content')->nullable();
            $table->boolean('autolink_content')->default(1);
        });
    }
    
    public function down()
    {
        Schema::table('cjkpl_tiles_cards', function($table)
        {
            $table->dropColumn('content');
            $table->dropColumn('autolink_content');
        });
    }
}
