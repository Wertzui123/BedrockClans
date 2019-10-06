<?php

namespace Wertzui123\BedrockClans\commands\subcommands;

use pocketmine\command\CommandSender;
use pocketmine\Player;
use Wertzui123\BedrockClans\Main;

class chat extends Subcommand
{

    private $plugin;

    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
    }

    public function canUse(CommandSender $sender) : bool
    {
        return $sender instanceof Player;
    }

    public function execute(CommandSender $sender, array $args)
    {
        if($this->plugin->ConfigArray()['chat_disabled'] === true){
            $sender->sendMessage($this->plugin->getMessage('chat_disabled'));
            return;
        }
        $player = $this->plugin->getPlayer($sender);
        $clan = $player->getClan();
        if($clan === null){
            $sender->sendMessage($this->plugin->getMessage('chat_not_in_clan'));
            return;
        }
        if(!isset($args[0])){
            $sender->sendMessage($this->plugin->getMessage('chat_provide_message'));
            return;
        }

        foreach ($clan->getMembersWithRealName() as $member){
            if(($member = $this->plugin->getServer()->getPlayerExact($member)) instanceof Player){
                $member->sendMessage(str_replace(['{name}', '{message}'], [$sender->getName(), implode(' ', $args)], $this->plugin->getConfig()->get('clan_chat_format')));
            }
        }
    }

}