<?php

namespace steellgold\skyblock\commands;

use CortexPE\Commando\BaseCommand;
use pocketmine\command\CommandSender;
use steellgold\skyblock\commands\subs\IslandCreateCommand;

class IslandCommand extends BaseCommand {

	protected function prepare(): void {
		$this->registerSubCommand(new IslandCreateCommand("create", "Create a new island"));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
		$this->sendUsage();
	}
}