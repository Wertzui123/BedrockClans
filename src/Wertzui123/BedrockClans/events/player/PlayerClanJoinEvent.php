<?php

declare(strict_types=1);

namespace Wertzui123\BedrockClans\events\player;

use pocketmine\event\CancellableTrait;
use Wertzui123\BedrockClans\Clan;
use pocketmine\event\Cancellable;
use pocketmine\player\Player;

class PlayerClanJoinEvent extends PlayerEvent implements Cancellable
{
    use CancellableTrait;

    /** @var Clan */
    private $clan;

    /**
     * PlayerClanJoinEvent constructor.
     * @param Player $player
     * @param Clan $clan
     */
    public function __construct(Player $player, Clan $clan)
    {
        parent::__construct($player);
        $this->clan = $clan;
    }

    /**
     * Returns the clan that the player joined
     * @return Clan
     */
    public function getClan(): Clan
    {
        return $this->clan;
    }

}