<?php

declare(strict_types=1);

namespace Wertzui123\BedrockClans\events\clan;

use pocketmine\event\CancellableTrait;
use Wertzui123\BedrockClans\Clan;
use pocketmine\event\Cancellable;

class ClanMinimumInviteRankChangeEvent extends ClanEvent implements Cancellable
{
    use CancellableTrait;

    /** @var string */
    private $oldRank;
    /** @var string */
    private $newRank;

    /**
     * ClanMinimumInviteRankChangeEvent constructor.
     * @param Clan $clan
     * @param string $oldRank
     * @param string $newRank
     */
    public function __construct(Clan $clan, string $oldRank, string $newRank)
    {
        parent::__construct($clan);
        $this->oldRank = $oldRank;
        $this->newRank = $newRank;
    }

    /**
     * Returns the clan whose minimum invite rank is changing
     * @return Clan
     */
    public function getClan(): Clan
    {
        return $this->clan;
    }

    /**
     * Returns the previous minimum rank required to invite players into the clan
     * @return string
     */
    public function getOldRank(): string
    {
        return $this->oldRank;
    }

    /**
     * Returns the new minimum rank required to invite players into the clan
     * @return string
     */
    public function getNewRank(): string
    {
        return $this->newRank;
    }

}