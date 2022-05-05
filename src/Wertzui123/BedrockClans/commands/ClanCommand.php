<?php

declare(strict_types=1);

namespace Wertzui123\BedrockClans\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use Wertzui123\BedrockClans\commands\subcommands\AboutSubcommand;
use Wertzui123\BedrockClans\commands\subcommands\AcceptSubcommand;
use Wertzui123\BedrockClans\commands\subcommands\ChatSubcommand;
use Wertzui123\BedrockClans\commands\subcommands\DemoteSubcommand;
use Wertzui123\BedrockClans\commands\subcommands\CreateSubcommand;
use Wertzui123\BedrockClans\commands\subcommands\DeleteSubcommand;
use Wertzui123\BedrockClans\commands\subcommands\DepositSubcommand;
use Wertzui123\BedrockClans\commands\subcommands\HelpSubcommand;
use Wertzui123\BedrockClans\commands\subcommands\HomeSubcommand;
use Wertzui123\BedrockClans\commands\subcommands\InfoSubcommand;
use Wertzui123\BedrockClans\commands\subcommands\InviteSubcommand;
use Wertzui123\BedrockClans\commands\subcommands\JoinSubcommand;
use Wertzui123\BedrockClans\commands\subcommands\KickSubcommand;
use Wertzui123\BedrockClans\commands\subcommands\LeaderSubcommand;
use Wertzui123\BedrockClans\commands\subcommands\LeaveSubcommand;
use Wertzui123\BedrockClans\commands\subcommands\PromoteSubcommand;
use Wertzui123\BedrockClans\commands\subcommands\SetColorSubcommand;
use Wertzui123\BedrockClans\commands\subcommands\SetHomeSubcommand;
use Wertzui123\BedrockClans\commands\subcommands\Subcommand;
use Wertzui123\BedrockClans\commands\subcommands\WithdrawSubcommand;
use Wertzui123\BedrockClans\Main;

class ClanCommand extends Command
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
            case 'about':
                return new AboutSubcommand($this->plugin);
            case 'accept':
                return new AcceptSubcommand($this->plugin);
            case 'chat':
                return new ChatSubcommand($this->plugin);
            case 'create':
                return new CreateSubcommand($this->plugin);
            case 'delete':
                return new DeleteSubcommand($this->plugin);
            case 'demote':
                return new DemoteSubcommand($this->plugin);
            case 'deposit':
                return new DepositSubcommand($this->plugin);
            case 'help':
                return new HelpSubcommand($this->plugin);
            case 'home':
                return new HomeSubcommand($this->plugin);
            case 'info':
                return new InfoSubcommand($this->plugin);
            case 'invite':
                return new InviteSubcommand($this->plugin);
            case 'join':
                return new JoinSubcommand($this->plugin);
            case 'kick':
                return new KickSubcommand($this->plugin);
            case 'leader':
                return new LeaderSubcommand($this->plugin);
            case 'leave':
                return new LeaveSubcommand($this->plugin);
            case 'promote':
                return new PromoteSubcommand($this->plugin);
            case 'setcolor':
                return new SetColorSubcommand($this->plugin);
            case 'sethome':
                return new SetHomeSubcommand($this->plugin);
            case 'withdraw':
                return new WithdrawSubcommand($this->plugin);
        }
        return null;
    }

}