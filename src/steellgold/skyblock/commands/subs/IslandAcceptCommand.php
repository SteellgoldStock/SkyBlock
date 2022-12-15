<?php

namespace steellgold\skyblock\commands\subs;

use CortexPE\Commando\BaseSubCommand;
use dktapps\pmforms\FormIcon;
use dktapps\pmforms\MenuForm;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use steellgold\skyblock\player\SkyBlockPlayer;
use steellgold\skyblock\utils\Invites;
use steellgold\skyblock\utils\TextUtils;

class IslandAcceptCommand extends BaseSubCommand {

	protected function prepare(): void {
		// TODO: Implement prepare() method.
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
		if (!$sender instanceof Player) return;

		$session = SkyBlockPlayer::get($sender);
		if ($session->hasIsland()) {
			$sender->sendMessage(TextUtils::error("Vous avez déjà une île, supprimez-la d'abord avant de pouvoir accepter un invitation."));
			return;
		}

		$invite = Invites::getInvite($sender->getName());
		if ($invite === null) {
			$sender->sendMessage(TextUtils::error("Vous n'avez reçu aucune invitations jusqu'à présent."));
			return;
		}

		$island = Invites::getInviteIsland($sender->getName());

		$session->setIsland($island);
		$island->addMember($sender->getName());
		// TODO: Add role
		$sender->sendMessage(TextUtils::text("texte bienvenue.txt"));
	}
}