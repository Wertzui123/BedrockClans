<?php

declare(strict_types=1);

namespace Wertzui123\BedrockClans;

use pocketmine\Player;

class BCPlayer {

    private $plugin;
    private $player;
    private $clan;

    public function __construct(Main $plugin, Player $player, $clan = null)
    {
        $this->plugin = $plugin;
        $this->player = $player;
        $this->clan = $clan ?? $plugin->getPlayersFile()->get(strtolower($player->getName())) !== false ? $this->plugin->getClan($plugin->getPlayersFile()->get(strtolower($player->getName()))) : null;
    }

    public function getPlayer() : Player{
        return $this->player;
    }

    public function getClan() : ?Clan{
        return $this->clan;
    }

    public function setClan(?Clan $clan){
        $this->clan = $clan;
    }

    public function isInClan() : bool{
        return $this->clan !== null;
    }

    public function isLeader() : bool{
        return $this->isInClan() ? $this->getClan()->getLeader() === strtolower($this->getPlayer()->getName()) : false;
    }

    public function save(){
        $cfg = $this->plugin->getPlayersFile();
        $cfg->set(strtolower($this->getPlayer()->getName()), $this->getClan() === null ? $this->getClan() : $this->getClan()->getName());
        $cfg->save();
    }

}