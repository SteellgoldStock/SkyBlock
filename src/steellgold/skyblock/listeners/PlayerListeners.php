<?php

namespace steellgold\skyblock\listeners;

use Exception;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\player\Player;
use pocketmine\Server;
use steellgold\skyblock\player\SkyBlockPlayer;
use steellgold\skyblock\utils\TextUtils;

class PlayerListeners implements Listener {

	/** @throws Exception */
	public function onPlayerJoin(PlayerJoinEvent $event) {
		$player = $event->getPlayer();
		$session = SkyBlockPlayer::get($player);

		foreach ($session->getIsland()->getMembers() as $member) {
			$connected = Server::getInstance()->getPlayerExact($member);
			if ($connected instanceof Player) {
				$connected->sendMessage(TextUtils::PREFIX . "Le membre §d" . $player->getName() . " §f(§d" . $session->getRole()->getName() . "§f) c'est connecté");
			}
		}
	}

	/** @throws Exception */
	public function onPlayerQuit(PlayerQuitEvent $event) {
		// Path: src\steellgold\skyblock\listeners\PlayerListeners.php
	}

	public function onPlayerMove(PlayerMoveEvent $event) {
		$player = $event->getPlayer();
		$from = $event->getFrom();
		$to = $event->getTo();
		$fromX = $from->getX();
		$fromY = $from->getY();
		$fromZ = $from->getZ();
		$toX = $to->getX();
		$toY = $to->getY();
		$toZ = $to->getZ();

		if ($fromX !== $toX || $fromY !== $toY || $fromZ !== $toZ) {
			$player->sendTip("§dX: §f$toX §dY: §f$toY §dZ: §f$toZ");
		}
	}
}