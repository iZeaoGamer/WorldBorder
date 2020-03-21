<?php

declare(strict_types=1);

namespace iZeaoGamer\WorldBorder\form;

use iZeaoGamer\WorldBorder\Main;
use pocketmine\form\Form;
use pocketmine\Player;

class CustomForm implements Form {

    public const FORM_WORLDS = 1;

    /** @var array $data */
    public $data = [];

    /** @var int $id */
    public $id;

    /**
     * CustomForm constructor.
     * @param string $title
     */
    public function __construct(string $title = "") {
        $this->data["type"] = "custom_form";
        $this->data["title"] = $title;
        $this->data["content"] = [];
    }

    /**
     * @param string $text
     */
    public function addInput(string $text) {
        $this->data["content"][] = ["type" => "input", "text" => $text];
    }

    /**
     * @param string $text
     */
    public function addLabel(string $text) {
        $this->data["content"][] = ["type" => "label", "text" => $text];
    }

    /**
     * @param string $text
     * @param string|null $default
     */
    public function addToggle(string $text, ?bool $default = null) {
        if($default!== null) {
            $this->data["content"][] = ["type" => "toggle", "text" => $text, "default" => $default];
            return;
        }
        $this->data["content"][] = ["type" => "toggle", "text" => $text];
    }

    /**
     * @param string $text
     * @param array $options
     */
    public function addDropdown(string $text, array $options) {
        $this->data["content"][] = ["type" => "dropdown", "text" => $text, "options" => $options];
    }

    /**
     * @param Player $player
     * @param mixed $data
     */
    public function handleResponse(Player $player, $data): void {
     Main::getInstance()->formManager->handleCustomFormResponse($player, $data, $this);
    }

    public function jsonSerialize(): array {
        return $this->data;
    }
}