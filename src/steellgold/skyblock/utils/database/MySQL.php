<?php

namespace steellgold\skyblock\utils\database;

use steellgold\skyblock\commands\subs\IslandAcceptCommand;
use steellgold\skyblock\SkyBlock;

class MySQL {

	private static \mysqli $mysqli;

	public function __construct() {
		$config = SkyBlock::getInstance()->getConfig();
		self::$mysqli = new \mysqli(
			$config->get("database")["host"],
			$config->get("database")["username"],
			$config->get("database")["password"],
			$config->get("database")["database"],
			$config->get("database")["port"]
		);
		self::default();
	}

	public static function mysqli(): \mysqli {
		return self::$mysqli;
	}

	public static function default(): void {
		self::mysqli()->query("CREATE TABLE IF NOT EXISTS players (
			id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
			player VARCHAR(100),
			island VARCHAR(100) DEFAULT null
		)");

		self::mysqli()->query("CREATE TABLE IF NOT EXISTS islands (
			id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
			uuid VARCHAR(100) NOT NULL,
    		island_name VARCHAR(100) NOT NULL,
			owner VARCHAR(100) NOT NULL,
			members JSON NOT NULL
		)");
	}

	public static function removeIsland(string $uuid): void {
		$mysqli = self::mysqli();
		$mysqli->query("DELETE FROM islands WHERE uuid = '{$uuid}'");
	}

	public static function updateIsland($column, $value, $uuid): void {
		$mysqli = self::mysqli();
		$mysqli->query("UPDATE islands SET {$column} = '{$value}' WHERE uuid = '{$uuid}'");
	}

	public static function islandExists(string $uuid): bool {
		$mysqli = self::mysqli();
		$data = $mysqli->query("SELECT * FROM islands WHERE uuid = '{$uuid}'");
		return $data->num_rows > 0;
	}

	public static function getIsland(string $uuid): ?array {
		$mysqli = self::mysqli();
		$data = $mysqli->query("SELECT * FROM islands WHERE uuid = '{$uuid}'");
		if (!$data) {
			return null;
		} else {
			return $data->fetch_assoc();
		}
	}

	public static function updatePlayer($column, $value, $player): void {
		$mysqli = self::mysqli();
		$mysqli->query("UPDATE players SET {$column} = '{$value}' WHERE player = '{$player}'");
	}
}