<?php

namespace Wertzui123\BedrockClans\commands\subcommands;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class JoinSubcommand extends Subcommand
{

    public function canUse(CommandSender $sender): bool
    {
        return $sender instanceof Player && $sender->hasPermission('bedrockclans.command.join');
    }

    public function execute(CommandSender $sender, array $args)
    {
        $player = $this->plugin->getPlayer($sender);
        if ($player->isInClan()) {
            $sender->sendMessage($this->plugin->getMessage('command.join.alreadyInClan'));
            return;
        }
        if (!isset($args[0])) {
            $sender->sendMessage($this->plugin->getMessage('command.join.passClan'));
            return;
        }
        $name = implode(' ', $args);
        if (!$this->plugin->clanExists($name)) {
            $sender->sendMessage($this->plugin->getMessage('command.join.invalidClan'));
            return;
        }
        $clan = $this->plugin->getClan($name);
        $player->joinClan($clan);
        $sender->sendMessage($this->plugin->getMessage('command.join.success', ['{clan}' => $clan->getDisplayName()]));
    }

}