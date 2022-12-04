<?php

namespace steellgold\skyblock\commands;

use CortexPE\Commando\BaseCommand;
use Exception;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use steellgold\skyblock\commands\subs\IslandCreateCommand;
use steellgold\skyblock\player\SkyBlockPlayer;

class IslandCommand extends BaseCommand {

	protected function prepare(): void {
		$this->registerSubCommand(new IslandCreateCommand("create", "Create a new island"));
	}

	/**
	 * @throws Exception
	 */
	public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
		if (!$sender instanceof Player) return;
		$session = SkyBlockPlayer::get($sender);
		if (!$session->hasIsland()) {

		}
	}
}