<?php

declare(strict_types=1);

namespace Wertzui123\BedrockClans\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use Wertzui123\BedrockClans\commands\subcommands\about;
use Wertzui123\BedrockClans\commands\subcommands\accept;
use Wertzui123\BedrockClans\commands\subcommands\chat;
use Wertzui123\BedrockClans\commands\subcommands\create;
use Wertzui123\BedrockClans\commands\subcommands\delete;
use Wertzui123\BedrockClans\commands\subcommands\help;
use Wertzui123\BedrockClans\commands\subcommands\info;
use Wertzui123\BedrockClans\commands\subcommands\invite;
use Wertzui123\BedrockClans\commands\subcommands\join;
use Wertzui123\BedrockClans\commands\subcommands\kick;
use Wertzui123\BedrockClans\commands\subcommands\leader;
use Wertzui123\BedrockClans\commands\subcommands\leave;
use Wertzui123\BedrockClans\commands\subcommands\Subcommand;
use Wertzui123\BedrockClans\Main;

class clancmd extends Command
{

    public $plugin;

    public function __construct(Main $plugin, $data)
    {
        parent::__construct($data['command'], $data['description'], null, $data['aliases']);
        $this->setPermission("bedrockclans.cmd");
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if(!isset($args[0])){
            $sender->sendMessage($this->plugin->getConfig()->get("usage"));
            return;
        }
        $subcommand = $this->getSubCommand($args[0]);
        if($subcommand === null){
            $sender->sendMessage($this->plugin->getConfig()->get("usage"));
            return;
        }
        if(!$subcommand->canUse($sender)){
            $sender->sendMessage($this->plugin->getMessage('cannot_use_subcommand'));
            return;
        }
        $arguments = $args;
        array_shift($arguments);
        $subcommand->execute($sender, $arguments);
    }

    public function getSubCommand($name) : ?Subcommand{
        switch ($name){
            case "about":
                return new about();
            case "accept":
                return new accept($this->plugin);
            case "chat":
                return new chat($this->plugin);
            case "create":
                return new create($this->plugin);
            case "delete":
                return new delete($this->plugin);
            case "help":
                return new help($this->plugin);
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
        }
        return null;
    }
}
