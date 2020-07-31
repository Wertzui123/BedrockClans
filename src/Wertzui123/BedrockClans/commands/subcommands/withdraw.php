<?php

namespace Wertzui123\BedrockClans\commands\subcommands;

use pocketmine\command\CommandSender;
use pocketmine\Player;
use Wertzui123\BedrockClans\Clan;
use Wertzui123\BedrockClans\Main;

class withdraw extends Subcommand
{

    private $plugin;

    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
    }

    public function canUse(CommandSender $sender): bool
    {
        return $sender instanceof Player;
    }

    public function execute(CommandSender $sender, array $args)
    {
        if($this->plugin->getConfig()->getNested('bank.enabled') !== true){
            $sender->sendMessage($this->plugin->getMessage('command.withdraw.disabled'));
            return;
        }
        $player = $this->plugin->getPlayer($sender);
        if (!$player->isInClan()) {
            $sender->sendMessage($this->plugin->getMessage('command.withdraw.noClan'));
            return;
        }
        if($player->hasWithdrawCooldown()){
            $sender->sendMessage($this->plugin->ConvertSeconds($player->getWithdrawCooldown(), $this->plugin->getMessage('command.withdraw.cooldown')));
            return;
        }
        if(!isset($args[0]) || !is_numeric($args[0]) || (int)$args[0] <= 0){
            $sender->sendMessage($this->plugin->getMessage('command.withdraw.passNumber'));
            return;
        }
        $clan = $player->getClan();
        $amount = (int)$args[0];
        if($amount > ($clan->getBank() * (0.01 * Clan::getMaxWithdrawAmount($player->getClan()->getRank($player)))) || $amount > $clan->getBank()){
            $sender->sendMessage($this->plugin->getMessage('command.withdraw.tooMuch'));
            return;
        }
        $clan->setBank($clan->getBank() - $amount);
        $player->addMoney($amount);
        if(!$player->isLeader()){
            $player->addWithdrawCooldown(Main::getInstance()->getConfig()->getNested('bank.withdraw.cooldown', 24) * 60 * 60);
        }
        $sender->sendMessage($this->plugin->getMessage('command.withdraw.success', ['{amount}' => $amount]));
    }

}