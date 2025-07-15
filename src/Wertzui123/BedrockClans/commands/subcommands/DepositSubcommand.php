<?php

declare(strict_types=1);

namespace Wertzui123\BedrockClans\commands\subcommands;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class DepositSubcommand extends Subcommand
{

    public function canUse(CommandSender $sender): bool
    {
        return $sender instanceof Player;
    }

    public function execute(CommandSender $sender, array $args)
    {
        if ($this->plugin->getConfig()->getNested('bank.enabled') !== true) {
            $sender->sendMessage($this->plugin->getMessage('command.deposit.disabled'));
            return;
        }
        $player = $this->plugin->getPlayer($sender);
        if (!$player->isInClan()) {
            $sender->sendMessage($this->plugin->getMessage('command.deposit.noClan'));
            return;
        }
        $clan = $player->getClan();
        if (!isset($args[0]) || !is_numeric($args[0]) || (int) $args[0] <= 0) {
            $sender->sendMessage($this->plugin->getMessage('command.deposit.passNumber'));
            return;
        }
        $amount = (int) $args[0];
        $player->tryRemoveMoney($amount, function (bool $success) use ($sender, $clan, $amount) {
            if ($success) {
                $clan->setBank($clan->getBank() + $amount);
                $sender->sendMessage($this->plugin->getMessage('command.deposit.success', ['{amount}' => $amount]));
            } else {
                $sender->sendMessage($this->plugin->getMessage('command.deposit.tooMuch'));
                return;
            }
        });
    }

}