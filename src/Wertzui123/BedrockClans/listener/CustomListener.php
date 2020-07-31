<?php

declare(strict_types=1);

namespace Wertzui123\BedrockClans\listener;

use Wertzui123\BedrockClans\Main;
use pocketmine\event\Listener;

class CustomListener implements Listener
{

    private $plugin;

    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
    }

}