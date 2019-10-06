<?php

namespace Wertzui123\BedrockClans\commands\subcommands;

use pocketmine\command\CommandSender;
use pocketmine\Player;
use Wertzui123\BedrockClans\Main;

class accept extends Subcommand
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
            $sender->sendMessage($this->plugin->getMessage("accept_already_in_clan"));
            return;
        }

        if (!isset($args[0])) {
            $sender->sendMessage($this->plugin->getMessage("accept_provide_clan"));
            return;
        }

        if (!$this->plugin->clanExist($args[0])) {
            $sender->sendMessage($this->plugin->getMessage("accept_clan_does_not_exist"));
            return;
        }

        $clan = $this->plugin->getClan($args[0]);

        if (!$clan->isInvited($player)) {
            $sender->sendMessage($this->plugin->getMessage("accept_not_invited"));
            return;
        }

        $clan->removeInvite($player);
        $message = str_replace("{clan}", $clan->getName(), $this->plugin->getMessage("accept_accepted_invite"));
        $sender->sendMessage($message);
        $this->plugin->joinClan($player, $clan);
    }

}