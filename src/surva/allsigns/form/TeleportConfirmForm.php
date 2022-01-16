<?php

namespace surva\allsigns\form;

use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;
use pocketmine\form\Form;
use pocketmine\player\Player;
use pocketmine\world\Position;

class TeleportConfirmForm implements Form
{

    public function __construct(private Position $pos)
    {
    }

    #[Pure] #[ArrayShape(["type" => "string", "title" => "string", "content" => "string", "button1" => "string", "button2" => "string"])]
    public function jsonSerialize(): array
    {
        return [
            "type" => "modal",
            "title" => "テレポート",
            "content" => "本当にテレポートしますか?\n\n" . "x: " . $this->pos->getX() . "\ny: " . $this->pos->getY() . "\nz: " . $this->pos->getZ(),
            "button1" => "はい",
            "button2" => "いいえ"
        ];
    }

    public function handleResponse(Player $player, $data): void
    {
        if ($data === null) return;

        if ($data) {
            $player->teleport($this->pos);
        }
    }
}