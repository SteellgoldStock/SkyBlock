<?php

namespace steellgold\skyblock\player;

use Exception;
use steellgold\skyblock\utils\database\MySQL;
use WeakMap;

final class SkyBlockIsland {

	public static $islands = [];

	/** @throws Exception */
	private static function loadSessionData(string $uuid): SkyBlockIsland {
		$data = MySQL::mysqli()->query("SELECT * FROM islands WHERE uuid = '$uuid'")->fetch_assoc();
		var_dump($data);
		// return new SkyBlockIsland($player->getXuid(), $player->getName(), $player->getName(), [$player->getName()]);
		return new SkyBlockIsland("cc","aaz","az",[]);
	}

	/**
	 * @param string $identifier
	 * @param string $island_name
	 * @param string $owner
	 * @param string[] $members
	 */
	public function __construct(
		private string $identifier,
		private string $island_name,
		private string $owner,
		private array  $members
	) {
	}

	/** @return string */
	public function getIdentifier(): string {
		return $this->identifier;
	}

	/** @param string $identifier */
	private function setIdentifier(string $identifier): void {
		$this->identifier = $identifier;
	}

	/** @return string */
	public function getIslandName(): string {
		return $this->island_name;
	}

	/** @param string $island_name */
	public function setIslandName(string $island_name): void {
		$this->island_name = $island_name;
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