<?php

/**
 * Crea las tablas para el feature "Prode" (pronostico deportivo sin dinero).
 *
 * prode_usuario:     usuarios del prode (independiente de Cruge)
 * prode_prediccion:  una prediccion por (usuario, partido)
 * prode_fecha_publicada: marca de "publicado" por (torneo, NFecha)
 * prode_reset_token: tokens para recuperacion de password
 */
class m260725_000001_add_prode extends CDbMigration
{
	public function safeUp()
	{
		if ($this->dbConnection->schema->getTable('prode_usuario') === null) {
			$this->createTable('prode_usuario', array(
				'idProdeUsuario' => 'pk',
				'email' => 'VARCHAR(150) NOT NULL',
				'nombre' => 'VARCHAR(100) NOT NULL',
				'passwordHash' => 'VARCHAR(255) NOT NULL',
				'activo' => 'TINYINT(1) NOT NULL DEFAULT 1',
				'esAdmin' => 'TINYINT(1) NOT NULL DEFAULT 0',
				'createdAt' => 'DATETIME NOT NULL',
				'lastLogin' => 'DATETIME NULL',
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8');

			$this->createIndex('ux_prode_usuario_email', 'prode_usuario', 'email', true);
			$this->createIndex('ix_prode_usuario_activo', 'prode_usuario', 'activo');
		}

		if ($this->dbConnection->schema->getTable('prode_prediccion') === null) {
			$this->createTable('prode_prediccion', array(
				'idProdePrediccion' => 'pk',
				'idProdeUsuario' => 'INT(11) NOT NULL',
				'idFixture' => 'INT(11) NOT NULL',
				'golesLocal' => 'TINYINT(3) UNSIGNED NOT NULL',
				'golesVisitante' => 'TINYINT(3) UNSIGNED NOT NULL',
				'puntos' => 'TINYINT(3) UNSIGNED NULL',
				'createdAt' => 'DATETIME NOT NULL',
				'updatedAt' => 'DATETIME NOT NULL',
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8');

			$this->createIndex('ux_prode_prediccion_usuario_fixture', 'prode_prediccion', 'idProdeUsuario,idFixture', true);
			$this->createIndex('ix_prode_prediccion_fixture', 'prode_prediccion', 'idFixture');

			$this->addForeignKey('fk_prode_prediccion_usuario', 'prode_prediccion', 'idProdeUsuario', 'prode_usuario', 'idProdeUsuario', 'CASCADE', 'CASCADE');
			$this->addForeignKey('fk_prode_prediccion_fixture', 'prode_prediccion', 'idFixture', 'fixture', 'idFixture', 'CASCADE', 'CASCADE');
		}

		if ($this->dbConnection->schema->getTable('prode_fecha_publicada') === null) {
			$this->createTable('prode_fecha_publicada', array(
				'idProdeFecha' => 'pk',
				'idTorneo' => 'INT(11) NOT NULL',
				'NFecha' => 'TINYINT(3) UNSIGNED NOT NULL',
				'publicadaEn' => 'DATETIME NOT NULL',
				'publicadaPor' => 'INT(11) NULL',
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8');

			$this->createIndex('ux_prode_fecha_publicada_torneo_fecha', 'prode_fecha_publicada', 'idTorneo,NFecha', true);
			$this->addForeignKey('fk_prode_fecha_publicada_torneo', 'prode_fecha_publicada', 'idTorneo', 'torneo', 'idTorneo', 'CASCADE', 'CASCADE');
		}

		if ($this->dbConnection->schema->getTable('prode_reset_token') === null) {
			$this->createTable('prode_reset_token', array(
				'idProdeReset' => 'pk',
				'idProdeUsuario' => 'INT(11) NOT NULL',
				'token' => 'VARCHAR(64) NOT NULL',
				'expiraEn' => 'DATETIME NOT NULL',
				'usadoEn' => 'DATETIME NULL',
				'createdAt' => 'DATETIME NOT NULL',
			), 'ENGINE=InnoDB DEFAULT CHARSET=utf8');

			$this->createIndex('ux_prode_reset_token', 'prode_reset_token', 'token', true);
			$this->createIndex('ix_prode_reset_usuario', 'prode_reset_token', 'idProdeUsuario');
			$this->addForeignKey('fk_prode_reset_usuario', 'prode_reset_token', 'idProdeUsuario', 'prode_usuario', 'idProdeUsuario', 'CASCADE', 'CASCADE');
		}
	}

	public function safeDown()
	{
		$this->dropForeignKey('fk_prode_reset_usuario', 'prode_reset_token');
		$this->dropTable('prode_reset_token');

		$this->dropForeignKey('fk_prode_fecha_publicada_torneo', 'prode_fecha_publicada');
		$this->dropTable('prode_fecha_publicada');

		$this->dropForeignKey('fk_prode_prediccion_fixture', 'prode_prediccion');
		$this->dropForeignKey('fk_prode_prediccion_usuario', 'prode_prediccion');
		$this->dropTable('prode_prediccion');

		$this->dropTable('prode_usuario');
	}
}
