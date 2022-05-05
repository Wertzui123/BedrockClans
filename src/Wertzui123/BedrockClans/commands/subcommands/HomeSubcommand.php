<?php

namespace Wertzui123\BedrockClans\commands\subcommands;

use pocketmine\command\CommandSender;
use pocketmine\entity\Location;
use pocketmine\player\Player;

class HomeSubcommand extends Subcommand
{

    public function canUse(CommandSender $sender): bool
    {
        return $sender instanceof Player;
    }

    public function execute(CommandSender $sender, array $args)
    {
        if ($this->plugin->getConfig()->getNested('home.enabled') !== true) {
            $sender->sendMessage($this->plugin->getMessage('command.home.disabled'));
            return;
        }
        $player = $this->plugin->getPlayer($sender);
        $clan = $player->getClan();
        if (is_null($clan)) {
            $sender->sendMessage($this->plugin->getMessage('command.home.noClan'));
            return;
        }
        if (is_null($clan->getHome())) {
            $sender->sendMessage($this->plugin->getMessage('command.home.noHome'));
            return;
        } elseif (!$clan->getHome()->isValid() && !$this->plugin->getServer()->getWorldManager()->isWorldGenerated($clan->homeLevel)) {
            $clan->setHome(null);
            $sender->sendMessage($this->plugin->getMessage('command.home.noHome'));
            return;
        }
        if (is_null($clan->getHome()->getWorld())) {
            $this->plugin->getServer()->getWorldManager()->loadWorld($clan->homeLevel);
            $home = $clan->getHome();
            $home = new Location($home->getX(), $home->getY(), $home->getZ(), $this->plugin->getServer()->getWorldManager()->getWorldByName($clan->homeLevel), $home->getYaw(), $home->getPitch());
            $clan->setHome($home);
        }
        $sender->teleport($clan->getHome());
        $sender->sendMessage($this->plugin->getMessage('command.home.success'));
    }

}