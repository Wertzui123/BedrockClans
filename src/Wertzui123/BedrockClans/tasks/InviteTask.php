<?php

namespace Wertzui123\BedrockClans\tasks;

use pocketmine\scheduler\Task;
use Wertzui123\BedrockClans\BCPlayer;
use Wertzui123\BedrockClans\Clan;
use Wertzui123\BedrockClans\Main;

class InviteTask extends Task
{

    private $plugin;
    private $ticks;
    private $sender;
    private $target;
    private $clan;

    /**
     * InviteTask constructor.
     * @param Main $plugin
     * @param BCPlayer $sender
     * @param BCPlayer $target
     * @param Clan $clan
     * @param int $ticks
     */
    public function __construct(Main $plugin, BCPlayer $sender, BCPlayer $target, Clan $clan, int $ticks)
    {
        $this->plugin = $plugin;
        $this->sender = $sender;
        $this->target = $target;
        $this->clan = $clan;
        $this->ticks = $ticks;
    }

    public function onRun(): void
    {
        if ($this->ticks > 0) {
            $this->ticks--;
        } else {
            if ($this->clan->deleted) return;
            if ($this->clan->isInvited($this->target)) {
                $this->plugin->expire($this->sender, $this->target, $this->clan);
            }
        }
    }

}