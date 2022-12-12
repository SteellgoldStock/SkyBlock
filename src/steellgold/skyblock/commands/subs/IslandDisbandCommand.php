<?php

namespace steellgold\skyblock\commands\subs;

use CortexPE\Commando\BaseSubCommand;
use dktapps\pmforms\ModalForm;
use Exception;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use steellgold\skyblock\player\SkyBlockIsland;
use steellgold\skyblock\player\SkyBlockPlayer;
use steellgold\skyblock\utils\database\MySQL;
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

		$sender->sendForm(self::disbandIslandForm($session, $session->getIsland()));
	}

	public static function disbandIslandForm(SkyBlockPlayer $player, SkyBlockIsland $island): ModalForm {
		return new ModalForm(
			TextUtils::FORM_TITLE_MODAL,
			"§d- §rVous êtes sur le point de supprimer votre île. Êtes-vous sûr de vouloir continuer? ceci est irréversible.",

			function (Player $submitter, bool $choice) use ($player, $island): void {
				if ($choice) {
					WorldUtils::lazyUnloadWorld($island->getIdentifier());
					WorldUtils::removeWorld($island->getIdentifier());
					$player->setIsland(null);

					MySQL::removeIsland($island->getIdentifier());
					$submitter->sendMessage(TextUtils::text("Vous venez de supprimer votre île avec succès!"));
					$submitter->teleport(WorldUtils::getDefaultWorldNonNull()->getSpawnLocation());
					$submitter->getInventory()->clearAll();
				} else {
					$submitter->sendMessage(TextUtils::error("Vous avez annulé la suppression de votre île."));
				}
			},
			"Oui",
			"§cAnnuler"
		);
	}
}