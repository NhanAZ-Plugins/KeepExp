<?php

declare(strict_types=1);

namespace NhanAZ\KeepExp;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\network\mcpe\protocol\ToastRequestPacket;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

final class Main extends PluginBase implements Listener{
	private bool $keepExp = true;
	private string $typeExp = 'realExp';
	private string $mode = 'all';
	/** @var list<string> */
	private array $worlds = [];
	private string $message = 'You died, but your experience is safe!';
	private string $toastTitle = 'KeepExp';
	private string $messageType = 'none';
	/** @var array<string, true> */
	private array $pendingMessages = [];

	protected function onEnable() : void{
		$this->saveDefaultConfig();
		$config = $this->getConfig();

		$keepExp = $config->get('keepExp', true);
		if(!is_bool($keepExp)){
			$this->getLogger()->warning('keepExp must be a boolean. Using true.');
		}else{
			$this->keepExp = $keepExp;
		}

		$this->typeExp = $this->readChoice($config, 'typeExp', 'realExp', ['realExp', 'droppedExp']);
		$this->mode = $this->readChoice($config, 'mode', 'all', ['all', 'whitelist', 'blacklist']);
		$this->messageType = $this->readChoice($config, 'msgType', 'none', ['none', 'message', 'title', 'popup', 'tip', 'actionbar', 'toast']);
		$this->message = $this->readString($config, 'msgAfterRespawn', $this->message);
		$this->toastTitle = $this->readString($config, 'toastTitle', $this->toastTitle);

		$worlds = $config->get('worlds', []);
		if(!is_array($worlds)){
			$this->getLogger()->warning('worlds must be a list of world names. Using an empty list.');
		}else{
			foreach($worlds as $world){
				if(is_string($world)){
					$this->worlds[] = $world;
				}else{
					$this->getLogger()->warning('Ignoring a non-string entry in worlds.');
				}
			}
		}

		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	/** @param list<string> $choices */
	private function readChoice(Config $config, string $key, string $default, array $choices) : string{
		$value = $config->get($key, $default);
		if(is_string($value) && in_array($value, $choices, true)){
			return $value;
		}
		$this->getLogger()->warning($key . ' has an invalid value. Using ' . $default . '.');
		return $default;
	}

	private function readString(Config $config, string $key, string $default) : string{
		$value = $config->get($key, $default);
		if(is_string($value)){
			return $value;
		}
		$this->getLogger()->warning($key . ' must be text. Using the default message.');
		return $default;
	}

	public function onPlayerDeath(PlayerDeathEvent $event) : void{
		$player = $event->getPlayer();
		if(!ProtectionPolicy::applies($this->keepExp, $this->mode, $this->worlds, $player->getWorld()->getDisplayName()) || $event->getKeepXp()){
			return;
		}

		$event->setKeepXp(true);
		if($this->typeExp === 'droppedExp' && !$player->getXpManager()->setCurrentTotalXp($event->getXpDropAmount())){
			$this->getLogger()->warning('Could not set the configured dropped XP amount. The player keeps their existing XP.');
		}

		if($this->messageType !== 'none'){
			$this->pendingMessages[$player->getUniqueId()->toString()] = true;
		}
	}

	public function onPlayerRespawn(PlayerRespawnEvent $event) : void{
		$player = $event->getPlayer();
		$id = $player->getUniqueId()->toString();
		if(!isset($this->pendingMessages[$id])){
			return;
		}
		unset($this->pendingMessages[$id]);

		$this->getScheduler()->scheduleDelayedTask(new ClosureTask(function() use ($player) : void{
			if($player->isOnline()){
				$this->sendRespawnMessage($player);
			}
		}), 1);
	}

	public function onPlayerQuit(PlayerQuitEvent $event) : void{
		unset($this->pendingMessages[$event->getPlayer()->getUniqueId()->toString()]);
	}

	private function sendRespawnMessage(Player $player) : void{
		$message = TextFormat::colorize($this->message);
		switch($this->messageType){
			case 'message':
				$player->sendMessage($message);
				break;
			case 'title':
				$player->sendTitle($message);
				break;
			case 'popup':
				$player->sendPopup($message);
				break;
			case 'tip':
				$player->sendTip($message);
				break;
			case 'actionbar':
				$player->sendActionBarMessage($message);
				break;
			case 'toast':
				$player->getNetworkSession()->sendDataPacket(ToastRequestPacket::create(TextFormat::colorize($this->toastTitle), $message));
				break;
		}
	}
}
