<?php

namespace Wertzui123\BedrockClans\tasks;

use pocketmine\Player;
use pocketmine\scheduler\Task;
use Wertzui123\BedrockClans\BCPlayer;
use Wertzui123\BedrockClans\Main;

class invitetask extends Task
{

    public $plugin;
    public $ticks;
    public $sender;
    public $target;

    public function __construct(Main $plugin, BCPlayer $sender, BCPlayer $target, $time)
    {
        $this->plugin = $plugin;
        $this->ticks = $time;
        $this->sender = $sender;
        $this->target = $target;
    }

    public function getPlugin()
    {
        return $this->plugin;
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
