<?php

namespace WorldBlock;

// 플러그인
use pocketmine\plugin\PluginBase;
// 이벤트
use pocketmine\event\Listener;
// 이벤트 블럭
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
// 커맨드
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
// 객체
use pocketmine\Player;
// 유틸
use pocketmine\utils\Config;
use pocketmine\utils\Utils;

class WorldBlock extends PluginBase implements Listener {
	public function onEnable() {
		@mkdir ( $this->getDataFolder () );
		$this->j = new Config ( $this->getDataFolder () . "j.yml", Config::YAML, [ 
				"월드보호" => [ 
						"world" 
				] 
		] );
		$this->jdb = $this->j->getAll ();
		$this->getServer ()->getPluginManager ()->registerEvents ( $this, $this );
	}
	public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
		$command = $command->getName ();
		$tag = "§b§l[ §f월드§b ]§f";
		if ($command == "월드보호") {
			if (! isset ( $args [0] )) {
				$sender->sendMessage ( $tag . " /월드보호 추가 [ 월드이름 ]" );
				return true;
			}
			switch ($args [0]) {
				case "추가" :
					if (! isset ( $args [1] )) {
						$sender->sendMessage ( $tag . " /월드보호 추가 [ 월드이름 ]" );
						return true;
					}
					switch ($args [1]) {
						case $args [1] :
							if ($sender->getPlayer ()->isOp ()) {
								$sender->sendMessage ( $tag . " 정상적으로 " . $args [1] . " 의 월드가 보호를 진행합니다." );
								$this->jdb ["월드보호"] = [ 
										$args [1] 
								];
								$this->save ();
								break;
							} else {
								$sender->sendMessage ( $tag . " 당신은 권한이 없어 명령어를 사용하지 못합니다." );
								return true;
							}
							break;
					}
					break;
			}
		}
		return true;
	}
	public function Break(BlockBreakEvent $event) {
		$tag = "§b§l[ §f월드§b ]§f";
		$player = $event->getPlayer ();
		if (! $player->isOp ()) {
			if (in_array($event->getBlock ()->getLevel ()->getFolderName () ,$this->jdb ["월드보호"])) {
				$player->sendPopup ( $tag . " 해당 월드는 보호가 진행중 입니다." );
				$event->setCancelled ();
			}
		}
	}
	public function Interact(BlockPlaceEvent $event) {
		$tag = "§b§l[ §f월드§b ]§f";
		$player = $event->getPlayer ();
		if (! $player->isOp ()) {
			if (in_array($event->getBlock ()->getLevel ()->getFolderName () ,$this->jdb ["월드보호"])) {
				$player->sendPopup ( $tag . " 해당 월드는 보호가 진행중 입니다." );
				$event->setCancelled ();
			}
		}
	}
	public function onDisable() {
		$this->save ();
	}
	public function save() {
		$this->j->setAll ( $this->jdb );
		$this->j->save ();
	}
}