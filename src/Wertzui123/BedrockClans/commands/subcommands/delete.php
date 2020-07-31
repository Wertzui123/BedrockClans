<?php

namespace Wertzui123\BedrockClans\commands\subcommands;

use pocketmine\command\CommandSender;
use pocketmine\Player;
use Wertzui123\BedrockClans\Main;

class delete extends Subcommand
{

    private $plugin;

    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
    }

    public function canUse(CommandSender $sender): bool
    {
        return $sender instanceof Player;
    }

    public function execute(CommandSender $sender, array $args)
    {
        $player = $this->plugin->getPlayer($sender);
        if (!$player->isInClan()) {
            $sender->sendMessage($this->plugin->getMessage('command.delete.noClan'));
            return;
        }
        if (!$player->isLeader()) {
            $sender->sendMessage($this->plugin->getMessage('command.delete.notLeader'));
            return;
        }
        if(!$this->plugin->deleteClan($player->getClan())){
            $sender->sendMessage($this->plugin->getMessage('command.delete.cancelled'));
        }
    }

}