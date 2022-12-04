<?php

namespace steellgold\skyblock\player;

use Exception;
use pocketmine\player\Player;
use WeakMap;

final class SkyBlockIsland {
	/**
	 * @var WeakMap
	 * @phpstan-var WeakMap<Player, SkyBlockIsland>
	 */
	private static WeakMap $data;

	/** @throws Exception */
	public static function get(Player $player) : SkyBlockIsland {
		self::$data ??= new WeakMap();

		return self::$data[$player] ??= self::loadSessionData($player);
	}

	/** @throws Exception */
	private static function loadSessionData(Player $player) : SkyBlockIsland {
		return new SkyBlockIsland($player->getXuid());
	}

	/**
	 * @param int $identifier
	 */
	public function __construct(
		private int $identifier
	){ }

	/** @return int */
	public function getIdentifier() : int{
		return $this->identifier;
	}
}