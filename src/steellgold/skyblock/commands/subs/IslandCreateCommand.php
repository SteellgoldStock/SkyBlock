<?php

namespace steellgold\skyblock\commands\subs;

use CortexPE\Commando\BaseSubCommand;
use dktapps\pmforms\ModalForm;
use Exception;
use pocketmine\command\CommandSender;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use steellgold\skyblock\player\SkyBlockIsland;
use steellgold\skyblock\player\SkyBlockPlayer;
use steellgold\skyblock\utils\TextUtils;
use steellgold\skyblock\utils\UUID;
use steellgold\skyblock\utils\WorldUtils;

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

	public static function createIslandForm(SkyBlockPlayer $player): ModalForm {
		return new ModalForm(
			TextUtils::FORM_TITLE_MODAL,
			"§d- §rVous êtes sur le point de créer une île. Êtes-vous sûr de vouloir continuer?",

			function (Player $submitter, bool $choice) use ($player): void {
				if ($choice) {
					$uuid = (new UUID())->generate();

					$island = new SkyBlockIsland($uuid, $submitter->getName(), $submitter->getName(), [$submitter->getName()]);
					$island->create();
					SkyBlockIsland::referenceIsland($island);

					$player->setIsland($island);
					$submitter->sendMessage(TextUtils::text("Vous venez de créer votre île « §d" . $island->getIslandName() . " §f» avec succès!"));

					WorldUtils::duplicateWorld("copypaste", $island->getIdentifier());
					WorldUtils::renameWorld($island->getIdentifier(), $island->getIdentifier());
					$world = WorldUtils::getLoadedWorldByName($island->getIdentifier());
					$world->setSpawnLocation(new Vector3(256, 71, 256));
					$submitter->teleport($world->getSpawnLocation());
				} else {
					$submitter->sendMessage(TextUtils::error("Vous avez annulé la création de votre île."));
				}
			},
			"Oui",
			"§cAnnuler"
		);
	}
}