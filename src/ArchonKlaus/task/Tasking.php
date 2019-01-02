<?php

namespace ArchonKlaus\task;

use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat as T;
use ArchonKlaus\Loader;
use ArchonKlaus\entities\NPC;

class Tasking extends PluginTask{

public $plugin;

public function __construct(Loader $plugin){
$this->plugin = $plugin; 
$this->time = 8;
}

public function onRun($currentTick){
$this->time++;
$players = count($this->plugin->players);
foreach($this->plugin->getServer()->getLevels() as $lvls){
foreach($lvls->getEntities()  as $e){ 
if($e instanceof NPC){
$e->setNameTag("§l§6Tnt Tag§r\n§e$players Playing\n§7Click me to play!");}}}}}
