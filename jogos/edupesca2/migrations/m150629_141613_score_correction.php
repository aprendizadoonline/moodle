<?php

use yii\db\Schema;
use yii\db\Migration;

class m150629_141613_score_correction extends Migration
{
    public function up() {
		$this->dropForeignKey('fk_score_level', '{{%score}}');
		$this->addForeignKey('fk_score_level', '{{%score}}', 'level_id', '{{%level}}', 'id', 'RESTRICT');
    }

    public function down() {
        $this->dropForeignKey('fk_score_level', '{{%score}}');
		$this->addForeignKey('fk_score_level', '{{%score}}', 'level_id', '{{%user}}', 'id', 'RESTRICT');
    }
}
