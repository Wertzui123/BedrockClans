<?php

namespace Wertzui123\BedrockClans\commands\subcommands;

use pocketmine\command\CommandSender;

class HelpSubcommand extends Subcommand
{

    public function canUse(CommandSender $sender): bool
    {
        return true;
    }

    public function execute(CommandSender $sender, array $args)
    {
        $sender->sendMessage($this->plugin->getMessage('command.help.success'));
    }

}