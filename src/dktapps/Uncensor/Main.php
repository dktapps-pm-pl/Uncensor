<?php

namespace dktapps\Uncensor;

use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\mcpe\protocol\TextPacket;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener{

	/** @var string[] */
	private $words = [];
	/** @var string */
	private $regex;

	public function onEnable() : void{
		$this->getServer()->getPluginManager()->registerEvents($this, $this);

		if(file_exists($this->getDataFolder() . "profanity_filter.wlist")){
			$this->words = file($this->getDataFolder() . "profanity_filter.wlist", FILE_IGNORE_NEW_LINES);
			$this->getLogger()->notice("Loaded word list!");
		}else{
			$this->getLogger()->error("Can't find word list! Please extract it from the game and place it in the plugin's data folder.");
			$this->getServer()->getPluginManager()->disablePlugin($this);
			return;
		}

		$this->regex = '/.*?(' . implode('|', array_map('preg_quote', $this->words)) . ').*?/i';
	}

	private function unfilter(string $message) : string{
		return preg_replace_callback($this->regex, function($matches){
			return str_replace($matches[1], $matches[1]{0} . "\x1c" . substr($matches[1], 1), $matches[0]);
		}, $message);
	}

	public function onDataPacketSend(DataPacketSendEvent $event) : void{
		$pk = $event->getPacket();
		if($pk instanceof TextPacket){
			if($pk->type !== TextPacket::TYPE_TRANSLATION){
				$pk->message = $this->unfilter($pk->message);
			}
			foreach($pk->parameters as $k => $param){
				$pk->parameters[$k] = $this->unfilter($pk->parameters[$k]);
			}
		}
	}
}
