<?php namespace Cjkpl\Tiles\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateCjkplTilesSections2 extends Migration
{
    public function up()
    {
        Schema::table('cjkpl_tiles_sections', function($table)
        {
            $table->integer('parent_id')->nullable()->unsigned();
            $table->integer('sort_order')->nullable();
            $table->string('slug', 255)->nullable();
            $table->boolean('is_seo')->default(1);
            $table->text('description')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('cjkpl_tiles_sections', function($table)
        {
            $table->dropColumn('parent_id');
            $table->dropColumn('sort_order');
            $table->dropColumn('slug');
            $table->dropColumn('is_seo');
            $table->dropColumn('description');
        });
    }
}
