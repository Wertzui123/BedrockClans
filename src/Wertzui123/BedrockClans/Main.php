<?php

declare(strict_types=1);

namespace Wertzui123\BedrockClans;

use pocketmine\IPlayer;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use Wertzui123\BedrockClans\commands\clancmd;
use Wertzui123\BedrockClans\listener\CustomListener;
use Wertzui123\BedrockClans\listener\EventListener;
use Wertzui123\BedrockClans\tasks\invitetask;

class Main extends PluginBase
{

    private $clans = [];
    private $playernames;
    private $msgs;
    private $playersfile;
    private $players = [];

    const CFG_VERSION = 2.0;

    public function onEnable(): void
    {
        $this->ConfigUpdater(self::CFG_VERSION);
        if (!is_dir($this->getDataFolder() . 'clans')) @mkdir($this->getDataFolder() . "clans");
        $data = ['command' => $this->getConfig()->get('command'), 'description' => $this->getConfig()->get('description'), 'aliases' => $this->getConfig()->get('aliases')];
        $this->getServer()->getCommandMap()->register("clancmd", new clancmd($this, $data));
        $this->getServer()->getPluginManager()->registerEvents(new CustomListener($this), $this);
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $this->playernames = new Config($this->getDataFolder() . "names.yml", Config::YAML);
        $this->msgs = new Config($this->getDataFolder() . 'messages.yml', Config::YAML);
        $this->playersfile = new Config($this->getDataFolder() . 'players.yml');
        $this->loadClans();
    }

    public function getPath(){
        return $this->getDataFolder();
    }

    private function ConfigUpdater($version){
        $cfgpath = $this->getDataFolder() . "config.yml";
        $msgpath = $this->getDataFolder() . "messages.yml";
        if (file_exists($cfgpath)) {
            $cfgversion = $this->getConfig()->get("version");
            if ($cfgversion !== $version) {
                $this->getLogger()->info("Your config has been renamed to config-" . $cfgversion . ".yml and your messages file has been renamed to messages-" . $cfgversion . ".yml. That's because your config version wasn't the latest avable. So we created a new config and a new messages file for you!");
                rename($cfgpath, $this->getDataFolder() . "config-" . $cfgversion . ".yml");
                rename($msgpath, $this->getDataFolder() . "messages-" . $cfgversion . ".yml");
                $this->saveResource("config.yml");
                $this->saveResource("messages.yml");
            }
        } else {
            $this->saveResource("config.yml");
            $this->saveResource("messages.yml");
        }
    }

    private function loadClans()
    {
        foreach ($this->allClans() as $clan) {
            $name = substr($clan, 0, -4);
            $name = substr($name,  strlen($this->getDataFolder()."clans/"));
            $this->addClan($name);
        }
    }

    public function addPlayer(Player $player)
    {
        $this->players[$player->getName()] = new BCPlayer($this, $player);
    }

    public function removePlayer(BCPlayer $player)
    {
        unset($this->players[$player->getPlayer()->getName()]);
    }

    public function getPlayer($player): BCPlayer
    {
        return $this->players[$player instanceof Player ? $player->getName() : $player];
    }

    public function getPlayerNames(): Config
    {
        return $this->playernames;
    }

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

    public function createClan($name, BCPlayer $leader)
    {
        $cfg = new Config($this->getDataFolder() . 'clans/' . $name . '.yml', Config::YAML);
        $cfg->set('name', $name);
        $cfg->set('leader', strtolower($leader->getPlayer()->getName()));
        $cfg->set('members', [strtolower($leader->getPlayer()->getName())]);
        $cfg->save();
        $clan = new Clan($this, $name, $cfg, strtolower($leader->getPlayer()->getName()), [strtolower($leader->getPlayer()->getName())]);
        return $this->addClan($clan);
    }

    public function joinClan(BCPlayer $player, Clan $clan)
    {
        foreach ($clan->getMembers() as $member) {
            if (($mp = $this->getServer()->getPlayerExact($this->getPlayerName($member)))) {
                $mp->sendMessage(str_replace("{playername}", $player->getPlayer()->getName(), $this->getMessagesArray()["join_player_joined_clan"]));
            }
        }
        $clan->addMember($player);
        $player->setClan($clan);
    }

    public function getMessagesArray(): array
    {
        return $this->getMessages()->getAll();
    }

    public function getMessages(): Config
    {
        return $this->msgs;
    }

    public function getClan($cname): ?Clan
    {
        return isset($this->clans[$cname]) ? $this->clans[$cname] : null;
    }

    public function getClanByPlayer($player){
        return $this->getClan($this->getPlayersFile()->get(strtolower($player instanceof BCPlayer ? $player->getPlayer()->getName() : $player)));
    }

    public function deleteClan($clan)
    {
        if (!$clan instanceof Clan) {
            $clan = $this->getClan($clan);
        }
        $members = $clan->getMembers();
        $msgs = $this->getMessagesArray();
        foreach ($members as $member) {
            if (($member = $this->getServer()->getPlayerExact($member))) {
                $member->sendMessage($msgs["delete_clan_deleted_members"]);
                $member = $this->getPlayer($member->getName());
                $member->setClan(null);
            } else {
                $member = $this->getServer()->getOfflinePlayer($member);
                $this->setClan($member, null);
            }
        }
        unset($this->clans[$clan->getName()]);
        $file = $this->getPath().'clans/'.$clan->getName().'.yml';
        unset($clan);
        unlink($file);
        unset($file);
    }

    public function clanExist($clanname)
    {
        return isset($this->clans[$clanname]);
    }

    public function invite(BCPlayer $sender, BCPlayer $target)
    {
        $messages = $this->getMessagesArray();
        $sclan = $sender->getClan();
        $sname = $this->getPlayerName($sender);
        $message = str_replace("{clan}", $sclan->getName(), $messages["invite_were_invited"]);
        $message = str_replace("{player}", $sname, $message);
        $target->getPlayer()->sendMessage($message);
        $sclan->invite($target);

        $task = new invitetask($this, $sender, $target, isset($this->ConfigArray()["expire_time"]) ? $this->ConfigArray()["expire_time"] : 600);
        $handle = $this->getScheduler()->scheduleRepeatingTask($task, 1);
        $task->setHandler($handle);
    }

    public function expire(BCPlayer $sender, BCPlayer $target)
    {
        $sclan = $sender->getClan();
        $messages = $this->getMessagesArray();
        $sname = strtolower($sender->getPlayer()->getName());
        $message = str_replace("{clan}", $sclan->getName(), $messages["invite_invite_expired"]);
        $message = str_replace("{player}", $sname, $message);
        $target->getPlayer()->sendMessage($message);
        $tname = $target->getPlayer()->getName();
        $msg = str_replace("{player}", $tname, $messages["invite_invite_expired_sender"]);
        $sender->getPlayer()->sendMessage($msg);
        $sclan->removeInvite($target);
    }

    public function getPlayersFile(): Config
    {
        return $this->playersfile;
    }

    public function setClan($player, ?Clan $clan)
    {
        $clan = $clan === null ? $clan : $clan->getName();
        $this->getPlayersFile()->set($player instanceof IPlayer ? strtolower($player->getName()) : $player, $clan);
        $this->getPlayersFile()->save();
    }

    public function ConfigArray()
    {
        return $this->getConfig()->getAll();
    }

    public function allClans()
    {
        $clans = glob($this->getDataFolder() . "clans/*.yml");
        return $clans;
    }

    /**
     * @return BCPlayer[]
     */
    public function getPlayers(): array
    {
        return $this->players;
    }

    /**
     * @return Clan[]
     */
    public function getClans(): array
    {
        return $this->players;
    }

    public function getMessage($key){
        return $this->getMessagesArray()[$key];
    }

    public function onDisable(): void
    {
        foreach ($this->getPlayers() as $player) {
            $player->save();
        }
        foreach ($this->clans as $clan){
            $clan->save();
        }
    }
}
