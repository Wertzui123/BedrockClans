<?php

namespace Wertzui123\BedrockClans\commands\subcommands;

use pocketmine\command\CommandSender;
use pocketmine\Player;
use Wertzui123\BedrockClans\Main;

class leave extends Subcommand
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
        if ($player->isInClan()) {
            if (strtolower($sender->getName()) !== $player->getClan()->getLeader()) {
                $clan = $player->getClan();
                $player->setClan(null);
                $clan->removeMember($player);
                $sender->sendMessage($this->plugin->getMessage("leave_leaved_clan"));
                foreach ($clan->getMembers() as $member) {
                    if (($p = $this->plugin->getServer()->getPlayerExact($this->plugin->getPlayerName($member)))) {
                        $msg = str_replace("{player}", $sender->getName(), $this->plugin->getMessage("leave_player_leaved_clan"));
                        $p->sendMessage($msg);
                    }
                }
            } else {
                $sender->sendMessage($this->plugin->getMessage("leave_cannot_leave"));
            }
        } else {
            $sender->sendMessage($this->plugin->getMessage("leave_not_in_clan"));
        }
    }

}