<?php

namespace surva\allsigns\form;

use JetBrains\PhpStorm\ArrayShape;
use pocketmine\form\Form;
use pocketmine\player\Player;

class CommandConfirmForm implements Form
{
    public function __construct(private string $command)
    {
    }

    #[ArrayShape(["type" => "string", "title" => "string", "content" => "string", "button1" => "string", "button2" => "string"])]
    public function jsonSerialize(): array
    {
        return [
            "type" => "modal",
            "title" => "コマンド",
            "content" => "本当にコマンドを実行しますか?\n\n/" . $this->command,
            "button1" => "はい",
            "button2" => "いいえ"
        ];
    }

    public function handleResponse(Player $player, $data): void
    {
        if ($data === null) return;

        if ($data) {
            $player->getServer()->dispatchCommand($player, $this->command);
        }
    }
}