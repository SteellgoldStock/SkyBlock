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
			id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
			player VARCHAR(100),
			island VARCHAR(100) DEFAULT null
		)");

		$mysqli->query("CREATE TABLE IF NOT EXISTS islands (
			id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
			uuid VARCHAR(100) NOT NULL,
    		public_name VARCHAR(100) NOT NULL,
			owner VARCHAR(100) NOT NULL,
			members VARCHAR(100) NOT NULL
		)");
	}

	public static function updateIsland($column, $value, $uuid): void {
		$mysqli = self::mysqli();
		$mysqli->query("UPDATE islands SET {$column} = '{$value}' WHERE uuid = '{$uuid}'");
	}
}