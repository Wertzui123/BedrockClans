<?php

namespace Wertzui123\BedrockClans\tasks;

use pocketmine\Player;
use pocketmine\scheduler\Task;
use Wertzui123\BedrockClans\BCPlayer;
use Wertzui123\BedrockClans\Main;

class invitetask extends Task
{

    public $plugin;
    public $seconds;
    public $sender;
    public $target;

    public function __construct(Main $plugin, BCPlayer $sender, BCPlayer $target,  $time)
    {
        $this->plugin = $plugin;
        $this->seconds = $time;
        $this->sender = $sender;
        $this->target = $target;
    }

    public function getPlugin()
    {
        return $this->plugin;
    }

    public function onRun(int $currentTick)
    {

        if ($this->seconds != 0) {
            $this->seconds--;
        }else{
            if(is_null($sender->getClan())) return;
            if($this->sender->getClan()->isInvited($this->target)){
                $this->plugin->expire($this->sender, $this->target);
            }
        }
    }
}
