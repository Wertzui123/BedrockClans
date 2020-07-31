<?php

namespace Wertzui123\BedrockClans\tasks;

use pocketmine\scheduler\Task;
use Wertzui123\BedrockClans\BCPlayer;
use Wertzui123\BedrockClans\Main;

class invitetask extends Task
{

    public $plugin;
    public $ticks;
    public $sender;
    public $target;

    public function __construct(Main $plugin, BCPlayer $sender, BCPlayer $target, $ticks)
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
        }else{
            if(is_null($this->sender->getClan())) return;
            if($this->sender->getClan()->isInvited($this->target)){
                $this->plugin->expire($this->sender, $this->target);
            }
        }
    }
}
