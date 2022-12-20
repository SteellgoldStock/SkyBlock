<?php

namespace steellgold\skyblock\player\roles;

class Member extends Role {

	public function getName(): string {
		return "Membre";
	}

	public function getPermissions(): array {
		return [
			self::PERMISSION_KICK_VISITOR,

			self::PERMISSION_OPEN_ALL_CHESTS,
			self::PERMISSION_BREAK_BLOCKS,
			self::PERMISSION_PLACE_BLOCKS
		];
	}
}