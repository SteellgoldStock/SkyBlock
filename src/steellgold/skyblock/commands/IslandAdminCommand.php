<?php

namespace steellgold\skyblock\commands;

use CortexPE\Commando\BaseCommand;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use steellgold\skyblock\commands\subs\IslandStartChestCommand;
use steellgold\skyblock\utils\TextUtils;

class IslandAdminCommand extends BaseCommand {

	protected function prepare(): void {
		$this->registerSubCommand(new IslandStartChestCommand("chest","Edit the coords, or content of start chest"));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
		if (!$sender instanceof Player) return;
		if (!Server::getInstance()->isOp($sender->getName())){
			$sender->sendMessage(TextUtils::error("Vous devez être opérateur pour utiliser cette commande"));
			return;
		}
	}
}