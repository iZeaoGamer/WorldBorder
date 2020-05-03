<?php 
namespace iZeaoGamer\WorldBorder\commands;

use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\utils\TextFormat;
use iZeaoGamer\WorldBorder\form\CustomForm;
use iZeaoGamer\WorldBorder\form\SimpleForm;
use iZeaoGamer\WorldBorder\Main;
use pocketmine\Player;

class WorldBorderCommand extends Command{
    public function __construct(Main $plugin){
        parent::__construct("worldborder");
        $this->setPermission("worldborder.command"); //todo make this configurable
        $this->setDescription("Customise World Borders in-game!");
        $this->setUsage("/worldborder <command>"); 
        $this->setAliases(["wb"]); //todo make configurable.
        $this->plugin = $plugin;
    }
    public function execute(CommandSender $sender, string $commandLabel, array $args){
        if(!$this->testPermission($sender)){
            return true;
        }
        if(!$sender instanceof Player){
            $sender->sendMessage("Use this command in-game!"); //todo make this configurable.
            return true;
        }
            if($this->plugin->getConfig()->get("open-type") === "forms" or $this->plugin->getConfig()->get("open-type") === "both"){
                $form = new SimpleForm("World Border", "Select action");
                $form->id = 0;
                $form->setContent("Customise World Border in-game!");
                $form->addButton("Worlds");
                $form->addButton("Range");
                $form->addButton("Teleport");
                $form->addButton("Default Level Spawn");
                $form->addButton("Spawn Location");
                $form->addButton("Coordinates");
                $player->sendForm($form);

            }elseif($this->plugin->getConfig()->get("open-type") === "command" or $this->plugin->getConfig()->get("open-type") === "both"){
                if(!isset($args[0])){
                $sender->sendMessage(TextFormat::colorize("&5&lWorldBorder &6Help Commands"));
                $sender->sendMessage(TextFormat::colorize("&7/$commandLabel world <string> - &bMake WorldBorders work in a specific world."));
                $sender->sendMessage(TextFormat::colorize("&7/$commandLabel range <int> - &bEdit how far a worldborder should be (In blocks)"));
                $sender->sendMessage(TextFormat::colorize("&7/$commandLabel teleport <bool> - &bControls whether or not you get teleported to a safe location once you've reached the world border."));
                $sender->sendMessage(TextFormat::colorize("&7/$commandLabel def-level-spawn <bool> - &bControls whether or not the range depends on the default level spawnpoint."));
                $sender->sendMessage(TextFormat::colorize("&7/$commandLabel spawn-location <bool> - &bControls wether or not the range depends on the spawn-location (/setworldspawn)"));
                $sender->sendMessage(TextFormat::colorize("&7/$commandLabel coordinates <int: X> <int: Y> <int: Z> - &bSets the coordinates to where the range begins from. (Must set def-level-spawn and spawn-location to false)."));
                return true;
                }
            if($args[0] === "world"){
                if(!isset($args[1])){
                    $sender->sendMessage(TextFormat::colorize("&cYou didn't enter the worlds you wanted the world border to work in. Please use: /$commandLabel $args[0] <string: worlds>"));
                    return true;
                }
                $this->plugin->getConfig()->set("worlds", implode("\n-", $args));
            $this->plugin->getConfig()->save();
            foreach($this->plugin->getConfig()->get("worlds") as $worlds){
            $sender->sendMessage(TextFormat::colorize("&5You have set the Worlds to be set as the following: &6" . $worlds));
            }
            return true;
        }
        if($args[0] === "range"){
            if(!isset($args[1])){
                $sender->sendMessage(TextFormat::colorize("&cUse: /$commandLabel range <int>"));
                return true;
            }
            if(is_string($args[1])){
                $sender->sendMessage(TextFormat::colorize("&cArgument 1 must return a int. String given."));
                return true;
            }
            $this->plugin->getConfig()->set("range", (int)$args[1]);
            $this->plugin->getConfig()->save();
            $sender->sendMessage(TextFormat::colorize("&5You have set the range to be as &6" . $args[1]));
            return true;
        }
        if($args[0] === "teleport"){
            if(!isset($args[1])){
                $sender->sendMessage(TextFormat::colorize("&cUse: /$commandLabel teleport <bool>"));
                return true;
            }  
            if(!is_bool($args[1])){
                $sender->sendMessage(TextFormat::colorize("&cArgument 1 must be a bool, invalid argument given."));
                return true;
            }
            $this->plugin->getConfig()->set("teleport", (bool)$args[1]);
            $this->plugin->getConfig()->save();
            $sender->sendMessage(TextFormat::colorize("&5You have set the teleportation to &6" . (bool)$args[1]));
            return true;
        }
        if($args[0] === "def-level-spawn"){
            if(!isset($args[1])){
                $sender->sendMessage(TextFormat::colorize("&cUse: /$commandLabel def-level-spawn <bool>"));
                return true;
            }  
            if(!is_bool($args[1])){
                $sender->sendMessage(TextFormat::colorize("&cArgument 1 must be a bool, invalid argument given."));
                return true;
            }
            $this->plugin->getConfig()->set("def-level-spawn", (bool)$args[1]);
            $this->plugin->getConfig()->save();
            $sender->sendMessage(TextFormat::colorize("&5You have set the def-level-spawn to &6" . (bool)$args[1]));
            return true;
        }
        if($args[0] === "spawn-location"){
            if(!isset($args[1])){
                $sender->sendMessage(TextFormat::colorize("&cUse: /$commandLabel spawn-location <bool>"));
                return true;
            }  
            if(!is_bool($args[1])){
                $sender->sendMessage(TextFormat::colorize("&cArgument 1 must be a bool, invalid argument given."));
                return true;
            }
            $this->plugin->getConfig()->set("spawn-location", (bool)$args[1]);
            $this->plugin->getConfig()->save();
            $sender->sendMessage(TextFormat::colorize("&5You have set the spawn-location to &6" . (bool)$args[1]));
            return true;
        }
        if($args[0] === "coordinates"){
            if(!isset($args[3])){
                $sender->sendMessage(TextFormat::colorize("&cUse: /$commandLabel coordinates <int: X> <int: Y> <int: Z>"));
                return true;
            }
            //todo implement checks to ensure coordinates aren't being handled via strings AKA letters.   
            $this->plugin->getConfig()->set("coordinates", (int)$args[1], (int)$args[2], (int)$args[3]);
            $this->plugin->getConfig()->save();
            $sender->sendMessage(TextFormat::colorize("&5Your coordinates have been set successfully. The coordinates have been set at: &6" . $args[1] . ", " . $args[2] . ", and " . $args[3]));
            return true;
        }


}


}
}
