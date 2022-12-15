<?php

namespace steellgold\skyblock\player;

use Exception;
use pocketmine\player\Player;
use steellgold\skyblock\utils\database\MySQL;
use steellgold\skyblock\utils\TextUtils;
use WeakMap;

final class SkyBlockPlayer {

	/**
	 * @param string $name
	 * @param SkyBlockIsland|null $island
	 */
	public function __construct(
		private string          $name,
		private ?SkyBlockIsland $island
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
			MySQL::mysqli()->query("INSERT INTO players (player, island) VALUES ('{$player->getName()}', 'null')");
		}

		$data = MySQL::mysqli()->query("SELECT * FROM players WHERE player = '{$player->getName()}'")->fetch_assoc();

		$island = null;
		if ($data["island"] !== "null") {
			if (MySQL::islandExists($data["island"])) {
				if (in_array($player->getName(), json_decode(MySQL::getIsland($data["island"])["members"]))) {
					$island = SkyBlockIsland::loadIslandSession($data["island"]);
					$player->sendMessage(TextUtils::text("Votre île a été chargée avec succès ! §c(message factice)"));
				} else $player->sendMessage(TextUtils::text("Pendant votre absence, vous avez été exclu de l'île."));
			} else $player->sendMessage(TextUtils::text("Pendant votre absence, votre île a été supprimée par son propriétaire."));
		}

		if ($island === null) {
			$player->teleport($player->getServer()->getWorldManager()->getDefaultWorld()->getSafeSpawn());
			$player->getInventory()->clearAll();
		}

		return new SkyBlockPlayer($player->getName(), $island);
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
}