<?php

declare(strict_types=1);

namespace Wertzui123\BedrockClans\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use Wertzui123\BedrockClans\commands\subcommands\about;
use Wertzui123\BedrockClans\commands\subcommands\accept;
use Wertzui123\BedrockClans\commands\subcommands\chat;
use Wertzui123\BedrockClans\commands\subcommands\demote;
use Wertzui123\BedrockClans\commands\subcommands\create;
use Wertzui123\BedrockClans\commands\subcommands\delete;
use Wertzui123\BedrockClans\commands\subcommands\deposit;
use Wertzui123\BedrockClans\commands\subcommands\help;
use Wertzui123\BedrockClans\commands\subcommands\home;
use Wertzui123\BedrockClans\commands\subcommands\info;
use Wertzui123\BedrockClans\commands\subcommands\invite;
use Wertzui123\BedrockClans\commands\subcommands\join;
use Wertzui123\BedrockClans\commands\subcommands\kick;
use Wertzui123\BedrockClans\commands\subcommands\leader;
use Wertzui123\BedrockClans\commands\subcommands\leave;
use Wertzui123\BedrockClans\commands\subcommands\promote;
use Wertzui123\BedrockClans\commands\subcommands\sethome;
use Wertzui123\BedrockClans\commands\subcommands\Subcommand;
use Wertzui123\BedrockClans\commands\subcommands\withdraw;
use Wertzui123\BedrockClans\Main;

class clancmd extends Command
{

    /** @var Main */
    private $plugin;

    public function __construct(Main $plugin, $data)
    {
        parent::__construct($data['command'], $data['description'], $data['usage'], $data['aliases']);
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!isset($args[0])) {
            $sender->sendMessage($this->plugin->getMessage('command.clan.usage'));
            return;
        }
        $subcommand = $this->getSubCommand($args[0]);
        if (is_null($subcommand)) {
            $sender->sendMessage($this->plugin->getMessage('command.clan.usage'));
            return;
        }
        if (!$subcommand->canUse($sender)) {
            $sender->sendMessage($this->plugin->getMessage('command.clan.cannotUseSubcommand'));
            return;
        }
        array_shift($args);
        $subcommand->execute($sender, $args);
    }

    public function getSubCommand($name): ?Subcommand
    {
        switch ($name) {
            case "about":
                return new about($this->plugin);
            case "accept":
                return new accept($this->plugin);
            case "chat":
                return new chat($this->plugin);
            case "create":
                return new create($this->plugin);
            case "delete":
                return new delete($this->plugin);
            case "demote":
                return new demote($this->plugin);
            case "deposit":
                return new deposit($this->plugin);
            case "help":
                return new help($this->plugin);
            case "home":
                return new home($this->plugin);
            case "info":
                return new info($this->plugin);
            case "invite":
                return new invite($this->plugin);
            case "join":
                return new join($this->plugin);
            case "kick":
                return new kick($this->plugin);
            case "leader":
                return new leader($this->plugin);
            case "leave":
                return new leave($this->plugin);
            case "promote":
                return new promote($this->plugin);
            case "sethome":
                return new sethome($this->plugin);
            case "withdraw":
                return new withdraw($this->plugin);
        }
        return null;
    }

}