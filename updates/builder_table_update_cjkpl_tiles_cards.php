<?php namespace Cjkpl\Tiles\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateCjkplTilesCards extends Migration
{
    public function up()
    {
        Schema::table('cjkpl_tiles_cards', function($table)
        {
            $table->string('headline')->nullable();
            $table->string('card_url')->nullable();
            $table->string('tags_url_pattern')->nullable();
            $table->string('photo')->nullable();
            $table->string('name')->nullable();
            $table->string('status')->nullable();
            $table->text('custom_set')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('cjkpl_tiles_cards', function($table)
        {
            $table->dropColumn('headline');
            $table->dropColumn('card_url');
            $table->dropColumn('tags_url_pattern');
            $table->dropColumn('photo');
            $table->dropColumn('name');
            $table->dropColumn('status');
            $table->dropColumn('custom_set');
        });
    }
}
