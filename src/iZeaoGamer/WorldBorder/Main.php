<?php 
namespace iZeaoGamer\WorldBorder;

use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\utils\TextFormat;
use pocketmine\utils\Config;
use pocketmine\level\Position;

class Main extends PluginBase implements Listener{
    private $x;
    private $z;
    private $radius;
    private $maxX;
    private $maxZ;
    private $minX;
    private $minZ;
    private $safeBlocks;
    private $unsafeBlocks;
    private $level;
    public $config;

    const safeBlocks = [0, 6, 8, 9, 27, 30, 31, 32, 37,
        38, 39, 40, 50, 59, 63, 64, 65,
        66, 68, 71, 78, 83, 104, 105, 106,
        141, 142, 171, 244];
        
    const unsafeBlocks = [10, 11, 51, 81];
    
public function onEnable(): void{
$this->getServer()->getPluginManager()->registerEvents($this, $this);
if(!is_file($this->getDataFolder() . "config.yml")){
    $this->saveDefaultConfig();
}
$this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML, array());

if (!is_dir($this->getDataFolder())) {
    @mkdir($this->getDataFolder());
    }
}
public function Boarder(PlayerMoveEvent $event){
  
    
    if($this->config->get("def-level-spawn")){
        $spawn = $this->getServer()->getDefaultLevel()->getSpawnLocation();
    }else{
        $spawn = new Vector3($this->config->get("spawn-coordinates")); //todo implement multiworld support
    }
	 $player = $event->getPlayer();
		 if($spawn->distance($player) >= $this->config->get("range")){
          $event->setCancelled(true);
          if($this->config->get("teleport")){
          $player->teleport($this->correctPosition($player->getLocation()));
          }
			  $player->sendMessage(TextFormat::colorize($this->config->get("border-message")));
		 }
	}

															/**
     * @param $location
     * @return Vector3
     */
    public function correctPosition($location) : Vector3 {
        $knockback = 4.0;
        $x = $location->getX();
        $z = $location->getZ();
        $y = $location->getY();
        if($x <= $this->minX){
            $x = $this->minX + $knockback;
        }
        elseif($x >= $this->maxX){
            $x = $this->maxX - $knockback;
        }
        if($z <= $this->minZ){
            $z = $this->minZ + $knockback;
        }
        elseif($z >= $this->maxZ){
            $z = $this->maxZ - $knockback;
        }
        $y = $this->findSafeY($location->getLevel(), $x, $y, $z);
        if($y < 10){
            $y =  70;
        }
        if($this->radius === 25){
            $x = $location->getLevel()->getSpawnLocation()->getX();
            $y = $location->getLevel()->getSpawnLocation()->getY();
            $z = $location->getLevel()->getSpawnLocation()->getZ();
        }
        return new Vector3($x, $y, $z);
    }
    /**
     * @param Level $level
     * @param $x
     * @param $y
     * @param $z
     * @return int
     */
    private function findSafeY(Level $level, $x, $y, $z) : int {
        $top = $level->getHeightMap($x, $z) - 2;
        $bottom = 1;
        for($y1 = $y, $y2 = $y; ($y1 > $bottom) or ($y2 < $top); $y1--, $y2++){
            if($y1 > $bottom){
                if($this->isSafe($level, $x, $y1, $z)) return $y1;
            }
            if($y2 < $top and $y2 != $y1){
                if($this->isSafe($level, $x, $y2, $z)) return $y2;
            }
        }
        return -1;
    }
    /**
     * @param Level $level
     * @param $x
     * @param $y
     * @param $z
     * @return bool
     */
    private function isSafe(Level $level, $x, $y, $z) : bool{
        $safe = in_array($level->getBlockIdAt($x, $y, $z), self::safeBlocks) && in_array($level->getBlockIdAt($x, $y + 1, $z), self::safeBlocks);
        if(!$safe) return $safe;
        $below = $level->getBlockIdAt($x, $y - 1, $z);
        return ($safe and (!in_array($below, self::safeBlocks) or $below === 8 or $below === 9) and !in_array($below, self::unsafeBlocks));
    }
}
