<?php

namespace steellgold\skyblock\commands\subs;

use CortexPE\Commando\BaseSubCommand;
use dktapps\pmforms\ModalForm;
use Exception;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use steellgold\skyblock\player\SkyBlockIsland;
use steellgold\skyblock\player\SkyBlockPlayer;
use steellgold\skyblock\utils\TextUtils;

class IslandCreateCommand extends BaseSubCommand {

	protected function prepare(): void {
		// TODO: Implement prepare() method.
	}

	/**
	 * @throws Exception
	 */
	public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
		if (!$sender instanceof Player) return;
		$session = SkyBlockPlayer::get($sender);

		if ($session->hasIsland()) {
			$sender->sendMessage(TextUtils::error("Vous avez déjà une île, supprimez-la ou transférez-la à un autre joueur, avant de pouvoir en créer une nouvelle."));
			return;
		}

		$sender->sendForm(self::createIslandForm($session));
	}

	public static function createIslandForm(SkyBlockPlayer $player) : ModalForm {
		return new ModalForm(
			TextUtils::FORM_TITLE_MODAL,
			"§d- §rVous êtes sur le point de créer une île. Êtes-vous sûr de vouloir continuer?",

			function(Player $submitter, bool $choice) use ($player) : void{
				if ($choice) {
					$player->setIsland(new SkyBlockIsland($submitter->getXuid(), $submitter->getName(), [$submitter->getName()]));
					$submitter->sendMessage(TextUtils::text("Vous venez de créer votre île avec succès!"));
					// TODO: Create island world
					// TODO: Create island instance
					// TODO: Teleport player to island
				} else {
					$submitter->sendMessage(TextUtils::error("Vous avez annulé la création de votre île."));
				}
			},
			"Oui",
			"§cAnnuler"
		);
	}
}