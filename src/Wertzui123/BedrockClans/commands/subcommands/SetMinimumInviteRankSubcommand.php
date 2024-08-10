<?php

declare(strict_types=1);

namespace Wertzui123\BedrockClans\commands\subcommands;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use Wertzui123\BedrockClans\Clan;

class SetMinimumInviteRankSubcommand extends Subcommand
{

    public function canUse(CommandSender $sender): bool
    {
        return $sender instanceof Player;
    }

    public function execute(CommandSender $sender, array $args)
    {
        $player = $this->plugin->getPlayer($sender);
        if (!$player->isInClan()) {
            $sender->sendMessage($this->plugin->getMessage('command.setminimuminviterank.noClan'));
            return;
        }
        if (!$player->isLeader()) {
            $sender->sendMessage($this->plugin->getMessage('command.setminimuminviterank.noPermission'));
            return;
        }
        if (empty($args[0])) {
            $sender->sendMessage($this->plugin->getMessage('command.setminimuminviterank.passRank'));
            return;
        }
        if (!in_array(strtolower($args[0]), Clan::getRanks())) {
            $sender->sendMessage($this->plugin->getMessage('command.setminimuminviterank.notFound'));
            return;
        }
        if (!$player->getClan()->setMinimumInviteRank(strtolower($args[0]))) {
            $sender->sendMessage($this->plugin->getMessage('command.setminimuminviterank.cancelled'));
            return;
        }
        $sender->sendMessage($this->plugin->getMessage('command.setminimuminviterank.success', ['{rank}' => strtolower($args[0])]));
    }

}