<?php

declare(strict_types=1);

namespace Wertzui123\BedrockClans\commands\subcommands;

use pocketmine\command\CommandSender;
use Wertzui123\BedrockClans\Main;

abstract class Subcommand
{

    /** @var Main */
    protected $plugin;

    /**
     * Subcommand constructor.
     * @param Main $plugin
     */
    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * @param CommandSender $sender
     * @return bool
     */
    public abstract function canUse(CommandSender $sender): bool;

    /**
     * @param CommandSender $sender ;
     * @param array $args
     */
    public abstract function execute(CommandSender $sender, array $args);

}