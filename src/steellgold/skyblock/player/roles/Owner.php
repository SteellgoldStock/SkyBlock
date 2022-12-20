<?php

namespace steellgold\skyblock\player;

class Owner extends Role {

	public function getName(): string {
		return "Chef";
	}

	public function getPermissions(): array {
		return [
			"*"
		];
	}
}