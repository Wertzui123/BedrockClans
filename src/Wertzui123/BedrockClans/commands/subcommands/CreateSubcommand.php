<?php

namespace Wertzui123\BedrockClans\commands\subcommands;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use Wertzui123\BedrockClans\Clan;

class CreateSubcommand extends Subcommand
{

    public function canUse(CommandSender $sender): bool
    {
        return $sender instanceof Player && $sender->hasPermission('bedrockclans.command.create');
    }

    public function execute(CommandSender $sender, array $args)
    {
        $player = $this->plugin->getPlayer($sender);
        if ($player->isInClan()) {
            $sender->sendMessage($this->plugin->getMessage('command.create.alreadyInClan'));
            return;
        }
        if (!isset($args[0])) {
            $sender->sendMessage($this->plugin->getMessage('command.create.passName'));
            return;
        }
        $name = implode(' ', $args);
        if ($this->plugin->clanExists($name)) {
            $sender->sendMessage($this->plugin->getMessage('command.create.clanExists'));
            return;
        }
        if (!Clan::isValidName($name)) {
            $sender->sendMessage($this->plugin->getMessage('command.create.invalidName'));
            return;
        }
        if ($this->plugin->getConfig()->get('create_costs') && !is_null($this->plugin->getServer()->getPluginManager()->getPlugin('EconomyAPI'))) {
            if (!$sender->hasPermission('bedrockclans.create.cost.bypass')) {
                $price = (int)$this->plugin->getConfig()->get('clan_create_costs');
                if ($player->getMoney() >= $price) {
                    $player->removeMoney($price);
                } else {
                    $sender->sendMessage($this->plugin->getMessage('command.create.notEnoughMoney', ['{price}' => $price]));
                    return;
                }
            }
        }
        $sender->sendMessage($this->plugin->getMessage('command.create.success', ['{clan}' => $name]));
        $clan = $this->plugin->createClan($name, $player);
        $player->setClan($clan);
    }

}