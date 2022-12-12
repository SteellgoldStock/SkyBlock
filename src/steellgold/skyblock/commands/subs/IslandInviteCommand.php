<?php

namespace steellgold\skyblock\commands\subs;

use CortexPE\Commando\args\TargetArgument;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use pocketmine\command\CommandSender;

class IslandInviteCommand extends BaseSubCommand {

	/** @throws ArgumentOrderException */
	protected function prepare(): void {
		$this->registerArgument(0, new TargetArgument("player",true));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
		// TODO: Implement onRun() method.
	}
}