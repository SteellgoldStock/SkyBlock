<?php

namespace steellgold\skyblock;

use CortexPE\Commando\exception\HookAlreadyRegistered;
use CortexPE\Commando\PacketHooker;
use muqsit\invmenu\InvMenuHandler;
use mysqli;
use pocketmine\plugin\PluginBase;
use steellgold\skyblock\commands\IslandAdminCommand;
use steellgold\skyblock\commands\IslandCommand;
use steellgold\skyblock\listeners\PlayerListeners;
use steellgold\skyblock\utils\database\MySQL;

class SkyBlock extends PluginBase {

	public static SkyBlock $instance;

	/**
	 * @throws HookAlreadyRegistered
	 */
	protected function onEnable(): void {
		self::$instance = $this;

		if (!PacketHooker::isRegistered()) {
			PacketHooker::register($this);
		}

		if(!InvMenuHandler::isRegistered()){
			InvMenuHandler::register($this);
		}

		$this->saveResource("config.yml");
		$this->saveResource("chest.json");
		new MySQL();

		$this->getServer()->getCommandMap()->registerAll("skyblock", [
			new IslandCommand($this, "island", "Commande principale du SkyBlock", ["is"]),
			new IslandAdminCommand($this,"islandadmin","Commande de gestion SkyBlock", ["isa"])
		]);
		$this->getServer()->getPluginManager()->registerEvents(new PlayerListeners(), $this);
	}

	/** @return SkyBlock */
	public static function getInstance(): SkyBlock {
		return self::$instance;
	}
}