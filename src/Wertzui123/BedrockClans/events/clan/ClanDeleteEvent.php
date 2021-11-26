<?php

declare(strict_types=1);

namespace Wertzui123\BedrockClans\events\clan;

use pocketmine\event\CancellableTrait;
use Wertzui123\BedrockClans\Clan;
use pocketmine\event\Cancellable;

class ClanDeleteEvent extends ClanEvent implements Cancellable
{
    use CancellableTrait;

    /**
     * ClanDeleteEvent constructor.
     * @param Clan $clan
     */
    public function __construct(Clan $clan)
    {
        parent::__construct($clan);
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