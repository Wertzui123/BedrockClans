<?php

namespace Wertzui123\BedrockClans\commands\subcommands;

use pocketmine\command\CommandSender;
use Wertzui123\BedrockClans\Main;

class help extends Subcommand
{

    private $plugin;

    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
    }

    public function canUse(CommandSender $sender): bool
    {
        return true;
    }

    public function execute(CommandSender $sender, array $args)
    {
        $sender->sendMessage($this->plugin->getMessage("help_help_list"));
    }

}