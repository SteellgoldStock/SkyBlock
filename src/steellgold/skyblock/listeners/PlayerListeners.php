<?php

namespace steellgold\skyblock\listeners;

use Exception;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use steellgold\skyblock\player\SkyBlockPlayer;

class PlayerListeners implements Listener {

	/** @throws Exception */
	public function onPlayerJoin(PlayerJoinEvent $event) {
		$player = $event->getPlayer();
		$session = SkyBlockPlayer::get($player);

		// TODO: Check if player has an island
		//       if island exists always teleport player to island
		//       if island doesn't exist teleport player to spawn and clear inventory
		//		 and notify player of island was deleted by owner
	}

	/** @throws Exception */
	public function onPlayerQuit(PlayerQuitEvent $event) {
		$player = $event->getPlayer();
		$session = SkyBlockPlayer::get($player);
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
			$player->sendTip("§aX: §b$toX §aY: §b$toY §aZ: §b$toZ");
		}
	}
}