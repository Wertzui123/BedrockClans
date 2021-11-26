<?php

namespace Wertzui123\BedrockClans\commands\subcommands;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use Wertzui123\BedrockClans\Main;

class invite extends Subcommand
{

    /**
     * invite constructor.
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
            $sender->sendMessage($this->plugin->getMessage('command.invite.noClan'));
            return;
        }
        if (!$player->canInvite()) {
            $sender->sendMessage($this->plugin->getMessage('command.invite.noPermission'));
            return;
        }
        $name = implode(' ', $args);
        if (empty($name)) {
            $sender->sendMessage($this->plugin->getMessage('command.invite.passPlayer'));
            return;
        }
        if (!$this->plugin->getServer()->getPlayerExact($name)) {
            $sender->sendMessage($this->plugin->getMessage('command.invite.notFound'));
            return;
        }
        if ($this->plugin->getPlayer($this->plugin->getServer()->getPlayerExact($name))->getClan() === $player->getClan()) {
            $sender->sendMessage($this->plugin->getMessage('command.invite.alreadyInClan'));
            return;
        }
        if ($player->getClan()->isInvited($this->plugin->getPlayer($this->plugin->getServer()->getPlayerExact($name)))) {
            $sender->sendMessage($this->plugin->getMessage('command.invite.alreadyInvited'));
            return;
        }
        $player->getClan()->invite($player, $this->plugin->getPlayer($this->plugin->getServer()->getPlayerExact($name)));
        $sender->sendMessage($this->plugin->getMessage('command.invite.sender', ['{player}' => $this->plugin->getServer()->getPlayerExact($name)->getName()]));
    }

}