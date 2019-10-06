<?php

namespace Wertzui123\BedrockClans\commands\subcommands;

use pocketmine\command\CommandSender;
use pocketmine\Player;
use Wertzui123\BedrockClans\Main;

class invite extends Subcommand
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
            $sender->sendMessage($this->plugin->getMessage("invite_not_in_clan"));
            return;
        }

        if (!isset($args[0])) {
            $sender->sendMessage($this->plugin->getMessage("invite_provide_player"));
            return;
        }

        if (in_array(strtolower($args[0]), $player->getClan()->getMembers())) {
            $sender->sendMessage($this->plugin->getMessage("invite_in_same_clan"));
            return;
        }

        if (!$this->plugin->getServer()->getPlayerExact($args[0])) {
            $sender->sendMessage($this->plugin->getMessage("invite_player_does_not_exist"));
            return;
        }

        if ($this->plugin->getPlayer($this->plugin->getServer()->getPlayerExact($args[0]))->getClan() === $player->getClan()) {
            $sender->sendMessage($this->plugin->getMessage("invite_already_in_clan"));
            return;
        }

        if($player->getClan()->isInvited($this->plugin->getPlayer($this->plugin->getServer()->getPlayerExact($args[0])))){
            $sender->sendMessage($this->plugin->getMessage('invite_already_invited'));
            return;
        }

        $this->plugin->invite($player, $this->plugin->getPlayer($this->plugin->getServer()->getPlayerExact($args[0])));
        $msg = str_replace("{player}", $this->plugin->getServer()->getPlayerExact($args[0])->getName(), $this->plugin->getMessage("invite_invited"));
        $sender->sendMessage($msg);
    }

}