<?php

declare(strict_types=1);

namespace Wertzui123\BedrockClans\events\clan;

use Wertzui123\BedrockClans\Clan;
use pocketmine\event\Cancellable;
use pocketmine\Player;

class ClanCreateEvent extends ClanEvent implements Cancellable
{

    /** @var Player */
    private $player;

    /**
     * ClanCreateEvent constructor.
     * @param Clan $clan
     * @param Player $player
     */
    public function __construct(Clan $clan, Player $player)
    {
        parent::__construct($clan);
        $this->player = $player;
    }

    /**
     * Returns the player who is creating the clan
     * @return Player
     */
    public function getPlayer(): Player
    {
        return $this->player;
    }

    /**
     * Returns the new clan
     * @return Clan
     */
    public function getClan(): Clan
    {
        return $this->clan;
    }

}