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
		// TODO: Load data from database
		return new SkyBlockIsland($player->getXuid(), $player->getName(), [$player->getName()]);
	}

	/**
	 * @param int $identifier
	 * @param string $owner
	 * @param string[] $members
	 */
	public function __construct(
		private int $identifier,
		private string $owner,
		private array $members
	){ }

	/** @return int */
	public function getIdentifier() : int{
		return $this->identifier;
	}

	/** @param int $identifier */
	private function setIdentifier(int $identifier): void {
		$this->identifier = $identifier;
	}

	/** @return string */
	public function getOwner(): string {
		return $this->owner;
	}

	/** @param string $owner */
	public function setOwner(string $owner): void {
		$this->owner = $owner;
	}

	/** @return array */
	public function getMembers(): array {
		return $this->members;
	}

	/** @param array $members */
	public function setMembers(array $members): void {
		$this->members = $members;
	}

	/**
	 * @param string $member
	 * @return void
	 */
	public function addMember(string $member): void {
		$this->members[] = $member;
	}

	/**
	 * @param string $member
	 * @return void
	 */
	public function removeMember(string $member): void {
		$this->members = array_diff($this->members, [$member]);
	}
}