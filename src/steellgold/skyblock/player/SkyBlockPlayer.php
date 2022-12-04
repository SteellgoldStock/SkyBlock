<?php

namespace steellgold\skyblock\player;

use Exception;
use pocketmine\player\Player;
use WeakMap;

final class SkyBlockPlayer {

	/**
	 * @param int $identifier
	 * @param SkyBlockIsland|null $island
	 */
	public function __construct(
		private int $identifier,
		private ?SkyBlockIsland $island
	){ }

	/**
	 * @var WeakMap
	 * @phpstan-var WeakMap<Player, SkyBlockPlayer>
	 */
	private static WeakMap $data;

	/** @throws Exception */
	public static function get(Player $player) : SkyBlockPlayer {
		self::$data ??= new WeakMap();

		return self::$data[$player] ??= self::loadSessionData($player);
	}

	/** @throws Exception */
	private static function loadSessionData(Player $player) : SkyBlockPlayer {
		return new SkyBlockPlayer($player->getXuid(), null);
	}

	/** @return int */
	public function getIdentifier() : int{
		return $this->identifier;
	}

	/** @param SkyBlockIsland|null $island */
	public function setIsland(?SkyBlockIsland $island): void {
		$this->island = $island;
	}

	/** @return SkyBlockIsland|null */
	public function getIsland(): ?SkyBlockIsland {
		return $this->island;
	}

	public function hasIsland(): bool {
		return $this->island !== null;
	}
}