<?php

namespace steellgold\skyblock\commands\subs;

use CortexPE\Commando\args\TargetArgument;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Dropdown;
use dktapps\pmforms\element\Input;
use dktapps\pmforms\element\Label;
use Exception;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use steellgold\skyblock\player\SkyBlockPlayer;
use steellgold\skyblock\utils\TextUtils;

class IslandInviteCommand extends BaseSubCommand {

	/**
	 * @throws ArgumentOrderException
	 */
	protected function prepare(): void {
		$this->registerArgument(0, new TargetArgument("player", true));
	}

	/**
	 * @throws Exception
	 */
	public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
		if (!$sender instanceof Player) return;
		$session = SkyBlockPlayer::get($sender);

		if (!$session->hasIsland()) {
			$sender->sendMessage(TextUtils::error("Vous n'avez pas d'île, créez une avant de pouvoir inviter des personnes."));
			return;
		}

		if (!isset($args["player"])) $this->invitePlayerForm();
		else $this->invitePlayerForm($session, $args["player"]);
	}

	public function invitePlayerForm(?string $option) : CustomForm {
		$players = [];
		foreach (Server::getInstance()->getOnlinePlayers() as $onlinePlayer) {
			$players[] = $onlinePlayer->getName();
		}

		// If option was not set, define a dropdown with all online players, else define a label with the player name
		$element = $option === null ? new Dropdown("player", "Choisissez un joueur à inviter", $players) : new Input("player", "Pseudonyme du joueur à inviter" . $option);
	}
}