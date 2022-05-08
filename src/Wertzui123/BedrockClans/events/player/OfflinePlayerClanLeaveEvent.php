<?php

declare(strict_types=1);

namespace Wertzui123\BedrockClans\events\player;

use pocketmine\event\CancellableTrait;
use Wertzui123\BedrockClans\Clan;
use pocketmine\event\Cancellable;

class OfflinePlayerClanLeaveEvent extends OfflinePlayerEvent implements Cancellable
{
    use CancellableTrait;

    /** @var Clan */
    private $clan;

    /**
     * OfflinePlayerClanLeaveEvent constructor.
     * @param string $player
     * @param Clan $clan
     */
    public function __construct(string $player, Clan $clan)
    {
        parent::__construct($player);
        $this->clan = $clan;
    }

    /**
     * Returns the clan that the player left
     * @return Clan
     */
    public function getClan(): Clan
    {
        return $this->clan;
    }

}