<?php

declare(strict_types=1);

namespace Wertzui123\BedrockClans\events\clan;

use pocketmine\event\CancellableTrait;
use Wertzui123\BedrockClans\Clan;
use pocketmine\event\Cancellable;

class ClanColorChangeEvent extends ClanEvent implements Cancellable
{
    use CancellableTrait;

    /** @var string */
    private $oldColor;
    /** @var string */
    private $newColor;

    /**
     * ClanColorChangeEvent constructor.
     * @param Clan $clan
     * @param string $oldColor
     * @param string $newColor
     */
    public function __construct(Clan $clan, string $oldColor, string $newColor)
    {
        parent::__construct($clan);
        $this->oldColor = $oldColor;
        $this->newColor = $newColor;
    }

    /**
     * Returns the clan whose color is changing
     * @return Clan
     */
    public function getClan(): Clan
    {
        return $this->clan;
    }

    /**
     * Returns the old color
     * @return string
     */
    public function getOldColor(): string
    {
        return $this->oldColor;
    }

    /**
     * Returns the new color
     * @return string
     */
    public function getNewColor(): string
    {
        return $this->newColor;
    }

}