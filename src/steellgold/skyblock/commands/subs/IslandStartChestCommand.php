<?php

namespace steellgold\skyblock\commands\subs;

use CortexPE\Commando\BaseSubCommand;
use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Input;
use dktapps\pmforms\element\Label;
use dktapps\pmforms\element\Slider;
use dktapps\pmforms\FormIcon;
use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\command\CommandSender;
use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\Config;
use steellgold\skyblock\SkyBlock;
use steellgold\skyblock\utils\TextUtils;

class IslandStartChestCommand extends BaseSubCommand {

	protected function prepare(): void {
		// TODO: Implement prepare() method.
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
		if (!$sender instanceof Player) return;
		if (!Server::getInstance()->isOp($sender->getName())) {
			$sender->sendMessage(TextUtils::error("Vous devez être opérateur pour utiliser cette commande"));
			return;
		}

		$sender->sendForm($this->openConfigForm());
	}

	public function openConfigForm(): MenuForm {
		return new MenuForm(
			"Configuration",
			"Vous êtes actuellement sur l'interface de configuration du coffre de départ qui apparait lors de la création d'une île\n§cInterfaces factices", [
			new MenuOption("Ouvrir (lecture-seule)", new FormIcon("textures/items/book_normal", FormIcon::IMAGE_TYPE_PATH)),
			new MenuOption("Ouvrir (lecture-écriture)", new FormIcon("textures/items/book_writable", FormIcon::IMAGE_TYPE_PATH)),
			new MenuOption("Modifier les coordonées d'apparition", new FormIcon("textures/items/compass_item", FormIcon::IMAGE_TYPE_PATH))
		],
			function (Player $player, int $selectedOption): void {
				$menu = InvMenu::create(InvMenuTypeIds::TYPE_CHEST)->setName("Coffre de départ");
				$menu->getInventory()->setContents([
					VanillaItems::APPLE()->setCount(64),
					VanillaItems::DIAMOND()->setCount(64)
				]);


				$chest_config = new Config(SkyBlock::getInstance()->getDataFolder() . "chest.json", Config::JSON);
				switch ($selectedOption) {
					case 0:
						$menu->setListener(InvMenu::readonly());
						$menu->send($player);
						break;
					case 1:
						$menu->setInventoryCloseListener(function (Player $player, Inventory $inventory) use ($chest_config): void {
							$content = $inventory->getContents(true);
							$chest_config->set("content", base64_encode(json_encode($content)));
							$chest_config->save();

							$player->sendMessage(TextUtils::text("L'inventaire a été sauvegardé, créer une île pour voir les changements"));
						});
						$menu->send($player);
						break;
					case 2:
						$player->sendForm($this->openCoordsConfigForm());
						break;
				}
			}
		);
	}

	public function openCoordsConfigForm(): CustomForm {
		return new CustomForm(
			"Configuration - Coordonées", [
			new Label("label", "Vous pouvez modifier les coordonées du §dpoint d'apparition §fpar défaut du coffre de départ"),
			new Slider("x", "X", -256, 256),
			new Slider("y", "Y", 0, 256),
			new Slider("z", "Z", -256, 256),
		], function (Player $player, CustomFormResponse $response): void {
			$chest_config = new Config(SkyBlock::getInstance()->getDataFolder() . "chest.json", Config::JSON);
			$chest_config->set("position", [
				"x" => $response->getFloat("x"),
				"y" => $response->getFloat("y"),
				"z" => $response->getFloat("z")
			]);
			$chest_config->save();

			$player->sendMessage(TextUtils::text("Les coordonées ont été sauvegardé, créer une île pour voir le nouveau point d'apparition"));
		});
	}
}