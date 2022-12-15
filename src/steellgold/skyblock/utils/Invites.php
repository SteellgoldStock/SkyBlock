<?php

namespace steellgold\skyblock\utils;

use steellgold\skyblock\player\SkyBlockIsland;

class Invites {

	public static array $invites = [];

	/**
	 * @param string $player
	 * @param string $inviter
	 * @param SkyBlockIsland $island
	 * @return void
	 */
	public static function addInvite(string $player, string $inviter, SkyBlockIsland $island): void {
		self::$invites[$player] = [
			"inviter" => $inviter,
			"expire_at" => time() + 60,
			"island" => $island
		];
	}

	public static function removeInvite(string $player): void {
		unset(self::$invites[$player]);
	}

	public static function isInvited(string $player): bool {
		return isset(self::$invites[$player]);
	}

	public static function getInvite(string $player): ?array {
		return self::$invites[$player] ?? null;
	}

	public static function getInviteIsland(string $player): ?SkyBlockIsland {
		return self::$invites[$player]["island"] ?? null;
	}

	public static function getInvites(): array {
		return self::$invites;
	}
}