<?php

namespace ArchonKlaus;

/* 
--------------------
Credits to:         
     ArchonKlaus©
--------------------
Don't steal it!
*/

use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\event\entity\{EntityDamageEvent, EntityDamageByEntityEvent};
use pocketmine\Player;
use pocketmine\item\Item;
use pocketmine\block\Block;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\{PlayerQuitEvent, PlayerKickEvent, PlayerDeathEvent, PlayerInteractEvent};
use pocketmine\event\block\{BlockBreakEvent, BlockPlaceEvent};
use pocketmine\utils\Config;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\entity\Entity;
use ArchonKlaus\entities\NPC;
use ArchonKlaus\entities\Speed;
use ArchonKlaus\entities\Leaper;
use ArchonKlaus\entities\Spect;
use ArchonKlaus\task\Time;
use ArchonKlaus\task\Tasking;
use ArchonKlaus\command\Tagging;

class Loader extends PluginBase implements Listener{

public $arenas = array();

public $players = [];

public static $started = false;

public $target = "";

public function onEnable(){
$this->getServer()->loadLevel("wait");
$this->getServer()->getPluginManager()->registerEvents($this, $this);
$this->getServer()->getScheduler()->scheduleRepeatingTask(new Time($this), 20);
$this->getServer()->getScheduler()->scheduleRepeatingTask(new Tasking($this), 20);
$this->getServer()->getCommandMap()->register("/tnt", new Tagging($this));
$this->getLogger()->info("Charged!");
Entity::registerEntity(NPC::class);
Entity::registerEntity(Speed::class);
Entity::registerEntity(Leaper::class);
Entity::registerEntity(Spect::class);
@mkdir($this->getDataFolder());
$puntos = new Config($this->getDataFolder()."puntos.yml", Config::YAML);
$this->getServer()->loadLevel("tnt");
}

public function onDamageNPC(EntityDamageEvent $event){
$npcgame = $event->getEntity();
$player = $event->getDamager();
if($npcgame instanceof NPC){
if(self::$started){
$player->sendMessage("§cNo arenas available!");
return false;
}
$level = $this->getServer()->getLevelByName("wait");
$player->teleport($level->getSafeSpawn());
$player->getInventory()->clearAll();
$player->setFood(20);
$this->players[] = $player;
return false;}
if($npcgame instanceof Spect){
$player->setGamemode(3);
$level = $this->getServer()->getLevelByName("tnt");
$player->teleport($level->getSafeSpawn());
$player->getInventory()->clearAll();
$player->setGamemode(3);
$this->players[] = $player;}
if($npcgame instanceof Speed){

}}

public function onEx(PlayerExhaustEvent $event){
if(in_array($event->getPlayer(), $this->players)){
$event->setCancelled(true);
}
}

public function onDeath(PlayerDeathEvent $event){
if(in_array($event->getPlayer(), $this->players)){
unset($this->players[array_search($event->getPlayer(), $this->players)]);
$event->setDrops([]);
}
}

public function onInteract(PlayerInteractEvent $event){
$player = $event->getPlayer();
$block = $event->getBlock();
if(($tile = $event->getPlayer()->getLevel()->getTile($block)) instanceof \pocketmine\tile\Sign){
if(in_array($player, $this->players)){
return false;
}
$text = $tile->getText();
if($text[0] === "minigame: tnttag" and $text[1] === "level: tag"){
if(count($this->players) >= 20 or self::$started){
$player->sendMessage("You can't join");
return false;
}
$level = $this->getServer()->getLevelByName("tnt");
$player->teleport($level->getSafeSpawn());
$player->getInventory()->clearAll();
$this->players[] = $player;
}
}
}

public function onKick(PlayerKickEvent $event){
if(in_array($event->getPlayer(), $this->players)){
unset($this->players[array_search($event->getPlayer(), $this->players)]);
}
}

public function onMoveVoid(PlayerMoveEvent $event) {
if(in_array($event->getPlayer(), $this->players)){
if($event->getPlayer()->getY() < -5) {
			$event->getPlayer()->teleport($event->getPlayer()->getLevel()->getSafeSpawn());}}}
	
public function onDamageVoid(EntityDamageEvent $event) {
if(in_array($event->getEntity(), $this->players)){
if($event->getEntity() instanceof Player && $event->getEntity()->getY() < 0) {
$event->setCancelled();}}}

public function onQuit(PlayerQuitEvent $event){
if(in_array($event->getPlayer(), $this->players)){
unset($this->players[array_search($event->getPlayer(), $this->players)]);
}
}

public function onDamageHub(EntityDamageEvent $event){
$npcgame = $event->getEntity();
$player = $event->getDamager();
if($npcgame instanceof HUB){
$player->getInventory()->clearAll();
$player->teleport($this->getServer()->getDefaultLevel()->getSpawnLocation());
return false;}}

public function onDamage(EntityDamageEvent $event){
$player = $event->getEntity();
if($player instanceof Player and in_array($player, $this->players)){
$event->setCancelled(true);
}
if($event instanceof EntityDamageByEntityEvent and ($damager = $event->getDamager()) instanceof Player){
if(!self::$started){
return false;
}
if(in_array($damager, $this->players) and $damager->getInventory()->contains(Item::get(Block::TNT, 0, 1))){
$damager->getInventory()->setContents([]);
$this->target = $player->getName();
foreach($damager->getLevel()->getPlayers() as $players){
$players->sendMessage("§7====================\n§6The player §l§7".$player->getName()." §r§6have the TNT!\n§7====================");
}
$player->getInventory()->addItem($item = Item::get(Block::TNT, 0, 1));
$player->getInventory()->setItemInHand($item);
}
}
}

public function onMove(PlayerMoveEvent $event){
$player = $event->getPlayer();
$level = $event->getPlayer()->getLevel();
if($level == $this->getServer()->getDefaultLevel()){
if(in_array($event->getPlayer(), $this->players)){
unset($this->players[array_search($event->getPlayer(), $this->players)]);
}
}
if(in_array($event->getPlayer(), $this->players) and $this->target === $event->getPlayer()->getName()){
$event->getPlayer()->getLevel()->addParticle(new \pocketmine\level\particle\FlameParticle($event->getPlayer()->add(0, 1)));
}
}

public function onBreak(BlockBreakEvent $event){
$player = $event->getPlayer();
if(in_array($player, $this->players)){
$event->setCancelled(true);
}
}

public function onPlace(BlockPlaceEvent $event){
$player = $event->getPlayer();
if(in_array($player, $this->players)){
$event->setCancelled(true);
}
}}