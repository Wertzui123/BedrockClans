<?php

namespace Wertzui123\BedrockClans\commands\subcommands;

use pocketmine\command\CommandSender;
use pocketmine\Player;
use Wertzui123\BedrockClans\Main;

class leader extends Subcommand
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
            $sender->sendMessage($this->plugin->getMessage("leader_not_in_clan"));
            return;
        }
        $clan = $player->getClan();
        if (strtolower($sender->getName()) !== $clan->getLeader()) {
            $sender->sendMessage($this->plugin->getMessage("leader_not_leader"));
            return;
        }
        if (!isset($args[0])) {
            $sender->sendMessage($this->plugin->getMessage("leader_provide_player"));
            return;
        }
        if (!in_array(strtolower(implode(' ', $args)), $clan->getMembers())) {
            $message = str_replace("{player}", implode(' ', $args), $this->plugin->getMessage("leader_not_in_your_clan"));
            $sender->sendMessage($message);
            return;
        }
        $clan->setLeader(implode(' ', $args));
        $ps = str_replace("{player}", implode(' ', $args), $this->plugin->getMessage("leader_promoted_to_leader"));
        $sender->sendMessage($ps);
        if (($p = $this->plugin->getServer()->getPlayerExact($this->plugin->getPlayerName(strtolower(implode(' ', $args)))))) {
            $target = $this->plugin->getServer()->getPlayerExact(implode(' ', $args));
            $target->sendMessage($this->plugin->getMessage("leader_now_leader"));
        }
    }

}