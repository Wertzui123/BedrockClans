<?php

declare(strict_types=1);

namespace Wertzui123\BedrockClans\events\player;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\player\Player;

class PlayerRankChangeEvent extends PlayerEvent implements Cancellable
{
    use CancellableTrait;

    /** @var string */
    private $oldRank;
    /** @var string */
    private $newRank;

    /**
     * PlayerRankChangeEvent constructor.
     * @param Player $player
     * @param string $oldRank
     * @param string $newRank
     */
    public function __construct(Player $player, string $oldRank, string $newRank)
    {
        parent::__construct($player);
        $this->oldRank = $oldRank;
        $this->newRank = $newRank;
    }

    /**
     * Returns the old rank
     * @return string
     */
    public function getOldRank(): string
    {
        return $this->oldRank;
    }

    /**
     * Returns the new rank
     * @return string
     */
    public function getNewRank(): string
    {
        return $this->newRank;
    }

}