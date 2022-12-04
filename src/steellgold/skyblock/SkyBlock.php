<?php

namespace steellgold\skyblock;

use CortexPE\Commando\exception\HookAlreadyRegistered;
use CortexPE\Commando\PacketHooker;
use pocketmine\plugin\PluginBase;
use steellgold\skyblock\commands\IslandCommand;
use steellgold\skyblock\listeners\PlayerListeners;

class SkyBlock extends PluginBase {

	/**
	 * @throws HookAlreadyRegistered
	 */
	protected function onEnable(): void {
		if(!PacketHooker::isRegistered()) {
			PacketHooker::register($this);
		}

		$this->getServer()->getCommandMap()->register("skyblock", new IslandCommand($this, "island","Commande principale du SkyBlock", ["is"]));
		$this->getServer()->getPluginManager()->registerEvents(new PlayerListeners(), $this);
	}

	protected function onDisable(): void {

	}
}