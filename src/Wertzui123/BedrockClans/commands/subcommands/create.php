<?php

namespace Wertzui123\BedrockClans\commands\subcommands;

use pocketmine\command\CommandSender;
use pocketmine\Player;
use Wertzui123\BedrockClans\Main;

class create extends Subcommand
{

    private $plugin;

    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
    }

    public function canUse(CommandSender $sender): bool
    {
        return $sender instanceof Player && $sender->hasPermission("bedrockclans.cmd.create");
    }

    public function execute(CommandSender $sender, array $args)
    {
        $player = $this->plugin->getPlayer($sender);
        $money = $this->plugin->getServer()->getPluginManager()->getPlugin("EconomyAPI");
        if ($player->isInClan()) {
            $sender->sendMessage($this->plugin->getMessage("create_already_in_clan"));
            return;
        }

        if (!isset($args[0])) {
            $sender->sendMessage($this->plugin->getMessage("create_provide_name"));
        } else {
            if (!$this->plugin->ClanExist($args[0])) {
                if (in_array($args[0], $this->plugin->getConfig()->get("banned_clan_names"))) {
                    $sender->sendMessage($this->plugin->getMessage("create_name_is_banned"));
                    return;
                }
                if ($this->plugin->getConfig()->get("create_costs")) {
                    if ($money === null) {
                        $sender->sendMessage($this->plugin->getMessage("economy_plugin_was_not_found"));
                        return;
                    }
                    if (!$sender->hasPermission("bedrockclans.cmd.create.costs.bypass")) {
                        if ($money->myMoney($sender) >= (int)$this->plugin->getMessage("clan_create_cost")) {
                            $money->reduceMoney($sender, (int)$this->plugin->getMessage("clan_create_costs"));
                        } else {
                            $sender->sendMessage($this->plugin->getMessage("create_not_enought_money"));
                            return;
                        }
                    }
                }
                $sender->sendMessage($this->plugin->getMessage("create_create_succes"));
                $clan = $this->plugin->createClan($args[0], $player);
                $player->setClan($clan);
            } else {
                $sender->sendMessage($this->plugin->getMessage("create_already_exist"));
            }
        }
    }

}