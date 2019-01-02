<?php

namespace ArchonKlaus\entities;

use ArchonKlaus\Loader;
use pocketmine\entity\Entity;
use pocketmine\Player;
use pocketmine\network\mcpe\protocol\MoveEntityPacket;
use pocketmine\network\mcpe\protocol\AddEntityPacket;
use pocketmine\math\Vector2;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\utils\TextFormat;

class Spect extends Entity{
	
const NETWORK_ID = 36;

public function spawnTo(Player $sender){
parent::spawnTo($sender);
$pk = new AddEntityPacket();
$pk->entityRuntimeId = $this->id;
$pk->type = self::NETWORK_ID;
$pk->position = $this->asVector3();
$pk->yaw = $sender->yaw;
$pk->pitch = $sender->pitch;
$pk->metadata = $this->dataProperties;
$sender->dataPacket($pk);
}

public function saveNBT(){
parent::saveNBT();
}

public function initEntity(){
parent::initEntity();
}

public function getName(): string{
return "§7Spectate Map\n§bFree";
}
}