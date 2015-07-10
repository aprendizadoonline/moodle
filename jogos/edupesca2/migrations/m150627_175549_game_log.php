<?php

use yii\db\Schema;
use yii\db\Migration;

class m150627_175549_game_log extends Migration
{
	/**
     * @var string
     */
    protected $tableOptions;
	
    /**
     * @inheritdoc
     */
    public function init() {
        parent::init();

        switch (\Yii::$app->db->driverName) {
            case 'mysql':
                $this->tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
                break;
            case 'pgsql':
                $this->tableOptions = null;
                break;
            default:
                throw new \RuntimeException('Your database is not supported!');
        }
    }
	
	/**
     * @inheritdoc
     */
    public function safeUp() {
		// Tabela de sessão
		$this->createTable('{{%game_session}}', [
            'id'		=> Schema::TYPE_STRING,
            'user_id'	=> Schema::TYPE_INTEGER,
            'game_id'	=> Schema::TYPE_INTEGER,
            'level_id'	=> Schema::TYPE_INTEGER,
		], $this->tableOptions);
		$this->addPrimaryKey('pk_game_session', '{{%game_session}}', 'id');
		
		$this->createTable('{{%game_session_step}}', [
            'id'			=> Schema::TYPE_PK,
            'session_id'	=> Schema::TYPE_STRING . ' NOT NULL',
			'time'			=> Schema::TYPE_INTEGER,
            'action'		=> Schema::TYPE_STRING,
			'data'			=> Schema::TYPE_BINARY
        ], $this->tableOptions);
		
		$this->createIndex('idx_game_session_uid', '{{%game_session}}', 'user_id', false);
		$this->createIndex('idx_game_session_gid', '{{%game_session}}', 'game_id', false);
		$this->createIndex('idx_game_session_lid', '{{%game_session}}', 'level_id', false);
		
		$this->createIndex('idx_game_session_step_sessid', '{{%game_session_step}}', 'session_id', false);
		
		$this->addForeignKey('fk_game_session_uid', '{{%game_session}}', 'user_id', '{{%user}}', 'id', 'RESTRICT', 'RESTRICT');
		$this->addForeignKey('fk_game_session_gid', '{{%game_session}}', 'game_id', '{{%game}}', 'id', 'RESTRICT', 'RESTRICT');
		$this->addForeignKey('fk_game_session_lid', '{{%game_session}}', 'level_id', '{{%level}}', 'id', 'RESTRICT', 'RESTRICT');
		
		$this->addForeignKey('fk_game_session_step_session_id', '{{%game_session_step}}', 'session_id', '{{%game_session}}', 'id', 'RESTRICT', 'RESTRICT');
    }
    
    public function safeDown() {
		$this->dropForeignKey('fk_game_session_step_session_id', '{{%game_session_step}}');
		
		$this->dropForeignKey('fk_game_session_lid', '{{%game_session}}');
		$this->dropForeignKey('fk_game_session_gid', '{{%game_session}}');
		$this->dropForeignKey('fk_game_session_uid', '{{%game_session}}');
		
		$this->dropIndex('idx_game_session_step_sessid', '{{%game_session_step}}');
		
		$this->dropIndex('idx_game_session_lid', '{{%game_session}}');
		$this->dropIndex('idx_game_session_gid', '{{%game_session}}');
		$this->dropIndex('idx_game_session_uid', '{{%game_session}}');
		
		$this->dropTable('{{%game_session_step}}');
		$this->dropTable('{{%game_session}}');
    }
}
