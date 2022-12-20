<?php

namespace steellgold\skyblock\player\roles;

class Visitor extends Role {

	const VISITOR = "Visiteur";

	public function getName(): string {
		return "Visiteur";
	}

	public function getPermissions(): array {
		return [];
	}

	public function getIdentifier(): string {
		return "Visitor";
	}
}