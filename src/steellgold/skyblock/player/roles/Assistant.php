<?php

namespace steellgold\skyblock\player\roles;

class Assistant extends Role {

	const ASSISTANT = "Assistant";

	public function getName(): string {
		return "Assistant";
	}

	public function getPermissions(): array {
		return [
			self::PERMISSION_KICK_MEMBER,
			self::PERMISSION_KICK_VISITOR,

			self::PERMISSION_OPEN_ALL_CHESTS,
			self::PERMISSION_BREAK_BLOCKS,
			self::PERMISSION_PLACE_BLOCKS
		];
	}

	public function getIdentifier(): string {
		return "Assistant";
	}
}