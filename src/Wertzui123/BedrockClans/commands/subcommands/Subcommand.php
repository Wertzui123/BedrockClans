<?php

namespace Wertzui123\BedrockClans\commands\subcommands;

use pocketmine\command\CommandSender;

abstract class Subcommand
{

    /**
     * @param CommandSender $sender
     * @return bool
     */
    public abstract function canUse(CommandSender $sender) : bool;

    /**
     * @param CommandSender $sender;
     * @param array $args
     */
    public abstract function execute(CommandSender $sender, array $args);

}