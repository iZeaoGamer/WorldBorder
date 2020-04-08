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
use pocketmine\level\Location;

use iZeaoGamer\WorldBorder\utils\FormManager;
use iZeaoGamer\WorldBorder\commands\WorldBorderCommand;

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
     /** @var  Main $instance */
     private static $instance;

    const safeBlocks = [0, 6, 8, 9, 27, 30, 31, 32, 37,
        38, 39, 40, 50, 59, 63, 64, 65,
        66, 68, 71, 78, 83, 104, 105, 106,
        141, 142, 171, 244];
        
    const unsafeBlocks = [10, 11, 51, 81];
    
public function onEnable(): void{
    self::$instance = $this;
    $this->formManager = new FormManager($this);

    
$this->getServer()->getPluginManager()->registerEvents($this, $this);
if(!is_file($this->getDataFolder() . "config.yml")){
    $this->saveDefaultConfig();
}
$this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML, array());
$this->getServer()->getCommandMap()->register("worldborder", new WorldBorderCommand($this));
  if($this->isSameFile()){
    $this->getLogger()->critical("There is a config error whilst loading the plugin.");
    $this->getLogger()->error("def-level-spawn and spawn-location options cannot both be set to true in WorldBorder config.yml file on line 26 and line 30.");
    $this->getServer()->getPluginManager()->disablePlugin($this);
}
if (!is_dir($this->getDataFolder())) {
    @mkdir($this->getDataFolder());
    }
}
	//todo be able to change safe, and unsafe block ids.

     /**
     * @return Main $plugin
     */
    public static function getInstance(): Main {
        return self::$instance;
    }
	/**
	* Checks whether or not BOTH options are the same.
	* @return bool
	*/
public function isSameFile() : bool{
    return ($this->config->get("def-level-spawn") and $this->config->get("spawn-location"));
}
	/**
	* Checks to ensure the worldborder is compatible with certain worlds, as configured in the config.yml file.
	* @param Player $player
	* @return bool
	*/
public function isInWorld(Player $player): bool{
return (in_array($player->getLevel()->getFolderName(), $this->config->get("worlds")));
}
	
/**
* Checks for if the position of the range is near or inside the specific range distance.
* Player: Checks for the player name, and to recognize whether or not the player is in the range.
* Position: Checks for the position of the range (More likely the spawnpoint or the spawn position (/setworldspawn, /setspawn, or otherwise.)
* @param Player $player
* @param Position $spawn
* @return bool
*/
public function isInRange(Player $player, Position $spawn): bool{
return ($spawn->distance($player) >= $this->config->get("range"));
}
	
/**
* Sets the range via the config.
* @param int $range
*/
public function setRange(int $range){
$this->config->set("range", $range);
$this->config->save();
}
	
/**
* Reloads the config file, can be used by other plugins if wanting to reload their configurations.
* @param Config
* @param bool $save
*/
public function configReload(Config $config, bool $save = true){
	if($save){
$config->save();
	}
$config->reload();
}
	
/**
* Returns the default config.yml used by this plugin.
* This function will not work by other plugins. So this function is internal, and should only be used by this plugin.
* @return Config
*/
public function getPluginConfig(): Config{
return $this->config;
}
public function Boarder(PlayerMoveEvent $event){
  
    if (in_array($event->getPlayer()->getLevel()->getFolderName(), $this->config->get("worlds"))) {
    if($this->config->get("def-level-spawn")){
        $spawn = $this->getServer()->getDefaultLevel()->getSpawnLocation();
    }else{
	    if($this->config->get("spawn-location")){
		    $spawn = $event->getPlayer()->getLevel()->getSpawnLocation();
		    }else{
                    $cords = explode(", ", $this->config->get("coordinates"));
                    $x = $cords[0];
                    $y = $cords[1];
                    $z = $cords[2];
        $spawn = new Vector3($x, $y, $z);
    }
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
    }

															/**
     * @param Location $location
     * @return Vector3
     */
    public function correctPosition(Location $location) : Vector3 {
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
     * @param int $x
     * @param int $y
     * @param int $z
     * @return int
     */
    private function findSafeY(Level $level, int $x, int $y, int $z) : int {
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
     * @param int $x
     * @param int $y
     * @param int $z
     * @return bool
     */
    private function isSafe(Level $level, int $x, int $y, int $z) : bool{
        $safe = in_array($level->getBlockIdAt($x, $y, $z), self::safeBlocks) && in_array($level->getBlockIdAt($x, $y + 1, $z), self::safeBlocks);
        if(!$safe) return $safe;
        $below = $level->getBlockIdAt($x, $y - 1, $z);
        return ($safe and (!in_array($below, self::safeBlocks) or $below === 8 or $below === 9) and !in_array($below, self::unsafeBlocks));
    }
}
