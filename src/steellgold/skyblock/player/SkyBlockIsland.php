<?php

namespace steellgold\skyblock\player;

use Exception;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\Position;
use pocketmine\world\World;
use steellgold\skyblock\utils\database\MySQL;
use steellgold\skyblock\utils\TextUtils;
use steellgold\skyblock\utils\WorldUtils;

final class SkyBlockIsland {

	public static array $islands = [];

	/**
	 * @param string $identifier
	 * @param string $island_name
	 * @param string $owner
	 * @param string[] $members
	 * @param Position|null $spawn
	 */
	public function __construct(
		private string    $identifier,
		private string    $island_name,
		private string    $owner,
		private array     $members,
		private ?Position $spawn
	) {
	}

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
			$position = json_decode($data["spawn"], true);

			$loaded = WorldUtils::lazyLoadWorld($data["uuid"]);
			if ($loaded) Server::getInstance()->getLogger()->info("Island {$data["uuid"]} was loaded successfully !");
			else Server::getInstance()->getLogger()->info("Island {$data["uuid"]} was not loaded successfully !");

			$island = new SkyBlockIsland($data["uuid"], $data["island_name"], $data["owner"], json_decode($data["members"]), new Position(
				$position["x"], $position["y"], $position["z"], WorldUtils::getWorldByNameNonNull($data["uuid"])
			));
			self::$islands[$island->getIdentifier()] = $island;
			return $island;
		}
	}

	/**
	 * @param SkyBlockIsland $island
	 * @return void
	 */
	public static function referenceIsland(SkyBlockIsland $island): void {
		self::$islands[$island->getIdentifier()] = $island;
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
		MySQL::updateIsland("island_name", $island_name, $this->identifier);
		$this->island_name = $island_name;
	}

	/** @return string */
	public function getOwner(): string {
		return $this->owner;
	}

	/**
	 * @param string|Player $owner
	 */
	public function setOwner(string|Player $owner): void {
		$m = $owner instanceof Player ? $owner->getName() : $owner;
		MySQL::updateIsland("owner", $m, $this->identifier);
		$this->owner = $m;
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
	 * @param string|Player $member
	 * @return void
	 */
	public function addMember(string|Player $member): void {
		$this->members[] = ($member instanceof Player ? $member->getName() : $member);
		MySQL::updateIsland("members", json_encode($this->members), $this->identifier);
	}

	/**
	 * @param string|Player $member
	 * @return void
	 */
	public function removeMember(string|Player $member): void {
		$this->members = array_diff($this->members, [($member instanceof Player ? $member->getName() : $member)]);
		MySQL::updatePlayer("island", "null", ($member instanceof Player ? $member->getName() : $member));
		MySQL::updateIsland("members", json_encode($this->members), $this->identifier);
	}

	public function kick(string $player, string $member, bool $keep_inventory = false, bool $keep_enderchest = false, bool $keep_experience = false, string $reason = "No reason specified", bool $connected = false): void {
		$this->removeMember($member);
		$date = date("Y-m-d H:i:s");

		$message = "Vous venez d'être expulsé de l'île §d{$this->getIslandName()}" . PHP_EOL;
		$message .= "§fPar: §d{$player} §fà: §d{$date}" . PHP_EOL;
		$message .= "§fRaison: §d{$reason}" . PHP_EOL . PHP_EOL;
		if ($keep_inventory) $message .= "§f- Votre §dinventaire §fa été conservé" . PHP_EOL;
		else $message .= "§f- Votre §dinventaire §fa été vidé" . PHP_EOL;
		if ($keep_enderchest) $message .= "§f- Votre §dcoffre du néant §fa été conservé" . PHP_EOL;
		else $message .= "§f- Votre §dcoffre du néant §fa été vidé" . PHP_EOL;
		if ($keep_experience) $message .= "§f- Votre §dexpérience §fa été conservé" . PHP_EOL;
		else $message .= "§f- Votre §dexpérience §fa été vidé" . PHP_EOL;

		$pwk = Server::getInstance()->getPlayerExact($member);
		if ($connected and $pwk instanceof Player) {
			$member = Server::getInstance()->getPlayerExact($member);
			if (!$keep_inventory) $member->getInventory()->clearAll();
			if (!$keep_enderchest) $member->getEnderInventory()->clearAll();
			if (!$keep_experience) {
				$member->getXpManager()->setXpLevel(0);
				$member->getXpManager()->setXpProgress(0);
			}

			$member->sendMessage(TextUtils::text($message));
			$member->teleport(WorldUtils::getDefaultWorldNonNull()->getSpawnLocation());
		} else MySQL::updatePlayer("last_kick", json_encode([
			"keep_inventory" => $keep_inventory,
			"keep_enderchest" => $keep_enderchest,
			"keep_experience" => $keep_experience,
			"message" => base64_encode($message),
		]), $member);
	}

	/**
	 * @param string|Player $player
	 * @return bool
	 */
	public function isMember(string|Player $player): bool {
		return in_array($player instanceof Player ? $player->getName() : $player, $this->members);
	}

	public function create(): void {
		$position = ["x" => $this->spawn->x, "y" => $this->spawn->y, "z" => $this->spawn->z];
		MySQL::mysqli()->query("INSERT INTO islands (uuid, island_name, owner, members, spawn) VALUES ('{$this->identifier}', '{$this->island_name}', '{$this->owner}', '" . json_encode($this->members) . "', '" . json_encode($position) . "')");
	}

	public function getWorld(): World {
		return WorldUtils::getWorldByNameNonNull($this->identifier);
	}

	public function getSpawn(): ?Position {
		return $this->spawn;
	}

	public function setSpawn(Position $position) {
		$this->getWorld()->setSpawnLocation($position->asVector3());
		$this->spawn = $position;
		MySQL::updateIsland("spawn", json_encode(["x" => $position->getX(), "y" => $position->getY(), "z" => $position->getZ()]), $this->getIdentifier());
	}
}