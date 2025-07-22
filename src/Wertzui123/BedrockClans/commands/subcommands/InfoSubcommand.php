<?php

declare(strict_types=1);

namespace Wertzui123\BedrockClans\commands\subcommands;

use jojoe77777\FormAPI\SimpleForm;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class InfoSubcommand extends Subcommand
{
    public function canUse(CommandSender $sender): bool
    {
        return $sender instanceof Player;
    }

    public function execute(CommandSender $sender, array $args)
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage("§You have to run this command in the game.");
            return;
        }

        $player = $this->plugin->getPlayer($sender);

        if (isset($args[0])) {
            $clanName = implode(" ", $args);
            if (!$this->plugin->clanExists($clanName)) {
                $sender->sendMessage($this->plugin->getMessage('command.info.invalidClan'));
                return;
            }
            $clan = $this->plugin->getClan($clanName);
        } else {
            if (!$player->isInClan()) {
                $sender->sendMessage($this->plugin->getMessage('command.info.noClan'));
                return;
            }
            $clan = $player->getClan();
        }

        $creationDate = $clan->getCreationDate() < 0
            ? $this->plugin->getConfig()->get('date_unknown')
            : date($this->plugin->getConfig()->get('date_format'), $clan->getCreationDate());

        $form = new SimpleForm(function (Player $player, ?int $data): void {
            // Tidak ada aksi setelah menutup form
        });

        $form->setTitle("§l§9Clan Info");
        $form->setContent(
            "§fName Clan: §b" . $clan->getDisplayName() . "\n" .
            "§fDate Created: §a" . $creationDate . "\n" .
            "§fLeader: §e" . $clan->getLeaderWithRealName() . "\n" .
            "§fMember: §d" . implode(", ", $clan->getMembersWithRealName(true)) . "\n" .
            "§fMoney Clan: §6" . $clan->getBank()
        );
        $form->addButton("§cClosed", 0, "textures/ui/cancel");
        $sender->sendForm($form);
    }
}
