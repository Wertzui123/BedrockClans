<?php

namespace Wertzui123\BedrockClans\events\player;

use pocketmine\event\Event;
use pocketmine\Player;

class PlayerEvent extends Event
{

    /** @var Player */
    protected $player;

    /**
     * PlayerEvent constructor.
     * @param Player $player
     */
    public function __construct(Player $player)
    {
        $this->player = $player;
    }

    /**
     * @return Player
     */
    public function getPlayer()
    {
        return $this->player;
    }

}