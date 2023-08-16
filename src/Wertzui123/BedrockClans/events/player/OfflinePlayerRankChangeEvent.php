<?php

declare(strict_types=1);

namespace Wertzui123\BedrockClans\events\player;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;

class OfflinePlayerRankChangeEvent extends OfflinePlayerEvent implements Cancellable
{
    use CancellableTrait;

    /** @var string */
    private $oldRank;
    /** @var string */
    private $newRank;

    /**
     * OfflinePlayerRankChangeEvent constructor.
     * @param string $player
     * @param string $oldRank
     * @param string $newRank
     */
    public function __construct(string $player, $oldRank, $newRank)
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