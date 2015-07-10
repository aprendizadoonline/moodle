<?php

use yii\db\Schema;
use yii\db\Migration;

class m150702_141534_score_modify extends Migration {
    public function safeUp() {
        $this->dropForeignKey('fk_score_level', '{{%score}}');
		$this->dropForeignKey('fk_score_game', '{{%score}}');
		$this->dropForeignKey('fk_score_user', '{{%score}}');

        $this->dropPrimaryKey('pk_score', '{{%score}}');

        $this->addColumn('{{%score}}', 'id', Schema::TYPE_PK . ' FIRST');

        $this->addForeignKey('fk_score_user', '{{%score}}', 'user_id', '{{%user}}', 'id', 'RESTRICT');
		$this->addForeignKey('fk_score_game', '{{%score}}', 'game_id', '{{%game}}', 'id', 'RESTRICT');
		$this->addForeignKey('fk_score_level', '{{%score}}', 'level_id', '{{%level}}', 'id', 'RESTRICT');
    }

    public function safeDown() {
        $this->dropColumn('{{%score}}', 'id');
        $this->addPrimaryKey('pk_score', '{{%score}}', ['game_id', 'level_id', 'user_id']);
    }
}
