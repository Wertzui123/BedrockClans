<?php

declare(strict_types=1);

namespace Wertzui123\BedrockClans\events\clan;

use pocketmine\event\Event;
use Wertzui123\BedrockClans\Clan;

class ClanEvent extends Event
{

    /** @var Clan */
    protected $clan;

    /**
     * ClanEvent constructor.
     * @param Clan $clan
     */
    public function __construct(Clan $clan)
    {
        $this->clan = $clan;
    }

    /**
     * @return Clan
     */
    public function getClan(): Clan
    {
        return $this->clan;
    }

}