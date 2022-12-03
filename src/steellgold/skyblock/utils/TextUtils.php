<?php

namespace steellgold\skyblock\utils;

class TextUtils {
	const PREFIX = "§dSkyBlock §f» ";
	const ERROR = "§cErreur §f» ";

	public static function getPrefix(): string {
		return self::PREFIX;
	}

	public static function getNoPermissionMessage(): string {
		return self::PREFIX . "§cVous n'avez pas la permission d'utiliser cette commande.";
	}

	public static function text(string $text): string {
		return self::PREFIX . $text;
	}

	public static function error(string $text): string {
		return self::ERROR . $text;
	}
}