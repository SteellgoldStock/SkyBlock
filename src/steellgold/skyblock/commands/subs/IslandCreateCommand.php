<?php

namespace steellgold\skyblock\commands\subs;

use CortexPE\Commando\BaseSubCommand;
use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Input;
use dktapps\pmforms\element\Label;
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

					// $island = SkyBlockIsland::get($uuid, $submitter->getName(), $submitter->getName(), [$submitter->getName()]);
					$island = SkyBlockIsland::get($uuid);
					$player->setIsland($island);
					$submitter->sendMessage(TextUtils::text("Vous venez de créer votre île « §d" . $island->getIslandName() . " §f» avec succès!"));
					$submitter->sendForm(self::chooseIslandNameForm($submitter, $island));

					WorldUtils::duplicateWorld("copypaste", $island->getIdentifier());
					$world = WorldUtils::getLoadedWorldByName($island->getIdentifier());
					$world->setSpawnLocation(new Vector3(256, 71, 256));

					if (WorldUtils::lazyLoadWorld("copypaste")) {
						$submitter->teleport($world->getSpawnLocation());
					} else {
						$submitter->sendMessage(TextUtils::error("Une erreur est survenue lors du téléportation sur votre île."));
					}
				} else {
					$submitter->sendMessage(TextUtils::error("Vous avez annulé la création de votre île."));
				}
			},
			"Oui",
			"§cAnnuler"
		);
	}

	public static function chooseIslandNameForm(Player $player, SkyBlockIsland $island): CustomForm {
		return new CustomForm(
			TextUtils::FORM_TITLE, [
			new Label("description", "Choisissez un nom pour votre île, si vous ne le faites pas alors votre pseudo sera utilisé.\n\nVous pourrez très bien le changer à tout moment via la commande §d/is rename <nom>§r."),
			new Input("name", "Nom de l'île", "Super île de " . $player->getName())
		],
			function (Player $submitter, CustomFormResponse $data): void {
				$island = SkyBlockPlayer::get($submitter)->getIsland();
				$island->setIslandName($data->getString("name"));
				$submitter->sendMessage(TextUtils::text("Vous venez de renommer votre île en §d" . $island->getIslandName() . "§r."));
			},
			function (Player $submitter): void {
				$submitter->sendMessage(TextUtils::error("Vous avez annulé le choix du nom de votre île. Elle sera donc nommée §fIle de " . $submitter->getName() . "§r."));
			}
		);
	}
}