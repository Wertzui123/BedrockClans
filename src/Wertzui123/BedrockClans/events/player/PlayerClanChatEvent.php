<?php

declare(strict_types=1);

namespace Wertzui123\BedrockClans\events\player;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\player\Player;

class PlayerClanChatEvent extends PlayerEvent implements Cancellable
{
    use CancellableTrait;

    /** @var string */
    private $message;

    /**
     * ClanChatEvent constructor.
     * @param Player $player
     * @param string $message
     */
    public function __construct(Player $player, $message)
    {
        parent::__construct($player);
        $this->message = $message;
    }

    /**
     * Returns the clan chat message
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Updates the clan chat message
     * @param string $message
     */
    public function setMessage(string $message)
    {
        $this->message = $message;
    }

}