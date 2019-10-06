<?php

namespace Wertzui123\BedrockClans\commands\subcommands;

use pocketmine\command\CommandSender;
use pocketmine\Player;
use Wertzui123\BedrockClans\Main;

class join extends Subcommand
{

    private $plugin;

    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
    }

    public function canUse(CommandSender $sender): bool
    {
        return $sender instanceof Player && $sender->hasPermission("bedrockclans.cmd.join");
    }

    public function execute(CommandSender $sender, array $args)
    {
        $player = $this->plugin->getPlayer($sender);
        if (!isset($args[0])) {
            $sender->sendMessage($this->plugin->getMessage("join_provide_clan"));
            return;
        }
        if (!$this->plugin->clanExist($args[0])) {
            $sender->sendMessage($this->plugin->getMessage("join_clan_does_not_exist"));
            return;
        }
        if ($player->isInClan()) {
            $sender->sendMessage($this->plugin->getMessage("join_already_in_clan"));
            return;
        }
        $clan = $this->plugin->getClan($args[0]);
        $this->plugin->joinClan($player, $clan);
        $jc = str_replace("{clan}", $args[0], $this->plugin->getMessage("join_joined_clan"));
        $sender->sendMessage($jc);
    }

}