<?php

declare(strict_types=1);

namespace Wertzui123\BedrockClans;

use pocketmine\OfflinePlayer;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use Wertzui123\BedrockClans\commands\clancmd;
use Wertzui123\BedrockClans\events\clan\ClanCreateEvent;
use Wertzui123\BedrockClans\events\clan\ClanDeleteEvent;
use Wertzui123\BedrockClans\listener\CustomListener;
use Wertzui123\BedrockClans\listener\EventListener;

class Main extends PluginBase
{

    // TODO: UIs

    private static $instance;
    private $prefix;
    private $clans = [];
    private $playerNames;
    private $stringsFile;
    private $playersFile;
    private $withdrawCooldownsFile;
    private $players = [];

    const CONFIG_VERSION = 3.2;

    public function onEnable(): void
    {
        self::$instance = $this;
        $this->ConfigUpdater();
        if (!is_dir($this->getDataFolder() . 'clans')) @mkdir($this->getDataFolder() . 'clans');
        $this->playerNames = new Config($this->getDataFolder() . "names.json", Config::YAML);
        $this->stringsFile = new Config($this->getDataFolder() . 'strings.yml', Config::YAML);
        $this->playersFile = new Config($this->getDataFolder() . 'players.json', Config::JSON);
        if (file_exists($this->getDataFolder() . 'players.yml')) {
            $this->playersFile->setAll((new Config($this->getDataFolder() . 'players.yml', Config::YAML))->getAll());
            unlink($this->getDataFolder() . 'players.yml');
        }
        $this->withdrawCooldownsFile = new Config($this->getDataFolder() . 'withdrawCooldowns.json', Config::JSON);
        $this->loadClans(true);
        $this->prefix = (string)$this->getConfig()->get('prefix');
        $this->getServer()->getPluginManager()->registerEvents(new CustomListener($this), $this);
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $data = ['command' => $this->getConfig()->getNested('command.clan.command'), 'description' => $this->getConfig()->getNested('command.clan.description'), 'usage' => $this->getConfig()->getNested('command.clan.usage'), 'aliases' => $this->getConfig()->getNested('command.clan.aliases')];
        $this->getServer()->getCommandMap()->register("BedrockClans", new clancmd($this, $data));
    }

    /**
     * Returns the current (and only) instance of this class
     * @return Main
     */
    public static function getInstance()
    {
        return self::$instance;
    }

    /**
     * @internal
     * @return Config
     */
    public function getStringsFile(): Config
    {
        return $this->stringsFile;
    }

    /**
     * @internal
     * @return Config
     */
    public function getPlayersFile(): Config
    {
        return $this->playersFile;
    }

    /**
     * @internal
     * @return Config
     */
    public function getWithdrawCooldownsFile(): Config
    {
        return $this->withdrawCooldownsFile;
    }

    /**
     * @internal
     * @return Config
     */
    public function getPlayerNames(): Config
    {
        return $this->playerNames;
    }

    /**
     * @internal
     * Returns a string from the strings file
     * @param array $replace [optional]
     * @param mixed $default [optional]
     * @param string $key
     * @return string|mixed
     */
    public function getString(string $key, array $replace = [], string $default = "")
    {
        return str_replace(array_keys($replace), $replace, $this->getStringsFile()->getNested($key, $default));
    }

    /**
     * @internal
     * Returns a message from the strings file
     * @param array $replace [optional]
     * @param mixed $default [optional]
     * @param string $key
     * @return string|mixed
     */
    public function getMessage(string $key, array $replace = [], string $default = "")
    {
        return $this->prefix . $this->getString($key, $replace, $default);
    }

    /**
     * Returns all clans found in the database
     * @param bool $loadYAML [optional]
     * @return array|false
     */
    public function allClans(bool $loadYAML = false)
    {
        return array_merge(glob($this->getDataFolder() . 'clans/*.json'), $loadYAML ? glob($this->getDataFolder() . 'clans/*.yml') : []);
    }

    /**
     * Loads all clans from the database
     * @param bool $loadYAML [optional]
     */
    private function loadClans(bool $loadYAML = false)
    {
        foreach ($this->allClans($loadYAML) as $clan) {
            $name = basename($clan, '.json');
            if ($loadYAML) {
                $name = basename($name, '.yml');
            }
            $this->addClan($name);
        }
    }

    /**
     * @return BCPlayer[]
     */
    public function getPlayers(): array
    {
        return $this->players;
    }

    /**
     * Returns whether a clan by the given name exists
     * @param string $name
     * @return bool
     */
    public function clanExists(string $name)
    {
        return !is_null($this->getClan($name));
    }

    /**
     * @return Clan[]
     */
    public function getClans(): array
    {
        return $this->players;
    }

    /**
     * @internal
     * @param Player $player
     */
    public function addPlayer(Player $player)
    {
        $this->players[$player->getName()] = new BCPlayer($this, $player);
    }

    /**
     * @internal
     * @param BCPlayer $player
     */
    public function removePlayer(BCPlayer $player)
    {
        $player->save();
        unset($this->players[$player->getPlayer()->getName()]);
    }

    /**
     * @api
     * Returns a BCPLayer instance for the given player
     * @param Player|string $player
     * @return BCPlayer
     */
    public function getPlayer($player): BCPlayer
    {
        return $this->players[$player instanceof Player ? $player->getName() : $player];
    }

    /**
     * Returns the full name of the given player
     * @param BCPlayer|Player|string $player
     * @return bool|mixed
     */
    public function getPlayerName($player)
    {
        if ($player instanceof BCPlayer) {
            return $this->getPlayerNames()->get(strtolower($player->getPlayer()->getName()));
        }
        if ($player instanceof Player) {
            return $this->getPlayerNames()->get(strtolower($player->getName()));
        }
        return $this->getPlayerNames()->get(strtolower($player));
    }

    /**
     * @internal
     * @see Main::createClan()
     * Registers a clan to the plugin
     * @param Clan|string $clan
     * @return Clan
     */
    public function addClan($clan)
    {
        if ($clan instanceof Clan) {
            $this->clans[$clan->getName()] = $clan;
            return $clan;
        } else {
            $c = new Clan($this, $clan);
            $this->clans[$clan] = $c;
            return $c;
        }
    }

    /**
     * @api
     * Returns a clan by its name or null if no clan by the given name exists
     * @param string $name
     * @return Clan|null
     */
    public function getClan(string $name): ?Clan
    {
        return $this->clans[$name] ?? null;
    }

    /**
     * @api
     * Returns a clan by the given player
     * @param BCPlayer|Player|string $player
     * @return Clan|null
     */
    public function getClanByPlayer($player)
    {
        $player = strtolower($player instanceof BCPlayer ? $player->getPlayer()->getName() : ($player instanceof Player ? $player->getName() : $player));
        return $this->getClan($this->getPlayersFile()->get($player, null));
    }

    /**
     * @api
     * Creates a new clan
     * @param string $name
     * @param BCPlayer $leader
     * @return Clan|null
     */
    public function createClan(string $name, BCPlayer $leader)
    {
        $file = new Config($this->getDataFolder() . 'clans/' . $name . '.json', Config::JSON);
        $file->set('name', $name);
        $file->set('leader', strtolower($leader->getPlayer()->getName()));
        $file->set('members', [strtolower($leader->getPlayer()->getName()) => 'leader']);
        $file->save();
        $clan = new Clan($this, $name, $file, strtolower($leader->getPlayer()->getName()), [strtolower($leader->getPlayer()->getName()) => 'leader']);
        $clan->setRank($leader, 'leader');
        $event = new ClanCreateEvent($clan, $leader->getPlayer());
        $event->call();
        if ($event->isCancelled()) return null;
        return $this->addClan($clan);
    }

    /**
     * @api
     * Deletes the given clan
     * @param Clan|string $clan
     * @return bool
     */
    public function deleteClan($clan)
    {
        if (!$clan instanceof Clan) $clan = $this->getClan($clan);
        $event = new ClanDeleteEvent($clan);
        $event->call();
        if ($event->isCancelled()) return false;
        $members = $clan->getMembers();
        foreach ($members as $member) {
            if ($player = $this->getServer()->getPlayerExact($member)) {
                $player->sendMessage($this->getMessage('clan.delete.members'));
                $this->getPlayer($player)->setClan(null);
            } else {
                $this->setClan($member, null);
            }
        }
        if ($clan->getBank() > 0) {
            if (!is_null($this->getServer()->getPlayerExact($clan->getLeaderWithRealName()))) {
                $this->getPlayer($this->getServer()->getPlayerExact($clan->getLeaderWithRealName()))->addMoney($clan->getBank());
                $this->getServer()->getPlayerExact($clan->getLeaderWithRealName())->sendMessage($this->getMessage('clan.delete.money', ['{amount}' => $clan->getBank()]));
            } else {
                if (!is_null($this->getServer()->getPluginManager()->getPlugin('EconomyAPI'))) {
                    $this->getServer()->getPluginManager()->getPlugin('EconomyAPI')->addMoney($clan->getLeaderWithRealName(), $clan->getBank());
                }
            }
        }
        $clan->setBank(0); // just in case some code is repeated
        unset($this->clans[$clan->getName()]);
        $file = $this->getDataFolder() . 'clans/' . $clan->getName() . '.json';
        unset($clan);
        unlink($file);
        unset($file);
        return true;
    }

    /**
     * Informs the sender and the target that an invitation has expired and removes the target from the invitation list
     * @param BCPlayer $sender
     * @param BCPlayer $target
     */
    public function expire(BCPlayer $sender, BCPlayer $target)
    {
        $sender->getClan()->removeInvite($target);
        $sender->getPlayer()->sendMessage($this->getMessage('clan.invite.expired.sender', ['{target}' => $target->getPlayer()->getName()]));
        $target->getPlayer()->sendMessage($this->getMessage('clan.invite.expired.target', ['{clan}' => $sender->getClan()->getName(), '{sender}' => $sender->getPlayer()->getName()])); // TODO: This will show an incorrect name if the sender has switched their clan
    }

    /**
     * @internal
     * @see BCPlayer::joinClan()
     * @param OfflinePlayer|string $player
     * @param Clan|null $clan
     */
    public function setClan($player, ?Clan $clan)
    {
        $clan = $clan === null ? $clan : $clan->getName();
        $this->getPlayersFile()->set($player instanceof OfflinePlayer ? strtolower($player->getName()) : $player, $clan);
        $this->getPlayersFile()->save();
    }

    /**
     * Checks whether the config version is the latest and updates it if it isn't
     */
    private function ConfigUpdater()
    {
        if (!file_exists($this->getDataFolder() . "config.yml")) {
            $this->saveResource('config.yml');
            $this->saveResource('strings.yml');
            return;
        }
        if (!$this->getConfig()->exists('config-version')) {
            $this->getLogger()->info("§eYour Config isn't the latest. BedrockClans renamed your old config to §bconfig-old.yml §6and created a new config. Have fun!");
            rename($this->getDataFolder() . "config.yml", $this->getDataFolder() . "config-old.yml");
            $this->saveResource('config.yml', true);
            $this->saveResource('strings.yml', true);
        } elseif ($this->getConfig()->get('config-version') !== self::CONFIG_VERSION) {
            $config_version = $this->getConfig()->get('config-version');
            $this->getLogger()->info("§eYour Config isn't the latest. BedrockClans renamed your old config to §bconfig-" . $config_version . ".yml §6and created a new config. Have fun!");
            rename($this->getDataFolder() . "config.yml", $this->getDataFolder() . "config-" . $config_version . ".yml");
            rename($this->getDataFolder() . "strings.yml", $this->getDataFolder() . "strings-" . $config_version . ".yml");
            $this->saveResource('config.yml');
            $this->saveResource('strings.yml');
        }
    }

    /**
     * Converts seconds to hours, minutes and seconds
     * @param int $seconds
     * @param string $message
     * @return string
     */
    public function ConvertSeconds(int $seconds, string $message): string
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds / 60) % 60);
        $seconds = $seconds % 60;
        return str_replace(["{hours}", "{minutes}", "{seconds}"], [$hours, $minutes, $seconds], $message);
    }

    public function onDisable(): void
    {
        foreach ($this->getPlayers() as $player) {
            $player->save();
        }
        foreach ($this->clans as $clan) {
            $clan->save();
        }
        $this->withdrawCooldownsFile->save();
    }

}