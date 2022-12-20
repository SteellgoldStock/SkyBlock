<?php

namespace steellgold\skyblock\utils;

use FilesystemIterator;
use pocketmine\block\tile\Chest;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\Server;
use pocketmine\utils\AssumptionFailedError;
use pocketmine\utils\Config;
use pocketmine\world\format\Chunk;
use pocketmine\world\format\io\data\BaseNbtWorldData;
use pocketmine\world\World;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use steellgold\skyblock\player\SkyBlockIsland;
use steellgold\skyblock\SkyBlock;
use Webmozart\PathUtil\Path;

/**
 * From MultiWorld
 */
class WorldUtils {

	const SIDES = [
		0 => 3,
		1 => 2,
		2 => 4,
		3 => 5
	];

	const DEFAULT_COPY = "copypaste";

	public static function removeWorld(string $name): int {
		if (Server::getInstance()->getWorldManager()->isWorldLoaded($name)) {
			$world = WorldUtils::getWorldByNameNonNull($name);
			if (count($world->getPlayers()) > 0) {
				foreach ($world->getPlayers() as $player) {
					$player->teleport(WorldUtils::getDefaultWorldNonNull()->getSpawnLocation());
				}
			}

			Server::getInstance()->getWorldManager()->unloadWorld($world);
		}

		$removedFiles = 1;

		$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($worldPath = Server::getInstance()->getDataPath() . "/worlds/$name", FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
		/** @var SplFileInfo $fileInfo */
		foreach ($files as $fileInfo) {
			if ($filePath = $fileInfo->getRealPath()) {
				if ($fileInfo->isFile()) {
					unlink($filePath);
				} else {
					rmdir($filePath);
				}

				$removedFiles++;
			}
		}

		rmdir($worldPath);
		return $removedFiles;
	}

	/**
	 * WARNING: This method should be used only in the case, when it is assured,
	 * that the world is generated and loaded.
	 */
	public static function getWorldByNameNonNull(string $name): World {
		$world = Server::getInstance()->getWorldManager()->getWorldByName($name);
		if ($world === null) {
			throw new AssumptionFailedError("Required world $name is null");
		}

		return $world;
	}

	public static function getDefaultWorldNonNull(): World {
		$world = Server::getInstance()->getWorldManager()->getDefaultWorld();
		if ($world === null) {
			throw new AssumptionFailedError("Default world is null");
		}

		return $world;
	}

	public static function renameWorld(string $oldName, string $newName): void {
		WorldUtils::lazyUnloadWorld($oldName, true);

		$from = Server::getInstance()->getDataPath() . "/worlds/" . $oldName;
		$to = Server::getInstance()->getDataPath() . "/worlds/" . $newName;

		rename($from, $to);

		WorldUtils::lazyLoadWorld($newName);
		$newWorld = Server::getInstance()->getWorldManager()->getWorldByName($newName);
		if (!$newWorld instanceof World) {
			return;
		}

		$worldData = $newWorld->getProvider()->getWorldData();
		if (!$worldData instanceof BaseNbtWorldData) {
			return;
		}

		$worldData->getCompoundTag()->setString("LevelName", $newName);

		Server::getInstance()->getWorldManager()->unloadWorld($newWorld); // reloading the world
		WorldUtils::lazyLoadWorld($newName);
	}

	public static function duplicateWorld(string $worldName, string $duplicateName): void {
		if (Server::getInstance()->getWorldManager()->isWorldLoaded($worldName)) {
			WorldUtils::getWorldByNameNonNull($worldName)->save();
		}

		mkdir(Server::getInstance()->getDataPath() . "/worlds/$duplicateName");

		$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(Server::getInstance()->getDataPath() . "/worlds/$worldName", FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST);
		/** @var SplFileInfo $fileInfo */
		foreach ($files as $fileInfo) {
			if ($filePath = $fileInfo->getRealPath()) {
				if ($fileInfo->isFile()) {
					@copy($filePath, str_replace($worldName, $duplicateName, $filePath));
				} else {
					mkdir(str_replace($worldName, $duplicateName, $filePath));
				}
			}
		}
	}

	/**
	 * @return bool Returns if the world was unloaded with the function.
	 * If it has already been unloaded before calling this function, returns FALSE!
	 */
	public static function lazyUnloadWorld(string $name, bool $force = false): bool {
		if (($world = Server::getInstance()->getWorldManager()->getWorldByName($name)) !== null) {
			return Server::getInstance()->getWorldManager()->unloadWorld($world, $force);
		}
		return false;
	}

	/**
	 * @return bool Returns if the world was loaded with the function.
	 * If it has already been loaded before calling this function, returns FALSE!
	 */
	public static function lazyLoadWorld(string $name): bool {
		return !Server::getInstance()->getWorldManager()->isWorldLoaded($name) && Server::getInstance()->getWorldManager()->loadWorld($name, true);
	}

	/**
	 * @return string[] Returns all the levels on the server including
	 * unloaded ones
	 */
	public static function getAllWorlds(): array {
		$files = scandir(Server::getInstance()->getDataPath() . "/worlds/");
		if (!$files) {
			return [];
		}

		// This is not necessary in case only clean PocketMine without other plugins is used,
		// however, due to compatibility with plugins such as NativeDimensions it's needed to keep this.
		$files = array_unique(array_merge(
			array_map(fn(World $world) => $world->getFolderName(), Server::getInstance()->getWorldManager()->getWorlds()),
			$files
		));

		return array_values(array_filter($files, function (string $fileName): bool {
			return Server::getInstance()->getWorldManager()->isWorldGenerated($fileName) &&
				$fileName !== "." && $fileName !== ".."; // Server->isWorldGenerated detects '.' and '..' as world, TODO - make pull request
		}));
	}

	/**
	 * @return World|null Loads and returns world, if it is generated.
	 */
	public static function getLoadedWorldByName(string $name): ?World {
		WorldUtils::lazyLoadWorld($name);

		return Server::getInstance()->getWorldManager()->getWorldByName($name);
	}

	/**
	 * Not come from multiworld
	 */
	public static function isWorldExist(string $name): bool {
		return is_dir(Path::join(Server::getInstance()->getDataPath(), "worlds", $name));
	}

	public static function placeChest(World $world, SkyBlockIsland $island): void {
		$chest_config = new Config(SkyBlock::getInstance()->getDataFolder() . "chest.json", Config::JSON);
		$positions = $chest_config->get("position");

		$world->orderChunkPopulation($positions["x"] >> 4, $positions["z"] >> 4, null)->onCompletion(function (Chunk $chunk) use ($world, $chest_config, $positions): void {
			$world->setBlockAt($positions["x"], $positions["y"], $positions["z"], VanillaBlocks::CHEST()->setFacing($positions["side"]));
			$tile = $world->getTileAt($positions["x"], $positions["y"], $positions["z"]);

			if ($tile instanceof Chest) {
				$items = json_decode(base64_decode($chest_config->get("content")), true);

				$i = 0;
				foreach ($items as $item) {
					$tile->getInventory()->setItem($i, Item::jsonDeserialize($item));
					$i++;
				}
			}
		}, static fn() => null);
	}
}