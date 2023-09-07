<?php

declare(strict_types=1);

namespace Wertzui123\BedrockClans\commands\subcommands;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use Wertzui123\BedrockClans\events\player\OfflinePlayerClanLeaveEvent;
use Wertzui123\BedrockClans\events\player\PlayerClanLeaveEvent;

class KickSubcommand extends Subcommand
{

    public function canUse(CommandSender $sender): bool
    {
        return $sender instanceof Player;
    }

    public function execute(CommandSender $sender, array $args)
    {
        $player = $this->plugin->getPlayer($sender);
        if (!$player->isInClan()) {
            $sender->sendMessage($this->plugin->getMessage('command.kick.noClan'));
            return;
        }
        if (!$player->isLeader()) {
            $sender->sendMessage($this->plugin->getMessage('command.kick.notLeader'));
            return;
        }
        if (!isset($args[0])) {
            $sender->sendMessage($this->plugin->getMessage('command.kick.passPlayer'));
            return;
        }
        $clan = $player->getClan();
        $kicked = strtolower(implode(' ', $args));
        if (!in_array($kicked, $clan->getMembers())) {
            $sender->sendMessage($this->plugin->getMessage('command.kick.passPlayer'));
            return;
        }
        if ($clan->getLeader() === $kicked) {
            $sender->sendMessage($this->plugin->getMessage('command.kick.cannotKickLeader'));
            return;
        }
        if (!is_null($k = $this->plugin->getServer()->getPlayerExact($kicked))) {
            $event = new PlayerClanLeaveEvent($k, $clan);
            $event->call();
            if ($event->isCancelled()) {
                $sender->sendMessage($this->plugin->getMessage('command.kick.cancelled'));
                return;
            }
            $k = $this->plugin->getPlayer($k);
            $clan = $k->getClan();
            $k->setClan(null);
            $clan->removeMember($k);
            $sender->sendMessage($this->plugin->getMessage('command.kick.success', ['{player}' => $k->getPlayer()->getName()]));
            $k->getPlayer()->sendMessage($this->plugin->getMessage('clan.kick.kicked', ['{player}' => $sender->getName()]));
        } else {
            $clan = $this->plugin->getClanByPlayer($kicked);
            $event = new OfflinePlayerClanLeaveEvent($kicked, $clan);
            $event->call();
            if ($event->isCancelled()) {
                $sender->sendMessage($this->plugin->getMessage('command.kick.cancelled'));
                return;
            }
            $this->plugin->setClan($kicked, null);
            $clan->removeMember($kicked);
            $sender->sendMessage($this->plugin->getMessage('command.kick.success', ['{player}' => $kicked]));
        }
    }

}