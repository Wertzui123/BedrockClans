<?php

declare(strict_types=1);

namespace Wertzui123\BedrockClans\commands\subcommands;

use Wertzui123\BedrockClans\Main;
use Wertzui123\BedrockClans\form\CustomForm;
use Wertzui123\BedrockClans\form\SimpleForm;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

class UiSubcommand extends Subcommand
{

    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
    }
    
    public function canUse(CommandSender $sender): bool
    {
        return $sender instanceof Player;
    }

    public function execute(CommandSender $sender, array $args): void
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage("§cThis command can only be used in-game.");
            return;
        }

        $form = new SimpleForm(function (Player $player, ?int $data): void {
            if ($data === null) return;

            $map = [
                0 => null,
                1 => fn() => $this->Create($player),
                2 => fn() => $this->dispatch($player, "clan delete"),
                3 => fn() => $this->dispatch($player, "clan leave"),
                4 => fn() => $this->Invite($player),
                5 => fn() => $this->dispatch($player, "clan accept"),
                6 => fn() => $this->Demote($player),
                7 => fn() => $this->Join($player),
                8 => fn() => $this->Leader($player),
                9 => fn() => $this->Kick($player),
                10 => fn() => $this->Chat($player),
                11 => fn() => $this->Info($player),
                12 => fn() => $this->dispatch($player, "clan sethome"),
                13 => fn() => $this->dispatch($player, "clan home"),
                14 => fn() => $this->Withdraw($player),
                15 => fn() => $this->Deposit($player),
                16 => fn() => $this->Promote($player),
                17 => fn() => $this->dispatch($player, "clan leave"),
            ];

            if (isset($map[$data]) && is_callable($map[$data])) {
                $map[$data]();
            }
        });

        $form->setTitle("§l§9Clan Menu");
        $form->setContent("§fChoose a clan option below:");
        $form->addButton("§c§lExit", 0, "textures/ui/cancel");
        $form->addButton("§l§0Create", 0, "textures/ui/icon_recipe_nature");
        $form->addButton("§l§0Delete", 0, "textures/ui/trash");
        $form->addButton("§l§0Leave", 0, "textures/ui/NetherPortal");
        $form->addButton("§l§0Invite", 0, "textures/ui/icon_alex");
        $form->addButton("§l§0Accept Invite", 0, "textures/ui/confirm");
        $form->addButton("§l§0Demote", 0, "textures/ui/icon_alex");
        $form->addButton("§l§0Join Clan", 0, "textures/ui/icon_steve");
        $form->addButton("§l§0Transfer Leader", 0, "textures/ui/gear");
        $form->addButton("§l§0Kick", 0, "textures/ui/icon_alex");
        $form->addButton("§l§0Clan Chat", 0, "textures/ui/comment");
        $form->addButton("§l§0Clan Info", 0, "textures/ui/copy");
        $form->addButton("§l§0Set Home", 0, "textures/ui/World");
        $form->addButton("§l§0Home", 0, "textures/ui/accessibility_glyph_color");
        $form->addButton("§l§0Withdraw", 0, "textures/ui/MCoin");
        $form->addButton("§l§0Deposit", 0, "textures/items/map_filled");
        $form->addButton("§l§0Promote", 0, "textures/ui/FriendsIcon");
        $form->addButton("§l§0Leave (Again)", 0, "textures/ui/NetherPortal");

        $form->sendToPlayer($sender);
    }

    private function dispatch(Player $player, string $command): void {
        $this->plugin->getServer()->getCommandMap()->dispatch($player, $command);
    }

    private function inputForm(Player $player, string $title, string $placeholder, callable $callback): void {
        $form = new CustomForm(function (Player $player, ?array $data) use ($callback): void {
            if ($data !== null && isset($data[0]) && $data[0] !== "") {
                $callback($data[0]);
            }
        });
        $form->setTitle($title);
        $form->addInput($placeholder);
        $form->sendToPlayer($player);
    }

    public function Promote(Player $player): void {
        $this->inputForm($player, "Promote Member", "Enter player name", fn(string $name) => $this->dispatch($player, "clan promote $name"));
    }

    public function Deposit(Player $player): void {
        $this->inputForm($player, "Deposit to Clan Bank", "Enter amount", fn(string $amount) => $this->dispatch($player, "clan deposit $amount"));
    }

    public function Withdraw(Player $player): void {
        $this->inputForm($player, "Withdraw from Clan Bank", "Enter amount", fn(string $amount) => $this->dispatch($player, "clan withdraw $amount"));
    }

    public function Create(Player $player): void {
        $this->inputForm($player, "Create Clan", "Enter clan name", fn(string $name) => $this->dispatch($player, "clan create $name"));
    }

    public function Invite(Player $player): void {
        $this->inputForm($player, "Invite Player to Clan", "Enter player name", fn(string $name) => $this->dispatch($player, "clan invite $name"));
    }

    public function Join(Player $player): void {
        $this->inputForm($player, "Join Clan", "Enter clan name", fn(string $name) => $this->dispatch($player, "clan join $name"));
    }

    public function Leader(Player $player): void {
        $this->inputForm($player, "Transfer Leadership", "Enter player name", fn(string $name) => $this->dispatch($player, "clan leader $name"));
    }

    public function Kick(Player $player): void {
        $this->inputForm($player, "Kick Member", "Enter player name", fn(string $name) => $this->dispatch($player, "clan kick $name"));
    }

    public function Demote(Player $player): void {
        $this->inputForm($player, "Demote Member", "Enter player name", fn(string $name) => $this->dispatch($player, "clan demote $name"));
    }

    public function Chat(Player $player): void {
        $this->inputForm($player, "Send Clan Message", "Enter message", fn(string $message) => $this->dispatch($player, "clan chat $message"));
    }

    public function Info(Player $player): void {
        $this->inputForm($player, "View Clan Info", "Enter clan name", fn(string $name) => $this->dispatch($player, "clan info $name"));
    }
}
