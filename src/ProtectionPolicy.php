<?php

declare(strict_types=1);

namespace NhanAZ\KeepExp;

final class ProtectionPolicy{
	/** @param list<string> $worlds */
	public static function applies(bool $enabled, string $mode, array $worlds, string $worldName) : bool{
		if(!$enabled){
			return false;
		}
		return match($mode){
			'all' => true,
			'whitelist' => in_array($worldName, $worlds, true),
			'blacklist' => !in_array($worldName, $worlds, true),
			default => false
		};
	}
}
