<?php

namespace steellgold\skyblock\commands\subs;

use CortexPE\Commando\BaseSubCommand;
use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Input;
use dktapps\pmforms\element\Label;
use Exception;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use steellgold\skyblock\player\roles\Chief;
use steellgold\skyblock\player\roles\Role;
use steellgold\skyblock\player\SkyBlockPlayer;
use steellgold\skyblock\SkyBlock;
use steellgold\skyblock\utils\TextUtils;

class IslandRenameCommand extends BaseSubCommand {

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
			$sender->sendMessage(TextUtils::error("Vous n'avez pas d'île, créez une avant de pouvoir la renommer."));
			return;
		}

		if (!$session->getRole()->hasPermission(Role::PERMISSION_RENAME)) {
			$sender->sendMessage(TextUtils::error(TextUtils::getNoPermissionMessage(Chief::class)));
			return;
		}

		$sender->sendForm(new CustomForm(
			TextUtils::FORM_TITLE, [
			new Label("label", "§d- §rEntrez le nouveau nom de votre île."),
			new Input("name", "Nouveau nom d'île", str_replace("{player}", $sender->getName(), SkyBlock::getInstance()->getConfig()->get("island")["default_name"]))
		],
			function (Player $player, CustomFormResponse $response): void {
				if ($response->getString("name") === "") {
					$player->sendMessage(TextUtils::error("Vous devez écrire quelque chose."));
					return;
				}

				$new_name = str_replace(["'"], ["''"], $response->getString("name"));

				$session = SkyBlockPlayer::get($player);
				$session->getIsland()->setIslandName($new_name);
				$player->sendMessage(TextUtils::text("Vous venez de renommer votre île en « §d{$response->getString("name")} §f» avec succès!"));
			},
			function (Player $player): void {
				$player->sendMessage(TextUtils::error("Vous avez annulé la modification du nom de votre île."));
			}
		));
	}
}