<?php

namespace Wertzui123\BedrockClans\commands\subcommands;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use Wertzui123\BedrockClans\Clan;

class PromoteSubcommand extends Subcommand
{

    public function canUse(CommandSender $sender): bool
    {
        return $sender instanceof Player;
    }

    public function execute(CommandSender $sender, array $args)
    {
        $player = $this->plugin->getPlayer($sender);
        if (!$player->isInClan()) {
            $sender->sendMessage($this->plugin->getMessage('command.promote.noClan'));
            return;
        }
        $clan = $player->getClan();
        if (strtolower($sender->getName()) !== $clan->getLeader()) {
            $sender->sendMessage($this->plugin->getMessage('command.promote.notLeader'));
            return;
        }
        if (!isset($args[0])) {
            $sender->sendMessage($this->plugin->getMessage('command.promote.passPlayer'));
            return;
        }
        if (!in_array(strtolower(implode(' ', $args)), $clan->getMembers())) {
            $sender->sendMessage($this->plugin->getMessage('command.promote.notInClan', ['{player}' => implode(' ', $args)]));
            return;
        }
        if ($clan->getRank(implode(' ', $args)) === 'leader' || $clan->getRank(implode(' ', $args)) === 'coleader') {
            $sender->sendMessage($this->plugin->getMessage('command.promote.alreadyHighest'));
            return;
        }
        if ($clan->getRank(implode(' ', $args)) === 'vim') {
            $rank = 'coleader';
        } else {
            $rank = 'vim';
        }
        $member = implode(' ', $args);
        if ($this->plugin->getServer()->getPlayerExact($member) instanceof Player) {
            $member = $this->plugin->getServer()->getPlayerExact($member);
        }
        if (!$clan->setRank($member, $rank)) {
            $sender->sendMessage($this->plugin->getMessage('command.promote.cancelled'));
            return;
        }
        $sender->sendMessage($this->plugin->getMessage('command.promote.success', ['{player}' => implode(' ', $args), '{rank}' => Clan::getRankName($rank, true)]));
        if (!is_null($p = $this->plugin->getServer()->getPlayerExact($this->plugin->getPlayerName(strtolower(implode(' ', $args)))))) {
            $p->sendMessage($this->plugin->getMessage('clan.promote.promoted', ['{rank}' => Clan::getRankName($rank, true)]));
        }
    }

}