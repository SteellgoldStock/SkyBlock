<?php

namespace steellgold\skyblock\player;

abstract class Role {
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

	abstract public function getName(): string;

	abstract public function getPermissions(): array;

	public function hasPermission(string $permission): bool {
		return in_array("*", $this->getPermissions()) || in_array($permission, $this->getPermissions());
	}
}