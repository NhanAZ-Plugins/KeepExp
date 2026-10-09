<?php

declare(strict_types=1);

require dirname(__DIR__) . '/src/ProtectionPolicy.php';

use NhanAZ\KeepExp\ProtectionPolicy;

$cases = [
	['disabled in all mode', false, 'all', [], 'world', false],
	['all mode', true, 'all', [], 'world', true],
	['listed whitelist world', true, 'whitelist', ['world'], 'world', true],
	['unlisted whitelist world', true, 'whitelist', ['world'], 'nether', false],
	['listed blacklist world', true, 'blacklist', ['world'], 'world', false],
	['unlisted blacklist world', true, 'blacklist', ['world'], 'nether', true],
	['empty blacklist', true, 'blacklist', [], 'world', true],
	['invalid mode is safe', true, 'unknown', [], 'world', false],
];

foreach($cases as [$name, $enabled, $mode, $worlds, $world, $expected]){
	$actual = ProtectionPolicy::applies($enabled, $mode, $worlds, $world);
	if($actual !== $expected){
		throw new RuntimeException($name . ' failed');
	}
}

echo 'PASS: ' . count($cases) . " protection policy cases\n";
