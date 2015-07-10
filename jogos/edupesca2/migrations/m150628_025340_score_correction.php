<?php

use yii\db\Schema;
use yii\db\Migration;

class m150628_025340_score_correction extends Migration
{
    public function up()
    {
	$this->dropColumn('{{%score}}', 'data');
	$this->addColumn('{{%score}}', 'points', Schema::TYPE_INTEGER);
    }

    public function down()
    {
        $this->dropColumn('{{%score}}', 'points');
        $this->addColumn('{{%score}}', 'data', Schema::TYPE_BINARY);
    }
}
