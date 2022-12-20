<?php

namespace steellgold\skyblock\player\roles;

abstract class Role {

	const ROLES = [
		0 => Chief::class,
		1 => SubChief::class,
		2 => Assistant::class,
		3 => Member::class,
		4 => Visitor::class
	];

	const PERMISSION_KICK = "skyblock.command.kick_all"; // It can exclude any person of any rank
	const PERMISSION_KICK_VISITOR = "skyblock.command.kick_visitors";
	const PERMISSION_KICK_MEMBER = "skyblock.command.kick_members";
	const PERMISSION_KICK_ASSISTANT = "skyblock.command.kick_assistants";
	const PERMISSION_KICK_OFFICIER = "skyblock.command.kick_officiers";

	const PERMISSION_BAN = "skyblock.command.ban";
	const PERMISSION_DISBAND = "skyblock.command.disband";

	const PERMISSION_RENAME = "skyblock.forms.rename";
	const PERMISSION_SETTINGS = "skyblock.forms.settings";

	const PERMISSION_BREAK_BLOCKS = "skyblock.access.breakblocks";
	const PERMISSION_PLACE_BLOCKS = "skyblock.access.placeblocks";
	const PERMISSION_OPEN_ALL_CHESTS = "skyblock.access.openallchests";

	public static function getClass($class = null): Visitor|Member|SubChief|Assistant|Chief|null {
		if ($class === null) return null;
		return match ($class) {
			Chief::class => new Chief(),
			SubChief::class => new SubChief(),
			Assistant::class => new Assistant(),
			Member::class => new Member(),
			default => new Visitor()
		};
	}

	abstract public function getName(): string;

	abstract public function getIdentifier(): string;

	abstract public function getPermissions(): array;

	public function hasPermission(string $permission): bool {
		return in_array("*", $this->getPermissions()) || in_array($permission, $this->getPermissions());
	}

	public static function toRole(string $role): Role {
		return match ($role) {
			"Chief" => new Chief(),
			"SubChief" => new SubChief(),
			"Assistant" => new Assistant(),
			"Member" => new Member(),
			default => new Visitor()
		};
	}
}