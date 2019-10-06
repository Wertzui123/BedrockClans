<?php

declare(strict_types=1);

namespace Wertzui123\BedrockClans\events;

use Wertzui123\BedrockClans\Clan;
use Wertzui123\BedrockClans\Main;
use pocketmine\event\plugin\PluginEvent;
use pocketmine\event\Cancellable;
use pocketmine\Player;

// TODO: Use this event

class ClanCreateEvent extends PluginEvent implements Cancellable{

    private $player;
    private $name;
    private $clan;

    public function __construct(Main $plugin, Player $player, $name) {
        parent::__construct($plugin);
        $this->player = $player;
        $this->name = $name;
        $this->clan = new Clan($plugin, $name);
        $plugin->addClan($this->clan);
    }

    public function getPlayer(){
        return $this->player;
    }

    public function getName(){
        return $this->name;
    }

    public function getClan() : Clan{
        return $this->clan;
    }
}
