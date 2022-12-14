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

		$sender->sendForm($this->invitePlayerForm($args["player"] ?? null));
	}

	public function invitePlayerForm(?string $option): CustomForm {
		$players = [];
		$i = 0;
		$f = 0;
		foreach (Server::getInstance()->getOnlinePlayers() as $onlinePlayer) {
			$players[] = $onlinePlayer->getName();
			if ($onlinePlayer->getName() === $option) $f = $i;
			$i++;
		}

		return new CustomForm("Inviter un joueur", [
			new Label("info", "Choisissez un joueur à inviter, il devra accepter votre invitation pour rejoindre votre île. \n§d» §fElle sera automatiquement refusée si le joueur ce déconnecte, ou n'est pas acceptée dans la minute qui viens."),
			new Dropdown("player", "Choisissez un joueur à inviter", $players, $f),
		], function (Player $player, CustomFormResponse $response) use ($option): void {
			var_dump($response);
		}, function (Player $player) use ($option): void {
			$player->sendMessage(TextUtils::error("Vous avez annulé l'invitation."));
		});
	}
}