<?php

declare(strict_types=1);

namespace Wertzui123\BedrockClans\events\player;

use Wertzui123\BedrockClans\Clan;
use pocketmine\event\Cancellable;
use pocketmine\Player;

class ClanJoinEvent extends PlayerEvent implements Cancellable
{

    /** @var Clan */
    private $clan;

    /**
     * ClanJoinEvent constructor.
     * @param Player $player
     * @param Clan $clan
     */
    public function __construct(Player $player, Clan $clan)
    {
        parent::__construct($player);
        $this->clan = $clan;
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