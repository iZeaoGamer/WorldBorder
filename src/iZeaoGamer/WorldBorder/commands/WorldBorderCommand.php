<?php 
namespace iZeaoGamer\WorldBorder\commands;

use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\utils\TextFormat;
use jojoe77777\FormAPI\CustomForm;
use jojoe77777\FormAPI\SimpleForm;

class WorldBorderCommand extends Command{
    public function __construct(Main $plugin){
        parent::__consturct("worldborder");
        $this->setPermission("worldborder.command"); //todo make this configurable
        $this->setDescription("Customise World Borders in-game!");
        $this->setUsage("/worldborder <command>"); 
        $this->setAliases(["wb"]); //todo make configurable.
        $this->plugin = $plugin;
    }
    public function execute(CommandSender $sender, string $commandLabel, array $args): bool{
        if(!$this->testPermission($sender)){
            return true;
        }
        if(!$sender instanceof Player){
            $sender->sendMessage("Use this command in-game!"); //todo make this configurable.
            return true;
        }
            if($this->plugin->getConfig()->get("open-type") === "forms" or $this->plugin->getConfig()->get("open-type") === "both"){
                $this->MainForm($sender);
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
public function MainForms(Player $sender){
    $form = new SimpleForm(function (Player $sender, $data){
        $result = $data;
        if ($result === null) {
            return;
        }
            switch ($result) {
                case 0:
                    $this->WorldForms($sender);
                break;
                case 1:
                    $this->RangeForms($sender);
                    break;
                    case 2:
                    $this->TeleportForms($sender);
                    break;
                    case 3:
                        $this->DefLevelSpawnForms($sender);
                    break;
                    case 4:
                       $this->SpawnLocationForms($sender);
                    break; 
                    case 5:
                        $this->CoordinatesForms($sender);
                    break;
            }
        
            });
        
            $form->setTitle("WorldBorder Config"); 
            $form->setContent("Customise World Border in-game!");
            $form->addButton("Worlds");
            $form->addButton("Range");
            $form->addButton("Teleport");
            $form->addButton("Default Level Spawn");
            $form->addButton("Spawn Location");
            $form->addButton("Coordinates");
            $player->sendForm($form);
    }
public function WorldForms(Player $sender){
    $form = new CustomForm(function (Player $sender, $data){
        if($result === null){
            return;
        }
            $this->plugin->getConfig()->set("worlds", implode("\n-", $data[1]));
            $this->plugin->getConfig()->save();
            $this->WorldSuccess($sender);
            return true;
        });
        $form->setTitle("WorldBorder - Worlds");
        $form->addLabel("Configure which worlds the world border should work in.");
        foreach($this->plugin->getConfig()->get("worlds") as $worlds){
        $form->addInput("Worlds", $worlds);
        }
        $player->sendForm($form);
    }
    public function RangeForms(Player $sender){
        $form = new CustomForm(function (Player $sender, $data){
            if($result === null){
                return;
            }
            if(is_string($result[1])){
                $this->RangeString($sender);
                return true;
            }
            $this->plugin->getConfig()->set("range", (int)$result[1]);
            $this->plugin->getConfig()->save();
            $this->RangeSuccess($sender);
        });
        $form->setTitle("WorldBorder - Range");
        $form->addLabel("Configure how far a worldborder distance (in blocks) should be.");
        $form->addInput("Range", $this->plugin->getConfig()->get("range"));
        $player->sendForm($form);
    }
    public function TeleportForms(Player $sender){
        $form = new CustomForm(function (Player $sender, $data){
            if($result === null){
                return;
            }
            if(!is_bool($result[1])){
                $this->TPBool($sender);
                return true;
            }
            $this->plugin->getConfig()->set("teleport", (bool)$result[1]);
            $this->plugin->getConfig()->save();
            $this->TPSuccess($sender);
        });
        $form->setTitle("WorldBorder - Teleport");
        $form->addLabel("Configure whether or not the border teleports you to a safe location.");
        $form->addInput("Teleport", (bool)$this->plugin->getConfig()->get("teleport"));
        $player->sendForm($form);
    }
    public function DefLevelSpawnForms(Player $sender){
        $form = new CustomForm(function (Player $sender, $data){
            if($result === null){
                return;
            }
            if(!is_bool($result[1])){
                $this->DLSBool($sender);
                return true;
            }
            $this->plugin->getConfig()->set("def-level-spawn", (bool)$result[1]);
            $this->plugin->getConfig()->save();
            $this->DLSSuccess($sender);
            return true;

        });
        $form->setTitle("WorldBorder - Default Level Spawn");
        $form->addLabel("Configure whether or not the range depends on the default level spawnpoint.");
        $form->addInput("Default Level Spawn", (bool)$this->plugin->getConfig()->get("def-level-spawn"));
        $player->sendForm($form);
    }
    public function SpawnLocationForms(Player $sender){
        $form = new CustomForm(function (Player $sender, $data){
            if($result === null){
                return;
            }
            if(!is_bool($result[1])){
                $this->SLBool($sender);
                return true;
            }
            $this->plugin->getConfig()->set("spawn-location", (bool)$result[1]);
            $this->plugin->getConfig()->save();
            $this->SLSuccess($sender);
            return true;
        });
        $form->setTitle("WorldBorder - Spawn-Location");
        $form->addLabel("Configure whether or not the range depends on spawn location. (/setworldspawn)");
        $form->addInput("Spawn Location", (bool)$this->plugin->getConfig()->get("spawn-location"));
        $player->sendForm($form);
    }
    public function CoordinatesForms(Player $sender){
       
        $form = new CustomForm(function (Player $sender, $data){
            if($result === null){
                return;
            }
            
            $this->plugin->getConfig()->set("coordinates", (int)$result[1], (int)$result[2], (int)$result[3]);
            $this->plugin->getConfig()->save();
            $this->CoordsSuccess($sender);
            return true;
        });
        $coords = explode(", ", $this->config->get("coordinates"));
        $x = $coords[0];
        $y = $coords[1];
        $z = $coords[2];
        $form->setTitle("WorldBorder - Coordinates");
        $form->addLabel("Configure the X-Y-Z coordinates which the range depends on. (If def-level-spawn & spawn-location is set to false.)");
        $form->addInput("X", $x);
        $form->addInput("Y", $y);
        $form->addInput("Z", $z);
        $player->sendForm($form);
    }
    //here comes the messages boi!
    //todo seperate the messages to a seperate class.
    public function SLBool(Player $sender){
        $form = new SimpleForm(function(Player $player, $data){
			if($data === null){
				return;
			}
        });
        $form->setTitle("WorldBorder - Spawn Location Error");
        $form->setContent("Argument 1 mut be a bool. Please try using /$commandLabel spawn-location <bool: bool>");
        $form->addButton("Submit");
        $player->sendForm($form);
    }
    public function SLSuccess(Player $player){
        $form = new SimpleForm(function(Player $player, $data){
			if($data === null){
				return;
			}
        });
        $form->setTitle("WorldBorder - Spawn Location Success");
        $form->setContent("Spawn Location has been set successfully.");
        $form->addButton("Submit");
        $player->sendForm($form);
    }
    public function DLSBool(Player $player){
        $form = new SimpleForm(function(Player $player, $data){
			if($data === null){
				return;
			}
        });
        $form->setTitle("WorldBorder - Default Spawn Location Error");
        $form->setContent("Argument 1 mut be a bool. Please try using /$commandLabel def-level-spawn <bool: bool>");
        $form->addButton("Submit");
        $player->sendForm($form);
    }
    public function DLSSuccess(Player $player){
        $form = new SimpleForm(function(Player $player, $data){
			if($data === null){
				return;
			}
        });
        $form->setTitle("WorldBorder - Default Level Spawn Success");
        $form->setContent("Default level spawn has been set with success.");
        $form->addButton("Submit");
        $player->sendForm($form);
    }
    public function TPBool(Player $player){
        $form = new SimpleForm(function(Player $player, $data){
			if($data === null){
				return;
			}
        });
        $form->setTitle("WorldBorder - Teleport Error");
        $form->setContent("Argument 1 mut be a bool. Please try using /$commandLabel teleport <bool: bool>");
        $form->addButton("Submit");
        $player->sendForm($form);
    }
    public function TPSuccess(Player $player){
        $form = new SimpleForm(function(Player $player, $data){
			if($data === null){
				return;
			}
        });
        $form->setTitle("WorldBorder - Teleport Success");
        $form->setContent("Teleportation option has been set with success.");
        $form->addButton("Submit");
        $player->sendForm($form);
    }
    public function RangeString(Player $player){
        $form = new SimpleForm(function(Player $player, $data){
			if($data === null){
				return;
			}
        });
        $form->setTitle("WorldBorder - Range String Error");
        $form->setContent("Argument 1 mut be a int. Please try using /$commandLabel range <int>");
        $form->addButton("Submit");
        $player->sendForm($form);
    }
    public function RangeSuccess(Player $player){
        $form = new SimpleForm(function(Player $player, $data){
			if($data === null){
				return;
			}
        });
        $form->setTitle("WorldBorder - Range Success");
        $form->setContent("Range has been set with success!");
        $form->addButton("Submit");
        $player->sendForm($form);
    }
    public function WorldSuccess(Player $player){
        $form = new SimpleForm(function(Player $player, $data){
			if($data === null){
				return;
			}
        });
        $form->setTitle("WorldBorder - World Success");
        $form->setContent("Worlds have been set with success");
        $form->addButton("Submit");
        $player->sendForm($form);
    }


}