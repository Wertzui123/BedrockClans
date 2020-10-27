<?php

namespace Wertzui123\BedrockClans\commands\subcommands;

use pocketmine\command\CommandSender;
use pocketmine\Player;
use Wertzui123\BedrockClans\Main;

class join extends Subcommand
{

    /**
     * join constructor.
     * @param Main $plugin
     */
    public function __construct(Main $plugin)
    {
        parent::__construct($plugin);
    }

    public function canUse(CommandSender $sender): bool
    {
        return $sender instanceof Player && $sender->hasPermission("bedrockclans.command.join");
    }

    public function execute(CommandSender $sender, array $args)
    {
        $player = $this->plugin->getPlayer($sender);
        if ($player->isInClan()) {
            $sender->sendMessage($this->plugin->getMessage('command.join.alreadyInClan'));
            return;
        }
        if (!isset($args[0])) {
            $sender->sendMessage($this->plugin->getMessage('command.join.passClan'));
            return;
        }
        $name = implode(' ', $args);
        if (!$this->plugin->clanExists($name)) {
            $sender->sendMessage($this->plugin->getMessage('command.join.invalidClan'));
            return;
        }
        $clan = $this->plugin->getClan($name);
        $player->joinClan($clan);
        $sender->sendMessage($this->plugin->getMessage('command.join.success', ['{clan}' => $clan->getName()]));
    }

}