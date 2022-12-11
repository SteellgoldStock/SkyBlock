<?php

namespace steellgold\skyblock;

use CortexPE\Commando\exception\HookAlreadyRegistered;
use CortexPE\Commando\PacketHooker;
use mysqli;
use pocketmine\plugin\PluginBase;
use steellgold\skyblock\commands\IslandCommand;
use steellgold\skyblock\listeners\PlayerListeners;
use steellgold\skyblock\utils\database\MySQL;

class SkyBlock extends PluginBase {

	public mysqli $mysql;

	public static SkyBlock $instance;

	/**
	 * @throws HookAlreadyRegistered
	 */
	protected function onEnable(): void {
		self::$instance = $this;

		if (!PacketHooker::isRegistered()) {
			PacketHooker::register($this);
		}

		$this->saveResource("config.yml");
		$this->mysql = MySQL::mysqli();
		MySQL::default($this->mysql);

		$this->getServer()->getCommandMap()->register("skyblock", new IslandCommand($this, "island", "Commande principale du SkyBlock", ["is"]));
		$this->getServer()->getPluginManager()->registerEvents(new PlayerListeners(), $this);
	}

	/** @return SkyBlock */
	public static function getInstance(): SkyBlock {
		return self::$instance;
	}

	/** @return mysqli */
	public function getMySQL(): mysqli {
		return $this->mysql;
	}
}