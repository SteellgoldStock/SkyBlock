<?php

namespace steellgold\skyblock\commands\subs;
use CortexPE\Commando\args\TargetArgument;
use CortexPE\Commando\BaseSubCommand;
use steellgold\skyblock\player\SkyBlockIsland;
class IslandKickCommand extends BaseSubCommand {
	protected function prepare(): void {
		$this->registerArgument(0, new TargetArgument("player", true));
	}
	public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
	}
	public function kickPlayerForm(?string $option, SkyBlockIsland $island): CustomForm {
}