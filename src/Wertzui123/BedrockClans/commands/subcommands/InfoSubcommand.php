<?php

declare(strict_types=1);

namespace Wertzui123\BedrockClans\commands\subcommands;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class InfoSubcommand extends Subcommand
{

    public function canUse(CommandSender $sender): bool
    {
        return $sender instanceof Player;
    }

    public function execute(CommandSender $sender, array $args)
    {
        $player = $this->plugin->getPlayer($sender);
        if (isset($args[0])) {
            if (!$this->plugin->clanExists(implode(' ', $args))) {
                $sender->sendMessage($this->plugin->getMessage('command.info.invalidClan'));
                return;
            }
            $clan = $this->plugin->getClan(implode(' ', $args));
        } else {
            if (!$player->isInClan()) {
                $sender->sendMessage($this->plugin->getMessage('command.info.noClan'));
                return;
            }
            $clan = $player->getClan();
        }
        $sender->sendMessage($this->plugin->getMessage('command.info.success', ['{name}' => $clan->getDisplayName(), '{creation_date}' => $clan->getCreationDate() < 0 ? $this->plugin->getConfig()->get('date_unknown') : date($this->plugin->getConfig()->get('date_format'), $clan->getCreationDate()), '{leader}' => $clan->getLeaderWithRealName(), '{members}' => implode(', ', $clan->getMembersWithRealName(true)), '{bank}' => $clan->getBank()]));
    }

}