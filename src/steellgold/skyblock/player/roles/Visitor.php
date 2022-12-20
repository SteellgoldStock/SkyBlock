<?php

namespace steellgold\skyblock\player\roles;

use steellgold\skyblock\player\Role;

class Visitor extends Role {

	public function getName(): string {
		return "Visiteur";
	}

	public function getPermissions(): array {
		return [];
	}
}