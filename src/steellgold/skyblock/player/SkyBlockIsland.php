<?php

namespace steellgold\skyblock\player;

use Exception;
use steellgold\skyblock\utils\database\MySQL;
use WeakMap;

final class SkyBlockIsland {

	public static array $islands = [];

	/**
	 * @param string $uuid
	 * @return SkyBlockIsland|null
	 * @throws Exception
	 */
	public static function loadIslandSession(string $uuid): ?SkyBlockIsland {
		$data = MySQL::mysqli()->query("SELECT * FROM islands WHERE uuid = '{$uuid}'");
		if (!$data) {
			return null;
		} else {
			$data = $data->fetch_assoc();
			return new SkyBlockIsland($data["uuid"], $data["public_name"], $data["owner"], json_decode($data["members"]));
		}
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
		MySQL::mysqli()->query("INSERT INTO islands (uuid, public_name, owner, members) VALUES ('{$this->identifier}', '{$this->island_name}', '{$this->owner}', '". json_encode($this->members) ."')");
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
		MySQL::updateIsland("public_name", $island_name, $this->identifier);
		$this->island_name = $island_name;
	}

	/** @return string */
	public function getOwner(): string {
		return $this->owner;
	}

	/** @param string $owner */
	public function setOwner(string $owner): void {
		MySQL::updateIsland("owner", $owner, $this->identifier);
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