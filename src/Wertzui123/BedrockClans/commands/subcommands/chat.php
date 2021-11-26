<?php

namespace Wertzui123\BedrockClans\commands\subcommands;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use Wertzui123\BedrockClans\Main;

class chat extends Subcommand
{

    /**
     * chat constructor.
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
        if ($this->plugin->getConfig()->getNested('chat.enabled') !== true) {
            $sender->sendMessage($this->plugin->getMessage('command.chat.disabled'));
            return;
        }
        $player = $this->plugin->getPlayer($sender);
        $clan = $player->getClan();
        if ($clan === null) {
            $sender->sendMessage($this->plugin->getMessage('command.chat.noClan'));
            return;
        }
        if (!isset($args[0])) {
            $sender->sendMessage($this->plugin->getMessage('command.chat.passMessage'));
            return;
        }
        $message = implode(' ', $args);
        if ($message === $this->plugin->getConfig()->getNested('chat.on')) {
            $player->setChatting();
            $sender->sendMessage($this->plugin->getMessage('command.chat.on'));
            return;
        }
        if ($message === $this->plugin->getConfig()->getNested('chat.off')) {
            $player->setChatting(false);
            $sender->sendMessage($this->plugin->getMessage('command.chat.off'));
            return;
        }
        $player->chat($message);
    }

}