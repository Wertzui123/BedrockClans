<?php

namespace Wertzui123\BedrockClans\commands\subcommands;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use Wertzui123\BedrockClans\events\player\ClanLeaveEvent;
use Wertzui123\BedrockClans\Main;

class leave extends Subcommand
{

    /**
     * leave constructor.
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
            $sender->sendMessage($this->plugin->getMessage('command.leave.noClan'));
            return;
        }
        if (strtolower($sender->getName()) !== $player->getClan()->getLeader()) {
            $clan = $player->getClan();
            $event = new ClanLeaveEvent($player->getPlayer(), $clan);
            $event->call();
            if ($event->isCancelled()) return; // TODO: Message
            $player->setClan(null);
            $clan->removeMember($player);
            $sender->sendMessage($this->plugin->getMessage('command.leave.success'));
            foreach ($clan->getMembers() as $member) {
                if (($p = $this->plugin->getServer()->getPlayerExact($this->plugin->getPlayerName($member)))) {
                    $p->sendMessage($this->plugin->getMessage('clan.leave.members', ['{player}' => $sender->getName()]));
                }
            }
        } else {
            $sender->sendMessage($this->plugin->getMessage('command.leave.leader'));
        }
    }

}