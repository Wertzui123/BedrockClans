<?php

namespace Wertzui123\BedrockClans\commands\subcommands;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use Wertzui123\BedrockClans\Main;

class sethome extends Subcommand
{

    /**
     * sethome constructor.
     * @param Main $plugin
     */
    public function __construct(Main $plugin)
    {
        parent::__construct($plugin);
    }

    public function canUse(CommandSender $sender): bool
    {
        return $sender instanceof Player;
    }

    public function execute(CommandSender $sender, array $args)
    {
        if ($this->plugin->getConfig()->getNested('home.enabled') !== true) {
            $sender->sendMessage($this->plugin->getMessage('command.sethome.disabled'));
            return;
        }
        $player = $this->plugin->getPlayer($sender);
        if (!$player->isInClan()) {
            $sender->sendMessage($this->plugin->getMessage('command.sethome.noClan'));
            return;
        }
        $clan = $player->getClan();
        if (strtolower($sender->getName()) !== $clan->getLeader()) {
            $sender->sendMessage($this->plugin->getMessage('command.sethome.notLeader'));
            return;
        }
        $clan->setHome($sender->getLocation());
        $sender->sendMessage($this->plugin->getMessage('command.sethome.success'));
    }

}