<?php

namespace ArchonKlaus\command;

use ArchonKlaus\Loader;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use pocketmine\utils\Config;
use pocketmine\entity\Entity;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use ArchonKlaus\entities\NPC;
use ArchonKlaus\entities\Speed;
use ArchonKlaus\entities\Leaper;
use ArchonKlaus\entities\Spect;

class Tagging extends PluginCommand{
	
public function __construct(Loader $plugin){
 $this->plugin = $plugin;
	parent::__construct("tag", $plugin);
	$this->getDescription("TntTag Games - Klaus");
	$this->setAliases(["tag", "tag"]);
}

public function execute(CommandSender $sender, String $label, array $args): bool{
if($sender->isOp()){
if($args[0]=="npc"){
$compoundtag = Entity::createBaseNBT($sender->asVector3());
$npcgame = Entity::createEntity("NPC", $sender->getLevel(), $compoundtag);
$npcgame->setNameTag($npcgame->getName());
$npcgame->setNameTagAlwaysVisible(true);
$npcgame->spawnToAll();
}
if($args[0]=="speed"){
$compoundtag = Entity::createBaseNBT($sender->asVector3());
$npcgame = Entity::createEntity("Speed", $sender->getLevel(), $compoundtag);
$npcgame->setNameTag($npcgame->getName());
$npcgame->setNameTagAlwaysVisible(true);
$npcgame->spawnToAll();
}
if($args[0]=="leaper"){
$compoundtag = Entity::createBaseNBT($sender->asVector3());
$npcgame = Entity::createEntity("Leaper", $sender->getLevel(), $compoundtag);
$npcgame->setNameTag($npcgame->getName());
$npcgame->setNameTagAlwaysVisible(true);
$npcgame->spawnToAll();
}
if($args[0]=="spect"){
$compoundtag = Entity::createBaseNBT($sender->asVector3());
$npcgame = Entity::createEntity("Spect", $sender->getLevel(), $compoundtag);
$npcgame->setNameTag($npcgame->getName());
$npcgame->setNameTagAlwaysVisible(true);
$npcgame->spawnToAll();
}}}}