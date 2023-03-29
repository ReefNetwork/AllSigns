<?php

namespace surva\allsigns\form;

use JetBrains\PhpStorm\ArrayShape;
use pocketmine\form\Form;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;
use surva\allsigns\AllSigns;

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
            $xuid = $player->getXuid();
            if (isset(AllSigns::$instance->coolTime[$xuid])) {
                $player->sendMessage("再度コマンド看板を実行するには5秒待ってください");
                return;
            }
            AllSigns::$instance->coolTime[$xuid] = $xuid;
            AllSigns::$instance->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($xuid): void {
                unset(AllSigns::$instance->coolTime[$xuid]);
            }), 100);
            $player->getServer()->dispatchCommand($player, $this->command);
        }
    }
}
