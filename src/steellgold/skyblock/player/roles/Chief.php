<?php

namespace steellgold\skyblock\player\roles;

class Chief extends Role {

	const CHIEF = "Chef";

	public function getName(): string {
		return "Chef";
	}

	public function getPermissions(): array {
		return [
			"*"
		];
	}
}