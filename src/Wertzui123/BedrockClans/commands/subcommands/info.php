<?php

namespace Wertzui123\BedrockClans\commands\subcommands;

use pocketmine\command\CommandSender;
use pocketmine\Player;
use Wertzui123\BedrockClans\Main;

class info extends Subcommand
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
        if (!isset($args[0])) {
            if (!$player->isInClan()) {
                $sender->sendMessage($this->plugin->getMessage("info_not_in_clan"));
                return;
            }

            $clan = $player->getClan();
            $members = $clan->getMembersWithRealName();
            $leader = $clan->getLeaderWithRealName();
            $cname = $clan->getName();
            $memberss = implode(", ", $members);
            $m = str_replace("{clanname}", $cname, $this->plugin->getMessage("info_info_about_your_clan"));
            $m = str_replace("{leader}", $leader, $m);
            $m = str_replace("{members}", $memberss, $m);
            $sender->sendMessage($m);
        } else {

            $cname = implode(' ', $args);

            if (!$this->plugin->clanExist($cname)) {
                $sender->sendMessage($this->plugin->getMessage("info_clan_does_not_exist"));
                return;
            }

            $clan = $this->plugin->getClan($cname);
            $members = $clan->getMembersWithRealName();
            $leader = $clan->getLeaderWithRealName();
            $cname = $clan->getName();
            $memberss = implode(", ", $members);
            $m = str_replace("{clanname}", $cname, $this->plugin->getMessage("info_info_about_an_other_clan"));
            $m = str_replace("{leader}", $leader, $m);
            $m = str_replace("{members}", $memberss, $m);
            $sender->sendMessage($m);
        }
    }

}