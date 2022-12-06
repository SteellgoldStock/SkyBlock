<?php

namespace steellgold\skyblock\utils\database;

use steellgold\skyblock\SkyBlock;

class MySQL {

	public static function mysqli(): \mysqli {
		$config = SkyBlock::getInstance()->getConfig();
		return new \mysqli(
			$config->get("database")["host"],
			$config->get("database")["username"],
			$config->get("database")["password"],
			$config->get("database")["database"],
			$config->get("database")["port"]
		);
	}

	public static function default(\mysqli $mysqli): void {
		$mysqli->query("CREATE TABLE IF NOT EXISTS players (
			id INT PRIMARY KEY NOT NULL,
			player VARCHAR(100),
			island VARCHAR(100) DEFAULT null
		)");
	}
}