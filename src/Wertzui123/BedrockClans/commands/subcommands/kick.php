<?php

namespace Wertzui123\BedrockClans\commands\subcommands;

use pocketmine\command\CommandSender;
use pocketmine\Player;
use Wertzui123\BedrockClans\Main;

class kick extends Subcommand
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
        if (!isset($args[0])) {
            $sender->sendMessage($this->plugin->getMessage("kick_provide_player"));
            return;
        }
        if (!$player->isInClan()) {
            $sender->sendMessage($this->plugin->getMessage("kick_not_in_clan"));
            return;
        }

        $clan = $player->getClan();
        $kicked = strtolower($args[0]);
        if (!$player->isLeader()) {
            $sender->sendMessage($this->plugin->getMessage("kick_not_leader"));
            return;
        }

        if ($clan->getLeader() === $kicked) {
            $sender->sendMessage($this->plugin->getMessage("kick_cannot_kick_leader"));
            return;
        }

        if (!in_array($kicked, $clan->getMembers())) {
            $sender->sendMessage($this->plugin->getMessage("kick_player_not_exists"));
            return;
        }

        if (($k = $this->plugin->getServer()->getPlayerExact($kicked))) {
            $k = $this->plugin->getPlayer($k);
            $clan = $k->getClan();
            $k->setClan(null);
            $clan->removeMember($k);
            $message = str_replace("{player}", $sender->getName(), $this->plugin->getMessage("kick_kicked"));
            $k->getPlayer()->sendMessage($message);
            $msg = str_replace("{player}", $k->getPlayer()->getName(), $this->plugin->getMessage("kick_kicked_sender"));
            $sender->sendMessage($msg);
        } else {
            $k = $kicked;
            $clan = $this->plugin->getClanByPlayer($k);
            $this->plugin->setClan($k, null);
            $clan->removeMember($k);
            $msg = str_replace("{player}", $kicked, $this->plugin->getMessage("kick_kicked_sender"));
            $sender->sendMessage($msg);
        }
    }

}