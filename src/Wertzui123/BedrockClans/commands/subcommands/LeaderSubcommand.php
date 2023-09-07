<?php

declare(strict_types=1);

namespace Wertzui123\BedrockClans\commands\subcommands;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class LeaderSubcommand extends Subcommand
{

    public function canUse(CommandSender $sender): bool
    {
        return $sender instanceof Player;
    }

    public function execute(CommandSender $sender, array $args)
    {
        $player = $this->plugin->getPlayer($sender);
        if (!$player->isInClan()) {
            $sender->sendMessage($this->plugin->getMessage('command.leader.noClan'));
            return;
        }
        $clan = $player->getClan();
        if (strtolower($sender->getName()) !== $clan->getLeader()) {
            $sender->sendMessage($this->plugin->getMessage('command.leader.notLeader'));
            return;
        }
        if (!isset($args[0])) {
            $sender->sendMessage($this->plugin->getMessage('command.leader.passPlayer'));
            return;
        }
        if (!in_array(strtolower(implode(' ', $args)), $clan->getMembers())) {
            $sender->sendMessage($this->plugin->getMessage('command.leader.notInClan', ['{player}' => implode(' ', $args)]));
            return;
        }
        if ($clan->getLeader() === strtolower(implode(' ', $args))) {
            $sender->sendMessage($this->plugin->getMessage('command.leader.alreadyLeader'));
            return;
        }
        if (!$clan->setRank($player, 'member')) {
            $sender->sendMessage($this->plugin->getMessage('command.leader.cancelled'));
            return;
        }
        $member = implode(' ', $args);
        if ($this->plugin->getServer()->getPlayerExact($member) instanceof Player) {
            $member = $this->plugin->getServer()->getPlayerExact($member);
        }
        if (!$clan->setRank($member, 'leader')) {
            $sender->sendMessage($this->plugin->getMessage('command.leader.cancelled'));
            $clan->setRank($player, 'member', true);
            return;
        }
        $clan->setLeader(implode(' ', $args));
        $sender->sendMessage($this->plugin->getMessage('command.leader.success', ['{player}' => implode(' ', $args)]));
        if (!is_null($p = $this->plugin->getServer()->getPlayerExact($this->plugin->getPlayerName(strtolower(implode(' ', $args)))))) {
            $p->sendMessage($this->plugin->getMessage('clan.leader.newLeader'));
        }
    }

}