<?php

declare(strict_types=1);

namespace iZeaoGamer\WorldBorder\utils;

use iZeaoGamer\WorldBorder\form\CustomForm;
use pocketmine\form\Form;
use iZeaoGamer\WorldBorder\Main;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class FormManager {

    public const FORM_WORLDS = 0;
    public const FORM_RANGE = 1;
    public const FORM_TELEPORT = 2;
    public const FORM_DEFAULT_LEVEL_SPAWN = 3;
    public const FORM_SPAWN_LOCATION = 4;
    public const FORM_COORDINATES = 5;
    /** @var Main $plugin */
    public $plugin;

    /**
     * FormManager constructor.
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    /**
     * @param Player $player
     * @param mixed $data
     * @param Form $form
     */
    public function handleFormResponse(Player $player, $data, Form $form) {
        if($data === null) return;
        $form = new CustomForm("World Border");
        $form->id = $data;

        switch ($data) {
            case FORM_WORLDS:
                $form->addLabel("Configure which worlds the world border should work in.");
        foreach(Main::getInstance()->getConfig()->get("worlds") as $worlds){
        $form->addInput("Worlds", $worlds);
        }
        $player->sendForm($form);
            break;
            case FORM_RANGE:
                $form->addLabel("Configure how far a worldborder distance (in blocks) should be.");
                $form->addInput("Range", Main::getInstance()->getConfig()->get("range"));
                $player->sendForm($form);
            break;
            case FORM_TELEPORT:
                $form->addLabel("Configure whether or not the border teleports you to a safe location.");
        $form->addInput("Teleport", (bool)Main::getInstance()->getConfig()->get("teleport"));
        $player->sendForm($form);
            break;
            case FORM_DEFAULT_LEVEL_SPAWN:
                $form->addLabel("Configure whether or not the range depends on the default level spawnpoint.");
                $form->addInput("Default Level Spawn", (bool)Main::getInstance()->getConfig()->get("def-level-spawn"));
                $player->sendForm($form);
            break;
            case FORM_SPAWN_LOCATION:
                $form->addLabel("Configure whether or not the range depends on spawn location. (/setworldspawn)");
        $form->addInput("Spawn Location", (bool)Main::getInstance()->getConfig()->get("spawn-location"));
        $player->sendForm($form);
            break;
            case FORM_COORDINATES:
                $coords = explode(", ", Main::getInstance()->getConfig()->get("coordinates"));
        $x = $coords[0];
        $y = $coords[1];
        $z = $coords[2];
                $form->addLabel("Configure the X-Y-Z coordinates which the range depends on. (If def-level-spawn & spawn-location is set to false.)");
                $form->addInput("X", $x);
                $form->addInput("Y", $y);
                $form->addInput("Z", $z);
                $player->sendForm($form);
            break;
        }
    }
     /**
     * @param Player $player
     * @param mixed $data
     * @param CustomForm $form
     */
    public function handleCustomFormResponse(Player $player, $data, CustomForm $form) {
        if($data === null) return;
        switch ($form->id) {
            case FORM_WORLDS:
                Main::getInstance()->getConfig()->set("worlds", implode("\n-", $data[1]));
                Main::getInstance()->getConfig()->save();
            $player->sendMesage(TextFormat::colorize("&5Worlds have been set!"));
            break;

            case FORM_RANGE:
                if(is_string($data[1])){
                   $player->sendMessage(TextFormat::colorize("&cArgument 1 must be a int."));
                   return true;
                }
                Main::getInstance()->getConfig()->set("range", (int)$data[1]);
                Main::getInstance()->getConfig()->save();
                $player->sendMessage(TextFormat::colorize("&5Range has been set to &6" . $data[1]));
            break;
            case FORM_TELEPORT:
                if(!is_bool($data[1])){
                    $player->sendMessage(TextFormat::colorize("&cArgument 1 must be a boolean! (true/false)"));
                    return true;
                }
                Main::getInstance()->getConfig()->set("teleport", (bool)$data[1]);
                Main::getInstance()->getConfig()->save();
                $player->sendMessage(TextFormat::colorize("&5Teleportation has been set to &6" . $data[1]));
            break;
            case FORM_DEFAULT_LEVEL_SPAWN:
                if(!is_bool($data[1])){
                    $player->sendMessage(TextFormat::colorize("&cArgument 1 must be a boolean. (true/false)"));
                    return true;
                }
                
                if($data[1] && Main::getInstance()->getConfig()->get("spawn-location")){
                    $player->sendMessage(TextFormat::colorize("&cOne of the options have been set to true, this can't be set to true!"));
                    return true;
                }
            
                Main::getInstance()->getConfig()->set("def-level-spawn", (bool)$data[1]);
                Main::getInstance()->getConfig()->save();
                $player->sendMessage(TextFormat::colorize("&5Default level spawn location has been set to &6" . $data[1]));
            break;
            case FORM_SPAWN_LOCATION:
                if(!is_bool($data[1])){
                    $player->sendMessage(TextFormat::colorize("&cArgument 1 must be a boolean. (true/false)"));
                    return true;
                }
                if($data[1] && Main::getInstance()->getConfig()->get("def-level-spawn")){
                    $player->sendMessage(TextFormat::colorize("&cOne of the options have been set to true, this can't be set to true!"));
                    return true;
                }
                Main::getInstance()->getConfig()->set("spawn-location", (bool)$data[1]);
                Main::getInstance()->getConfig()->save();
                $player->sendMessage(TextFormat::colorize("&5Spawn-Location has been set to &6" . $data[1]));
            break;
            case FORM_COORDINATES:
                Main::getInstance()->getConfig()->set("coordinates", (int)$data[1], (int)$data[2], (int)$data[3]);
                Main::getInstance()->getConfig()->save();
            $player->sendMessage(TextFormat::colorize("&5Coordinates has been set to: &6" . $data[1] . ", " . $data[2] . ", and " . $data[3]));
            break;
        }
    }
}


