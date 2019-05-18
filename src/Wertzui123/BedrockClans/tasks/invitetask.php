<?php

namespace Wertzui123\BedrockClans\tasks;

use pocketmine\Player;
use pocketmine\scheduler\Task;
use Wertzui123\BedrockClans\Main;

class invitetask extends Task
{

    public $plugin;
    public $seconds;
    public $sender;
    public $target;

    public function __construct(Main $plugin, Player $sender, Player $target,  $time)
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
            //Sends a message to the console with how many seconds the task has been running for
            //Checks if $this->seconds has the same value of 10
            //Adds 1 to $this->seconds
            $this->seconds--;
        }else{
            //Tells the console that the task is being stopped and at how many seconds
            //Calls a function from your Main that removes the task and stops it from running
            $this->plugin->removeTask($this->getTaskId());
            if($this->plugin->isInvited($this->target, $this->plugin->getClan($this->sender))) {
                $this->plugin->expire($this->sender, $this->target);
            }
        }
    }
}
