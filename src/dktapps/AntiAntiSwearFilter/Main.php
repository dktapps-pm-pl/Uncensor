<?php

namespace dktapps\AntiAntiSwearFilter;

use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\mcpe\protocol\TextPacket;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener{

	/** @var string[] */
	private $words = [];
	/** @var string[] */
	private $replacements = [];

	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);

		if(file_exists($this->getDataFolder() . "profanity_filter.wlist")){
			$this->words = file($this->getDataFolder() . "profanity_filter.wlist", FILE_IGNORE_NEW_LINES);
			$this->getLogger()->notice("Loaded word list!");
		}else{
			$this->getLogger()->error("Can't find word list! Please extract it from the game and place it in the plugin's data folder.");
			$this->getServer()->getPluginManager()->disablePlugin($this);
			return;
		}

		$this->replacements = array_map(function($word){
			return substr($word, 0, 1) . "\x1c" . substr($word, 1);
		}, $this->words);
	}

	public function onDataPacketSend(DataPacketSendEvent $event){
		$pk = $event->getPacket();
		if($pk instanceof TextPacket){
			if($pk->type !== TextPacket::TYPE_TRANSLATION){
				$pk->message = str_replace($this->words, $this->replacements, $pk->message);
			}
			foreach($pk->parameters as $k => $param){
				$pk->parameters[$k] = str_replace($this->words, $this->replacements, $param);
			}
		}
	}
}