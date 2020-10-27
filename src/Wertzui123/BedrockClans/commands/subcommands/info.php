<?php

namespace Wertzui123\BedrockClans\commands\subcommands;

use pocketmine\command\CommandSender;
use pocketmine\Player;
use Wertzui123\BedrockClans\Main;

class info extends Subcommand
{

    /**
     * info constructor.
     * @param Main $plugin
     */
    public function __construct(Main $plugin)
    {
        parent::__construct($plugin);
    }

    public function canUse(CommandSender $sender): bool
    {
        return $sender instanceof Player;
    }

    public function execute(CommandSender $sender, array $args)
    {
        $player = $this->plugin->getPlayer($sender);
        if (isset($args[0])) {
            if (!$this->plugin->clanExists(implode(' ', $args))) {
                $sender->sendMessage($this->plugin->getMessage('command.info.invalidClan'));
                return;
            }
            $clan = $this->plugin->getClan(implode(' ', $args));
        } else {
            if (!$player->isInClan()) {
                $sender->sendMessage($this->plugin->getMessage('command.info.noClan'));
                return;
            }
            $clan = $player->getClan();
        }
        $sender->sendMessage($this->plugin->getMessage('command.info.success', ['{name}' => $clan->getName(), '{leader}' => $clan->getLeaderWithRealName(), '{members}' => implode(', ', $clan->getMembersWithRealName(true)), '{bank}' => $clan->getBank()]));
    }

}