<?php

declare(strict_types=1);

namespace iZeaoGamer\WorldBorder\form;

use iZeaoGamer\WorldBorder\Main;
use pocketmine\form\Form;
use pocketmine\Player;

class SimpleForm implements Form {

    public const FORM_MENU = 0;

    /** @var array $formData */
    public $data = [];

    /** @var int $id */
    public $id;

    /**
     * Form constructor.
     * @param string $title
     * @param string $content
     */
    public function __construct(string $title = "TITLE", string $content = "Content") {
        $this->data["type"] = "form";
        $this->setTitle($title);
        $this->setContent($content);
    }

    /**
     * @param string $text
     */
    public function setTitle(string $text) {
        $this->data["title"] = $text;
    }

    /**
     * @param string $text
     */
    public function setContent(string $text) {
        $this->data["content"] = $text;
    }


    /**
     * @param string $text
     */
    public function addButton(string $text) {
        $this->data["buttons"][] = ["text" => $text];
    }

    /**
     * @param Player $player
     * @param mixed $data
     */
    public function handleResponse(Player $player, $data): void {
        Main::getInstance()->formManager->handleFormResponse($player, $data, $this);
    }

    /**
     * @return array
     */
    public function jsonSerialize() {
        return $this->data;
    }
}