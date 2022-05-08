<?php

namespace Wertzui123\BedrockClans\events\player;

use pocketmine\event\Event;

class OfflinePlayerEvent extends Event
{

    /** @var string */
    protected $player;

    /**
     * OfflinePlayerEvent constructor.
     * @param string $player
     */
    public function __construct(string $player)
    {
        $this->player = $player;
    }

    /**
     * @return string
     */
    public function getPlayer(): string
    {
        return $this->player;
    }

}