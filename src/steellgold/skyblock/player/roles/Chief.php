<?php

namespace steellgold\skyblock\player\roles;

class Chief extends Role {

	public function getName(): string {
		return "Chef";
	}

	public function getPermissions(): array {
		return [
			"*"
		];
	}
}