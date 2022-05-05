<?php

namespace Wertzui123\BedrockClans\commands\subcommands;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class SetColorSubcommand extends Subcommand
{

    public function canUse(CommandSender $sender): bool
    {
        return $sender instanceof Player;
    }

    public function execute(CommandSender $sender, array $args)
    {
        $player = $this->plugin->getPlayer($sender);
        if (!$player->isInClan()) {
            $sender->sendMessage($this->plugin->getMessage('command.setcolor.noClan'));
            return;
        }
        $clan = $player->getClan();
        if (strtolower($sender->getName()) !== $clan->getLeader()) {
            $sender->sendMessage($this->plugin->getMessage('command.setcolor.notLeader'));
            return;
        }

        switch (strtolower(implode(' ', $args))) {
            case 'f':
            case 'white':
                $color = 'f';
                break;
            case '4':
            case 'dark_red':
            case 'dark red':
                $color = '4';
                break;
            case 'c':
            case 'red':
                $color = 'c';
                break;
            case '6':
            case 'gold':
                $color = '6';
                break;
            case 'e':
            case 'yellow':
                $color = 'e';
                break;
            case '2':
            case 'dark_green':
            case 'dark green':
                $color = '2';
                break;
            case 'a':
            case 'green':
                $color = 'a';
                break;
            case '3':
            case 'dark_aqua':
            case 'dark aqua':
                $color = '3';
                break;
            case 'b':
            case 'aqua':
                $color = 'b';
                break;
            case '1':
            case 'dark_blue':
            case 'dark blue':
                $color = '1';
                break;
            case '9':
            case 'blue':
                $color = '9';
                break;
            case '5':
            case 'dark_purple':
            case 'dark purple':
            case 'purle':
                $color = '5';
                break;
            case 'd':
            case 'light_purple':
            case 'light purple':
            case 'pink':
                $color = 'd';
                break;
            case '8':
            case 'dark_gray':
            case 'dark gray':
                $color = '8';
                break;
            case '7':
            case 'light_gray':
            case 'light gray':
            case 'gray':
                $color = '7';
                break;
            case '0':
            case 'black':
                $color = '0';
                break;
            default:
                $sender->sendMessage($this->plugin->getMessage('command.setcolor.invalidColor'));
                return;
        }

        $clan->setColor($color);
        $sender->sendMessage($this->plugin->getMessage('command.setcolor.success'));
    }

}