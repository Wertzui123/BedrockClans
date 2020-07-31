<?php

namespace Wertzui123\BedrockClans\commands\subcommands;

use pocketmine\command\CommandSender;
use pocketmine\Player;
use Wertzui123\BedrockClans\Main;

class leader extends Subcommand
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
        if($clan->getLeader() === strtolower(implode(' ', $args))){
            $sender->sendMessage($this->plugin->getMessage('command.leader.alreadyLeader'));
            return;
        }
        $clan->setRank($player, 'member');
        $clan->setLeader(implode(' ', $args));
        $clan->setRank(implode(' ', $args), 'leader');
        $sender->sendMessage($this->plugin->getMessage('command.leader.success', ['{player}' => implode(' ', $args)]));
        if (!is_null($p = $this->plugin->getServer()->getPlayerExact($this->plugin->getPlayerName(strtolower(implode(' ', $args)))))) {
            $p->sendMessage($this->plugin->getMessage('clan.leader.newLeader'));
        }
    }

}