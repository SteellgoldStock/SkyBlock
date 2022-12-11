<?php

namespace steellgold\skyblock\commands\subs;

use CortexPE\Commando\BaseSubCommand;
use dktapps\pmforms\ModalForm;
use Exception;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use steellgold\skyblock\player\SkyBlockPlayer;
use steellgold\skyblock\utils\TextUtils;
use steellgold\skyblock\utils\WorldUtils;

class IslandDisbandCommand extends BaseSubCommand {

	protected function prepare(): void {
		// TODO: Implement prepare() method.
	}

	/**
	 * @throws Exception
	 */
	public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
		if (!$sender instanceof Player) return;
		$session = SkyBlockPlayer::get($sender);

		if (!$session->hasIsland()) {
			$sender->sendMessage(TextUtils::error("Vous n'avez pas d'île, créez une avant de pouvoir la supprimer."));
			return;
		}

		$sender->sendForm(self::disbandIslandForm($session));
	}

	public static function disbandIslandForm(SkyBlockPlayer $player): ModalForm {
		return new ModalForm(
			TextUtils::FORM_TITLE_MODAL,
			"§d- §rVous êtes sur le point de supprimer votre île. Êtes-vous sûr de vouloir continuer? ceci est irréversible.",

			function (Player $submitter, bool $choice) use ($player): void {
				if ($choice) {
					$world = WorldUtils::getLoadedWorldByName($submitter->getXuid());
					WorldUtils::lazyUnloadWorld($world);
					WorldUtils::removeWorld($submitter->getXuid());

					$player->setIsland(null);
					$submitter->sendMessage(TextUtils::text("Vous venez de supprimer votre île avec succès!"));
				} else {
					$submitter->sendMessage(TextUtils::error("Vous avez annulé la suppression de votre île."));
				}
			},
			"Oui",
			"§cAnnuler"
		);
	}
}