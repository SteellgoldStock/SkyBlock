<?php

namespace steellgold\skyblock\commands\subs\admin;

use CortexPE\Commando\BaseSubCommand;use pocketmine\command\CommandSender;use pocketmine\player\Player;use pocketmine\Server;use steellgold\skyblock\utils\TextUtils;use steellgold\skyblock\utils\WorldUtils;

class IslandAdminDefaultTeleportCommand extends BaseSubCommand {

	protected function prepare(): void {
		// TODO: Implement prepare() method.
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
		if(!$sender instanceof Player) return;
		if (!Server::getInstance()->isOp($sender->getName())) {
			$sender->sendMessage(TextUtils::error("Vous devez être opérateur pour utiliser cette commande"));
			return;
		}

		if (WorldUtils::lazyLoadWorld(WorldUtils::DEFAULT_COPY)) {
			$sender->teleport(WorldUtils::getWorldByNameNonNull(WorldUtils::DEFAULT_COPY)->getSpawnLocation());
			$sender->sendMessage(TextUtils::text("Vous venez d'être téléporter au monde de l'île de base"));
		}
	}
}