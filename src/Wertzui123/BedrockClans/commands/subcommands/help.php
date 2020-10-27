<?php

namespace Wertzui123\BedrockClans\commands\subcommands;

use pocketmine\command\CommandSender;
use Wertzui123\BedrockClans\Main;

class help extends Subcommand
{

    /**
     * help constructor.
     * @param Main $plugin
     */
    public function __construct(Main $plugin)
    {
        parent::__construct($plugin);
    }

    public function canUse(CommandSender $sender): bool
    {
        return true;
    }

    public function execute(CommandSender $sender, array $args)
    {
        $sender->sendMessage($this->plugin->getMessage("command.help.success"));
    }

}