<?php

use yii\db\Schema;
use yii\db\Migration;

class m150706_124327_score_add_sessionid extends Migration {
    public function up() {
        $this->addColumn('{{%score}}', 'session_id', Schema::TYPE_STRING);
        $this->addForeignKey('fk_score_session_id', '{{%score}}', 'session_id', '{{%game_session}}', 'id', 'RESTRICT');
    }

    public function down() {
        $this->dropForeignKey('fk_score_session_id', '{{%score}}');
        $this->dropColumn('{{%score}}', 'session_id');
    }
}
