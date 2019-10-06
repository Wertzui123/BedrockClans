<?php

declare(strict_types=1);

namespace Wertzui123\BedrockClans\listener;

use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use Wertzui123\BedrockClans\Main;
use pocketmine\event\Listener;

class EventListener implements Listener
{
    private $plugin;

    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
    }

    public function onLogin(PlayerLoginEvent $event)
    {
        $player = $event->getPlayer();
        if ($this->plugin->getPlayersFile()->get(strtolower($player->getName())) === false) {
            $this->plugin->getPlayersFile()->set(strtolower($player->getName()), null);
            $this->plugin->getPlayersFile()->save();
        }
        $this->plugin->getPlayerNames()->set(strtolower($player->getName()), $player->getName());
        $this->plugin->getPlayerNames()->save();
        $this->plugin->addPlayer($player);
    }

    public function onQuit(PlayerQuitEvent $event){
        $this->plugin->removePlayer($this->plugin->getPlayer($event->getPlayer()));
    }
}