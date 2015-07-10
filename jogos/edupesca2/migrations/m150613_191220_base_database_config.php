<?php

use yii\db\Schema;
use yii\db\Migration;

class m150613_191220_base_database_config extends Migration
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
		// Tabela de tutores
		$this->createTable('{{%user_tutor}}', [
            'tutor_id'	=> Schema::TYPE_INTEGER,
            'user_id'	=> Schema::TYPE_INTEGER,
        ], $this->tableOptions);
		$this->addPrimaryKey('pk_user_tutor', '{{%user_tutor}}', ['tutor_id', 'user_id']);
		
		// Tabela de turmas
		$this->createTable('{{%cohort}}', [
            'id'	=> Schema::TYPE_PK,
            'name'	=> Schema::TYPE_STRING,
        ], $this->tableOptions);
	
		// Tabela de relacionamentos entre turmas e pessoas (alunos, necessariamente)
		$this->createTable('{{%user_cohort}}', [
            'cohort_id'	=> Schema::TYPE_INTEGER,
            'user_id'	=> Schema::TYPE_INTEGER,
        ], $this->tableOptions);
		$this->addPrimaryKey('pk_user_cohort', '{{%user_cohort}}', ['cohort_id', 'user_id']);
	
		// Tabela de pontuação
		$this->createTable('{{%score}}', [
            'user_id'	=> Schema::TYPE_INTEGER . ' NOT NULL',
            'game_id'	=> Schema::TYPE_INTEGER . ' NOT NULL',
            'level_id'	=> Schema::TYPE_INTEGER . ' NOT NULL',
			'data' 		=> Schema::TYPE_BINARY,
			'date'		=> Schema::TYPE_INTEGER
        ], $this->tableOptions);
		$this->addPrimaryKey('pk_score', '{{%score}}', ['user_id', 'game_id', 'level_id']);
	
		// Tabela de jogos
		$this->createTable('{{%game}}', [
            'id'	=> Schema::TYPE_PK,
            'name'	=> Schema::TYPE_STRING,
            'path'	=> Schema::TYPE_STRING,
        ], $this->tableOptions);
	
		// Tabela de níveis
		$this->createTable('{{%level}}', [
			'id'			=> Schema::TYPE_PK,
			'game_id'		=> Schema::TYPE_INTEGER,
			'position'		=> Schema::TYPE_INTEGER,
			'created_by'	=> Schema::TYPE_INTEGER,
			'created_on'	=> Schema::TYPE_INTEGER,
			'data'			=> Schema::TYPE_BINARY,
        ], $this->tableOptions);
		$this->createIndex('idx_level', '{{%level}}', ['id', 'game_id'], true);
		
		// Tabela de filtros
		$this->createTable('{{%filter}}', [
			'identifier'	=> Schema::TYPE_STRING,
			'game_id'		=> Schema::TYPE_INTEGER,
			'level_id'		=> Schema::TYPE_INTEGER,
			'target_type'	=> Schema::TYPE_INTEGER,
			'target_id'		=> Schema::TYPE_INTEGER,
		], $this->tableOptions);
		$this->addPrimaryKey('pk_filter', '{{%filter}}', 'identifier');
		
		// Adiciona as chaves estrangeiras
		$this->addForeignKey('fk_tutor_rel_user', '{{%user_tutor}}', 'tutor_id', '{{%user}}', 'id', 'RESTRICT', 'RESTRICT');
		$this->addForeignKey('fk_user_rel_tutor', '{{%user_tutor}}', 'user_id', '{{%user}}', 'id', 'RESTRICT', 'RESTRICT');
		$this->addForeignKey('fk_cohort_rel_user', '{{%user_cohort}}', 'cohort_id', '{{%cohort}}', 'id', 'RESTRICT', 'RESTRICT');
		$this->addForeignKey('fk_user_rel_cohort', '{{%user_cohort}}', 'user_id', '{{%user}}', 'id', 'RESTRICT', 'RESTRICT');
		$this->addForeignKey('fk_score_user', '{{%score}}', 'user_id', '{{%user}}', 'id', 'RESTRICT');
		$this->addForeignKey('fk_score_game', '{{%score}}', 'game_id', '{{%game}}', 'id', 'RESTRICT');
		$this->addForeignKey('fk_score_level', '{{%score}}', 'level_id', '{{%user}}', 'id', 'RESTRICT');
		$this->addForeignKey('fk_level_game', '{{%level}}', 'game_id', '{{%game}}', 'id', 'RESTRICT', 'RESTRICT');
		$this->addForeignKey('fk_level_creator', '{{%level}}', 'created_by', '{{%user}}', 'id', 'RESTRICT', 'RESTRICT');
    }

	/**
     * @inheritdoc
     */
    public function safeDown() {
		$this->dropForeignKey('fk_level_creator', '{{%level}}');
		$this->dropForeignKey('fk_level_game', '{{%level}}');
		$this->dropForeignKey('fk_score_level', '{{%score}}');
		$this->dropForeignKey('fk_score_game', '{{%score}}');
		$this->dropForeignKey('fk_score_user', '{{%score}}');
		$this->dropForeignKey('fk_user_rel_cohort', '{{%user_cohort}}');
		$this->dropForeignKey('fk_cohort_rel_user', '{{%user_cohort}}');
		$this->dropForeignKey('fk_tutor_rel_user', '{{%user_tutor}}');
		$this->dropForeignKey('fk_user_rel_tutor', '{{%user_tutor}}');
		
        $this->dropTable('{{%filter}}');
        $this->dropTable('{{%level}}');
		$this->dropTable('{{%game}}');
        $this->dropTable('{{%score}}');
        $this->dropTable('{{%user_cohort}}');
        $this->dropTable('{{%cohort}}');
        $this->dropTable('{{%user_tutor}}');
    }
}
