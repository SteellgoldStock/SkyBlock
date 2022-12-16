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
use dktapps\pmforms\element\Toggle;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use steellgold\skyblock\player\SkyBlockIsland;
use steellgold\skyblock\player\SkyBlockPlayer;
use steellgold\skyblock\utils\Invites;
use steellgold\skyblock\utils\TextUtils;

class IslandKickCommand extends BaseSubCommand {

	/**
	 * @throws ArgumentOrderException
	 */
	protected function prepare(): void {
		$this->registerArgument(0, new TargetArgument("player", true));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
		if (!$sender instanceof Player) return;
		$session = SkyBlockPlayer::get($sender);

		if (!$session->hasIsland()) {
			$sender->sendMessage(TextUtils::error("Vous n'avez pas d'île, créez une avant de pouvoir d'expulser des personnes."));
			return;
		}

		if (isset($args["player"])) {
			if (IslandInviteCommand::checkIfSame($sender, $args["player"])){
				$sender->sendMessage(TextUtils::error("Vous ne pouvez pas vous inviter vous-même."));
				return;
			}
		}

		$guest = $args["player"] ?? null;
		if ($guest !== null) {
			if ($session->getIsland()->isMember($guest) === null) {
				$sender->sendMessage(TextUtils::error("Ce joueur n'est pas sur votre île."));
				return;
			}
		}

		$sender->sendForm($this->kickPlayerForm($guest, $session->getIsland()));
	}

	public function kickPlayerForm(?string $option, SkyBlockIsland $island): CustomForm {
		$players = [];
		$i = 0;
		$f = 0;
		foreach ($island->getMembers() as $onlinePlayer) {
			$players[] = $onlinePlayer;
			if ($onlinePlayer === $option) $f = $i;
			$i++;
		}

		return new CustomForm("Expulser un joueur", [
			new Label("info", "Choisissez un joueur à expulser, il pourra revenir dans votre île §davec une invitation§f."),
			new Dropdown("player", "Choisissez un joueur à expulser", $players, $f),
			new Toggle("keep_inventory", "Il gardera son inventaire", false),
			new Toggle("keep_enderchest", "Il gardera son enderchest", false),
			new Toggle("keep_experience", "Il gardera son expérience et ses niveaux", false),
			new Input("reason", "Précisez une §draison §fde l'expulsion", "Il n'a pas goutté à mes pommes."),
			new Toggle("ban", "§dBannir §fle joueur de l'île? (Il ne pourra pas la visiter)", false),
			new Toggle("confirm", "Je confirme vouloir §dexpulser ce joueur", false)
		], function (Player $player, CustomFormResponse $response) use ($option, $players, $island): void {
			if (!$response->getBool("confirm")) {
				$player->sendMessage(TextUtils::error("Vous n'avez pas confirmé l'expulsion du joueur."));
				return;
			}

			$playerName = $players[$response->getInt("player")];
			$guest = Server::getInstance()->getPlayerExact($playerName);
			if (!$guest instanceof Player) {
				$connected = false;
				$player->sendMessage(TextUtils::error("Le joueur n'est pas connecté, il sera donc averti par message lors de sa prochaine connexion."));
			} else $connected = true;

			$island->kick(
				$player->getName(),
				$playerName,
				$response->getBool("keep_inventory"),
				$response->getBool("keep_enderchest"),
				$response->getBool("keep_experience"),
				strlen($response->getString("reason")) > 0 ? $response->getString("reason") : "Aucune raison n'a été précisée.",
				$connected
			);

			$player->sendMessage(TextUtils::text("Vous avez bien expulsé §d$playerName §fde l'île."));
		}, function (Player $player) use ($option): void {
			$player->sendMessage(TextUtils::error("Vous avez annulé l'expulsion."));
		});
	}
}