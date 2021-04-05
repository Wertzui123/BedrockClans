<?php

namespace Wertzui123\BedrockClans\tasks;

use pocketmine\scheduler\Task;
use Wertzui123\BedrockClans\BCPlayer;
use Wertzui123\BedrockClans\Main;

class InviteTask extends Task
{

    private $plugin;
    private $ticks;
    private $sender;
    private $target;

    /**
     * InviteTask constructor.
     * @param Main $plugin
     * @param BCPlayer $sender
     * @param BCPlayer $target
     * @param int $ticks
     */
    public function __construct(Main $plugin, BCPlayer $sender, BCPlayer $target, int $ticks)
    {
        $this->plugin = $plugin;
        $this->ticks = $ticks;
        $this->sender = $sender;
        $this->target = $target;
    }

    public function onRun(int $currentTick)
    {
        if ($this->ticks > 0) {
            $this->ticks--;
        } else {
            if (is_null($this->sender) || is_null($this->target) || is_null($this->sender->getClan())) return;
            if ($this->sender->getClan()->isInvited($this->target)) {
                $this->plugin->expire($this->sender, $this->target);
            }
        }
    }

}