<?php

namespace ArchonKlaus\task;

use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat as T;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\block\Block;
use pocketmine\entity\Effect;
use pocketmine\math\Vector3;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\utils\Config;
use ArchonKlaus\Loader;
use pocketmine\level\sound\ClickSound;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\level\sound\GhastSound;
use pocketmine\nbt\tag\{CompoundTag, StringTag};
use pocketmine\level\particle\FloatingTextParticle;

class Time extends Task{

private $wait = 60;
private $match = 60;
private $rounds = 1;
public $plugin = null;

public function __construct(Loader $plugin){
parent::__construct($plugin);
$this->plugin = $plugin;
}

public function onRun(int $tick){
if(count($this->plugin->players) <= 0){
$this->wait++;
return false;
}
if(count($this->plugin->players) === 1){
foreach($this->plugin->players as $player){
}
$this->wait++;
}
if(count($this->plugin->players) >= 2){
if($this->wait > 60){
$this->wait = 60;
}
$this->wait--;
foreach($this->plugin->players as $player){
$level = $this->plugin->getServer()->getLevelByName("wait");
$players = count($this->plugin->players);
$level->addParticle(new FloatingTextParticle(new Vector3(0, +1, 0), "", "§l§6TntTag§r\n§eStarting in: ".$this->wait."\n§8===\n§7Players: $players / 12", $player));
}
}
if(count($this->plugin->players) <= 1 and $this->wait < 60){
$this->wait = 60;
}
if($this->wait === 0){
$this->wait = 60;
$target = $this->plugin->players[array_rand($this->plugin->players)];
$target->getInventory()->setContents([]);
foreach($this->plugin->players as $p){ $level = $this->plugin->getServer()->getLevelByName("tnt");
$p->teleport($level->getSafeSpawn());
$p->sendMessage("§6====================\n§6Game - §l§7TntTag Game§r\n\n§7Escape the player who has the TNT to win the game!\n§7The last remaining player wins!\n\n§6Map - §l§7Tagger§r §6made for §l§7Server - BuildTeam§r\n§6====================");
}
$target->getInventory()->addItem($item = Item::get(Block::TNT, 0, 1));
$target->getInventory()->setItemInHand($item);
$target->sendMessage("§7Has obtened the TnT!");
foreach($target->getLevel()->getPlayers() as $player){
if($player !== $target){
$player->sendMessage("§7====================\n§6The player §l§7".$target->getName()." §r§6have the TNT!\n§7====================");
}
}
Loader::$started = true;
$this->plugin->target = $target->getName();
$this->match--;
}
if(Loader::$started){
foreach($this->plugin->players as $player){
$players = count($this->plugin->players);
$player->sendTip("§l§8{§6Tnt - Tag§8}§r\n\n§7Players: §8$players   §7Explode in: §8".$this->match);
if($player->getName() === $this->plugin->target){
$player->setNameTag("§6".$player->getName());
}else{
$player->setNameTag("§7".$player->getName());
}
}
}
if(count($this->plugin->players) > 2 and Loader::$started){
$this->match--;
if($this->match === 1){
$player = $this->plugin->getServer()->getPlayer($this->plugin->target);
if(!$player instanceof Player){
$target = $this->plugin->players[array_rand($this->plugin->players)];
$target->getInventory()->addItem(Item::get(Block::TNT, 0, 1));
$target->sendMessage("§7Has obtened theTnT!");
foreach($this->plugin->players as $player){
$player->getLevel()->addSound(new \pocketmine\level\sound\LaunchSound($player));
}
}else{
unset($this->plugin->players[array_search($player, $this->plugin->players)]);
$player->sendMessage("§cEliminated!");
$player->teleport($this->plugin->getServer()->getDefaultLevel()->getSafeSpawn());
$player->getInventory()->clearAll();
$target = $this->plugin->players[array_rand($this->plugin->players)];
$target->getInventory()->addItem(Item::get(Block::TNT, 0, 1));
$target->sendMessage("§7Eliminated!");
}
$this->match = 60;
}
}elseif(count($this->plugin->players) === 2 and Loader::$started){
$this->match--;
if($this->match === 1){
$player = $this->plugin->getServer()->getPlayer($this->plugin->target);
if($player instanceof Player){
unset($this->plugin->players[array_search($player, $this->plugin->players)]);
$player->sendMessage("§cEliminated!");
$player->teleport($this->plugin->getServer()->getDefaultLevel()->getSafeSpawn());
$player->getInventory()->clearAll();
}
$this->match = 60;
}
}
if(count($this->plugin->players) === 1 and Loader::$started){
$winner = $this->plugin->players[array_rand($this->plugin->players)];
if($winner instanceof Player){
$winner->teleport($this->plugin->getServer()->getDefaultLevel()->getSafeSpawn());
$this->plugin->getServer()->broadcastMessage("§7==================\n§bGame - §l§7TntTag§r\n\n§l§9Winner ".$winner->getName()."!§r\n\n§bMap - §l§7Tagger §r§bmade for §l§7Server - BuildTeam§r\n§7==================");
}
unset($this->plugin->players[array_search($winner, $this->plugin->players)]);
$this->plugin->players = [];
$this->match = 60;
$this->wait = 60;
$this->rounds = 1;
$this->plugin->target = "";
Loader::$started = false;
foreach($this->plugin->getServer()->getLevelByName("tnt")->getPlayers() as $players){
$players->teleport($this->plugin->getServer()->getDefaultLevel()->getSafeSpawn());
$players->setGamemode(0);
if($winner !== $players){
$puntos = new Config($this->plugin->getDataFolder()."puntos.yml", Config::YAML);
$name = $winner->getName();
switch(rand(1,3)){
case 1:
$winner->addTitle("§6CubeHero Coins","§7You have +6 coins");
$puntos->set($winner->getName(),$puntos->get($name) + 6);
$puntos->save();
break;
case 2:
$winner->addTitle("§6CubeHero Coins","§7You have +5 coins");
$puntos->set($winner->getName(),$puntos->get($name) + 5);
$puntos->save();
break;
case 3:
$winner->addTitle("§6CubeHero Coins","§7You have +3 coins");
$puntos->set($winner->getName(),$puntos->get($name) + 3);
$puntos->save();
break;}
$winner->teleport($this->plugin->getServer()->getDefaultLevel()->getSafeSpawn());
$this->plugin->getServer()->broadcastMessage("§7==================\n§bGame - §l§7TntTag§r\n\n§l§9Winner ".$winner->getName()."!§r\n\n§bMap - §l§7Tagger §r§bmade for §l§7Server - BuildTeam§r\n§7==================");
}
}
}
if($this->rounds === 15){
$this->plugin->players = [];
$this->match = 60;
$this->wait = 60;
$this->rounds = 1;
$this->plugin->target = "";
foreach($this->plugin->getServer()->getLevelByName("tnt")->getPlayers() as $players){
$players->teleport($this->plugin->getServer()->getDefaultLevel()->getSafeSpawn());
$players->setGamemode(0);
}
}
}

}