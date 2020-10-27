<?php

namespace Wertzui123\BedrockClans\commands\subcommands;

use pocketmine\command\CommandSender;
use pocketmine\Player;
use Wertzui123\BedrockClans\Clan;
use Wertzui123\BedrockClans\Main;

class demote extends Subcommand
{

    /**
     * demote constructor.
     * @param Main $plugin
     */
    public function __construct(Main $plugin)
    {
        parent::__construct($plugin);
    }

    public function canUse(CommandSender $sender): bool
    {
        return $sender instanceof Player;
    }

    public function execute(CommandSender $sender, array $args)
    {
        $player = $this->plugin->getPlayer($sender);
        if (!$player->isInClan()) {
            $sender->sendMessage($this->plugin->getMessage('command.demote.noClan'));
            return;
        }
        $clan = $player->getClan();
        if (strtolower($sender->getName()) !== $clan->getLeader()) {
            $sender->sendMessage($this->plugin->getMessage('command.demote.notLeader'));
            return;
        }
        if (!isset($args[0])) {
            $sender->sendMessage($this->plugin->getMessage('command.demote.passPlayer'));
            return;
        }
        if (!in_array(strtolower(implode(' ', $args)), $clan->getMembers())) {
            $sender->sendMessage($this->plugin->getMessage('command.demote.notInClan', ['{player}' => implode(' ', $args)]));
            return;
        }
        if ($clan->getRank(implode(' ', $args)) === 'leader' || $clan->getRank(implode(' ', $args)) === 'member') {
            $sender->sendMessage($this->plugin->getMessage('command.demote.alreadyLowest'));
            return;
        }
        if ($clan->getRank(implode(' ', $args)) === "vim") {
            $rank = "member";
        } else {
            $rank = "vim";
        }
        $clan->setRank(implode(' ', $args), $rank);
        $sender->sendMessage($this->plugin->getMessage('command.demote.success', ['{player}' => implode(' ', $args), '{rank}' => Clan::getRankName($rank, true)]));
        if (!is_null($p = $this->plugin->getServer()->getPlayerExact($this->plugin->getPlayerName(strtolower(implode(' ', $args)))))) {
            $p->sendMessage($this->plugin->getMessage('clan.demote.demoted', ['{rank}' => Clan::getRankName($rank, true)]));
        }
    }

}