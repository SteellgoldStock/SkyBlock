<?php

namespace steellgold\skyblock\player;

use Exception;
use pocketmine\player\Player;
use WeakMap;

final class SkyBlockPlayer {

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
		return new SkyBlockPlayer($player->getXuid());
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