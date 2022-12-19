<?php

namespace steellgold\skyblock\player;

use Exception;
use pocketmine\player\Player;
use steellgold\skyblock\utils\database\MySQL;
use steellgold\skyblock\utils\TextUtils;
use steellgold\skyblock\utils\WorldUtils;
use WeakMap;

final class SkyBlockPlayer {

	/**
	 * @param string $name
	 * @param SkyBlockIsland|null $island
	 * @param array $lastKick
	 * @param array $islandsBans
	 */
	public function __construct(
		private string          $name,
		private ?SkyBlockIsland $island,
		private array           $lastKick,
		private array           $islandsBans
	) {
	}

	/**
	 * @var WeakMap
	 * @phpstan-var WeakMap<Player, SkyBlockPlayer>
	 */
	private static WeakMap $data;

	/** @throws Exception */
	public static function get(Player $player): SkyBlockPlayer {
		self::$data ??= new WeakMap();

		return self::$data[$player] ??= self::loadSessionData($player);
	}

	/** @throws Exception */
	private static function loadSessionData(Player $player): SkyBlockPlayer {
		if (!$player->hasPlayedBefore()) {
			$base = json_encode([]);
			MySQL::mysqli()->query("INSERT INTO players (player, island, last_kick, islands_bans) VALUES ('{$player->getName()}', 'null', '{$base}', '{$base}')");
		}

		$data = MySQL::mysqli()->query("SELECT * FROM players WHERE player = '{$player->getName()}'")->fetch_assoc();

		/** @var null|SkyBlockIsland $island */
		$island = null;

		if ($data["island"] !== "null") {
			if (MySQL::islandExists($data["island"]) AND WorldUtils::isWorldExist($data["island"])) {
				if (in_array($player->getName(), json_decode(MySQL::getIsland($data["island"])["members"]))) {
					$island = SkyBlockIsland::loadIslandSession($data["island"]);
					$player->sendMessage(TextUtils::text("Votre île a été chargée avec succès ! §c(message factice)"));
				} else {
					$kickinfos = json_decode($data["last_kick"], true);
					if (!$kickinfos["keep_inventory"]) $player->getInventory()->clearAll();
					if (!$kickinfos["keep_enderchest"]) $player->getEnderInventory()->clearAll();
					if (!$kickinfos["keep_experience"]) {
						$player->getXpManager()->setXpLevel(0);
						$player->getXpManager()->setXpProgress(0);
					}
					$player->sendMessage(TextUtils::text(base64_decode($kickinfos["message"])));

				}
			} else $player->sendMessage(TextUtils::text("Pendant votre absence, votre île a été supprimée par son propriétaire."));
		}

		if ($island === null) {
			$player->teleport($player->getServer()->getWorldManager()->getDefaultWorld()->getSafeSpawn());
			$player->getInventory()->clearAll();
		}

		MySQL::updatePlayer("island", $island?->getIdentifier() ?? "null", $player->getName());
		return new SkyBlockPlayer($player->getName(), $island, json_decode($data["last_kick"], true), json_decode($data["islands_bans"], true));
	}

	/** @return string */
	public function getName(): string {
		return $this->name;
	}

	private function setName(string $name): void {
		$this->name = $name;
	}

	/** @param SkyBlockIsland|null $island */
	public function setIsland(?SkyBlockIsland $island): void {
		MySQL::updatePlayer("island", $island === null ? "null" : $island->getIdentifier(), $this->getName());
		$this->island = $island;
	}

	/** @return SkyBlockIsland|null */
	public function getIsland(): ?SkyBlockIsland {
		return $this->island;
	}

	public function getIslandIdentifier(): ?string {
		return $this->island !== null ? $this->island->getIdentifier() : "null";
	}

	public function hasIsland(): bool {
		return $this->island !== null;
	}

	/**
	 * @return array
	 */
	public function getIslandsBans(): array {
		return $this->islandsBans;
	}

	public function addIslandBan(?string $reason, SkyBlockIsland $island): void {
		$this->islandsBans[] = [
			"date" => time(),
			"reason" => $reason,
			"island" => $island->getIdentifier()
		];
		MySQL::updatePlayer("islands_bans", json_encode($this->islandsBans), $this->getName());
	}

	/**
	 * @return array
	 */
	public function getLastKick(): array {
		return $this->lastKick;
	}
}