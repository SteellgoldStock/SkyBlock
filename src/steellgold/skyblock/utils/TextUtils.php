<?php

namespace steellgold\skyblock\utils;

use steellgold\skyblock\player\roles\Role;

class TextUtils {

	const PREFIX = "§dSkyBlock §f» ";
	const ERROR = "§cErreur §f» ";

	const FORM_TITLE = "§d- SkyBlock -";
	const FORM_TITLE_MODAL = "SkyBlock";

	public static function getNoPermissionMessage(?Role $role = null): string {
		if ($role === null) return "Vous n'avez pas la permission d'exécuter cette commande.";
		return "Vous n'avez pas la permission d'exécuter cette commande. Vous devez être §f{$role->getName()} §cou plus.";
	}

	public static function text(string $text): string {
		return self::PREFIX . $text;
	}

	public static function error(string $text): string {
		return self::ERROR . $text;
	}
}