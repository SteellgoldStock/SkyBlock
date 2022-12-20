<?php

namespace steellgold\skyblock\player;

class Officier extends Role {

	public function getName(): string {
		return "Officier";
	}

	public function getPermissions(): array {
		return [
			self::PERMISSION_OPEN_ALL_CHESTS,
			self::PERMISSION_BREAK_BLOCKS,
			self::PERMISSION_PLACE_BLOCKS,

			self::PERMISSION_BAN,
			self::PERMISSION_KICK_ASSISTANT,
			self::PERMISSION_KICK_MEMBER,
			self::PERMISSION_KICK_VISITOR
		];
	}
}