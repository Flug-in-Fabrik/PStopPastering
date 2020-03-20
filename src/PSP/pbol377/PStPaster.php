<?php
namespace PSP\pbol377;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\Player;
use pocketmine\scheduler\Task;
use pocketmine\utils\Config;

class PStPaster extends PluginBase implements Listener{
	
public function onEnable() {
		$this->getServer()->getPluginManager()->registerEvents ($this, $this);
		$this->data = new Config($this->getDataFolder() . "data.yml", Config::YAML);
        $this->db = $this->data->getAll();
		$ptask = new GTask($this);
		$this->getScheduler()-> scheduleRepeatingTask($ptask, 200);
		}
		
public function onJoin(PlayerJoinEvent $event){
	$player = $event->getPlayer();
	$name = $player->getName();
	if(!isset($this->db[$name])){
		$this->db[$name]=0;
		$this->save();
		}
	}
                    
public function onChat(PlayerChatEvent $event){
	$player = $event->getPlayer();
	$name = $player->getName();
	if(!$player->isOp()){
		if($this->db[$name]<5){
			$this->db[$name]++;
			$this->save();
		}
		else{
			$player->sendMessage("§l§c[ §f도배 방지§c ] §a1~10초 후에 채팅을 쳐주시기 바랍니다");
			$event->setCancelled();
		}
	}
}              

public function dpas($name){
	$this->db[$name] = 0;
	$this->save();
	}
	
public function save(){
		$this->data->setAll($this->db);
		$this->data->save();
	}
}
class GTask extends Task{
	private $ll;
	public function __construct(PStPaster $ll){
				$this->load = $ll;
			}
	public function onRun( $currentTick ) {
		foreach( $this->load->getServer()->getOnlinePlayers() as $player) {
			$name = $player->getName();
			$this->load->dpas($name);
		       }//foreach
		}//pub
	}
