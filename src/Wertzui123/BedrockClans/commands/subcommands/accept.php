<?php

namespace Wertzui123\BedrockClans\commands\subcommands;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use Wertzui123\BedrockClans\Main;

class accept extends Subcommand
{

    /**
     * accept constructor.
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
        if ($player->isInClan()) {
            $sender->sendMessage($this->plugin->getMessage('command.accept.alreadyInClan'));
            return;
        }
        if (!isset($args[0])) {
            $sender->sendMessage($this->plugin->getMessage('command.accept.passClan'));
            return;
        }
        if (!$this->plugin->clanExists($args[0])) {
            $sender->sendMessage($this->plugin->getMessage('command.accept.invalidClan'));
            return;
        }
        $clan = $this->plugin->getClan($args[0]);
        if (!$clan->isInvited($player)) {
            $sender->sendMessage($this->plugin->getMessage('command.accept.notInvited'));
            return;
        }
        $clan->removeInvite($player);
        $player->joinClan($clan);
        $sender->sendMessage($this->plugin->getMessage('command.accept.success', ['{clan}' => $clan->getDisplayName()]));
    }

}