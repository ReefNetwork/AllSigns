<?php
/**
 * AllSigns | create/edit teleport sign form
 */

namespace surva\allsigns\form;

use pocketmine\form\Form;
use pocketmine\player\Player;
use surva\allsigns\AllSigns;
use surva\allsigns\sign\TeleportSign;

class TeleportSignForm implements Form
{

    private AllSigns $allSigns;

    private TeleportSign $sign;

    private string $type = "custom_form";

    private string $title;

    private array $content;

    /**
     * @param  \surva\allsigns\AllSigns  $allSigns
     * @param  \surva\allsigns\sign\TeleportSign  $teleportSign
     */
    public function __construct(AllSigns $allSigns, TeleportSign $teleportSign)
    {
        $this->allSigns = $allSigns;
        $this->sign     = $teleportSign;

        $existingData = $this->sign->getData();

        $defaultWorld = "world";

        if (($wld = $this->sign->getSignBlock()->getPosition()->getWorld()) !== null) {
            $defaultWorld = $wld->getFolderName();
        }

        $this->title   = $allSigns->getMessage("form.teleportsign.title");
        $this->content = [
          [
            "type"    => "input",
            "text"    => $allSigns->getMessage("form.teleportsign.xc"),
            "default" => $existingData !== null ? $existingData["settings"]["xc"] : "",
          ],
          [
            "type"    => "input",
            "text"    => $allSigns->getMessage("form.teleportsign.yc"),
            "default" => $existingData !== null ? $existingData["settings"]["yc"] : "",
          ],
          [
            "type"    => "input",
            "text"    => $allSigns->getMessage("form.teleportsign.zc"),
            "default" => $existingData !== null ? $existingData["settings"]["zc"] : "",
          ],
          [
            "type"    => "input",
            "text"    => $allSigns->getMessage("form.commandsign.text"),
            "default" => $existingData !== null ? $existingData["settings"]["text"] : "",
          ],
        ];
    }

    /**
     * Getting a response from the client form
     *
     * @param  \pocketmine\player\Player  $player
     * @param  mixed  $data
     */
    public function handleResponse(Player $player, $data): void
    {
        if (!is_array($data)) {
            return;
        }

        if (count($data) !== 4) {
            return;
        }

        $signData = [
          "world" => $player->getWorld()->getFolderName(),
          "xc"    => $data[0],
          "yc"    => $data[1],
          "zc"    => $data[2],
        ];

        $text       = $data[3];
        $permission = "";

        if ($this->sign->createSign($signData, $text, $permission)) {
            $player->sendMessage($this->allSigns->getMessage("form.teleportsign.success"));
        } else {
            $player->sendMessage($this->allSigns->getMessage("form.teleportsign.error"));
        }
    }

    /**
     * Return JSON data of the form
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
          "type"    => $this->type,
          "title"   => $this->title,
          "content" => $this->content,
        ];
    }

}
