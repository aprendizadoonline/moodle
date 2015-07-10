<?php

use yii\db\Schema;
use yii\db\Migration;

class m150629_123949_add_level_title extends Migration {
    public function up() {
		$this->addColumn('{{%level}}', 'title', Schema::TYPE_STRING);
    }

    public function down() {
        $this->dropColumn('{{%level}}', 'title');
    }
}
