<?php

declare(strict_types=1);

namespace Wertzui123\BedrockClans;

use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\event\player\PlayerJoinEvent;
use Wertzui123\BedrockClans\commands\clancmd;
use Wertzui123\BedrockClans\tasks\invitetask;


/*Copyright by Wertzui123 2019
Change nothing, copy nothing etc.
My plugin!*/


class Main extends PluginBase{

    public $tasks;

public function onEnable() : void{
    $this->saveResource("config.yml");
    $this->saveResource("messages.yml");
    @mkdir($this->getDataFolder()."clans");
    $this->getServer()->getCommandMap()->register("clancmd", new clancmd($this));
}

public function onPlayerJoinEvent(PlayerJoinEvent $event)
{

    $player = $event->getPlayer();
    $pname = strtolower($player->getName());

    if (!file_exists($this->getDataFolder() . "players.yml")) {
        $pconfig = new Config($this->getDataFolder() . "players.yml", Config::YAML);
        $pconfig->set($pname, null);
        $pconfig->save();
    } else {

        $pconfig = new Config($this->getDataFolder() . "players.yml", Config::YAML);
        $pcname = $pconfig->get($pname);

        if ($pcname = null) {
            $pconfig->set($pname, null);
            $pconfig->save();
        }
    }
}

public function JoinClan(Player $player, $clan){
    $cconfig =  new Config($this->getDataFolder()."clans/$clan.yml", Config::YAML);
	$members = $cconfig->get("members");
	$messages = $this->getMessagesArray();
	foreach($members as $member){
        if($this->getServer()->getPlayer($member)){
		    $member = $this->getServer()->getPlayer($member);
		    $msg = str_replace("{playername}", $player->getName(), $messages["join_player_joined_clan"]);
		$member->sendMessage($msg);
		}
	}
	$this->setClan($player, $clan);
}

public function getMessagesArray(){
    $msgs = new Config($this->getDataFolder()."messages.yml", Config::YAML);
    $msgs = $msgs->getAll();
    return $msgs;
}

    public function getMessages(){
        $msgs = new Config($this->getDataFolder()."messages.yml", Config::YAML);
        return $msgs;
    }

public function getClan($player)
{
    $players = new Config($this->getDataFolder() . "players.yml", Config::YAML);
    $pclan = $players->get(strtolower($player->getName()));
    $clan = new Config($this->getDataFolder() . "clans/$pclan.yml", Config::YAML);
    return $clan;
}

    public function getClanByName($cname)
    {
        $clan = new Config($this->getDataFolder() . "clans/$cname.yml", Config::YAML);
        return $clan;
    }

public function isInClan($player){
    $players = new Config($this->getDataFolder() . "players.yml", Config::YAML);
    $pname = strtolower($player->getName());
    $pclan = $players->get($pname);
    if($pclan == null){
        return false;
    }else{
        return true;
    }
}

public function deleteClan($clanname){
    $clan = new Config($this->getDataFolder()."clans/".$clanname.".yml", Config::YAML);
    $members = $clan->get("members");
    $msgs = $this->getMessagesArray();
    foreach($members as $member) {
        if ($this->getServer()->getPlayerExact($member)) {
            $member = $this->getServer()->getPlayerExact($member);
            $member->sendMessage($msgs["delete_clan_deleted_members"]);
            $this->setClan($member, null);
        }else {
            $member = $this->getServer()->getOfflinePlayer($member);
            $this->setClan($member, null);
        }
    }
    if($this->clanExist($clanname))
        unlink($this->getDataFolder()."clans/".$clanname.".yml");
}

public function clanExist($clanname){
    if(file_exists($this->getDataFolder()."clans/$clanname.yml")) {
        return true;
    }else{
        return false;
    }
}

public function invite(Player $sender, Player $target){
    $messages = new Config($this->getDataFolder()."messages.yml", Config::YAML);
    $messages = $messages->getAll();
    $sclan = $this->getClan($sender);
    $sname = strtolower($sender->getName());
    $message = str_replace("{clan}", $sclan->get("name"), $messages["invite_were_invited"]);
    $message = str_replace("{player}", $sname, $message);
    $target->sendMessage($message);
    $cconfig = $this->getClan($sender);
    $invites = $cconfig->get("invites");
    array_push($invites, strtolower($target->getName()));
    $cconfig->set("invites", $invites);
    $cconfig->save();

    $task = new invitetask($this, $sender, $target, $this->ConfigArray()["expire_time"] ?? 600);
    $var = $this->getScheduler()->scheduleRepeatingTask($task, 1);
    $task->setHandler($var);
    $this->tasks[$task->getTaskId()] = $task->getTaskId();
}

public function expire(Player $sender, Player $target){
    $sclan = $this->getClan($sender);
    $messages = $this->getMessagesArray();
    $sname = strtolower($sender->getName());
    $message = str_replace("{clan}", $sclan->get("name"), $messages["invite_invite_expired"]);
   $message = str_replace("{player}", $sname, $message);
   $target->sendMessage($message);
   $tname = strtolower($target->getName());
   $msg = str_replace("{player}", $tname, $messages["invite_invite_expired_sender"]);
   $sender->sendMessage($msg);
   $invites = $sclan->get("invites");
   unset($invites[array_search($tname, $invites)]);
   $sclan->set("invites", $invites);
   $sclan->save();
}

    public function removeTask($id) {
        //Reomves the task from your array of tasks
        unset($this->tasks[$id]);
        //Cancels the task and stops it from running
        $this->getScheduler()->cancelTask($id);
    }

	public function isLeader($player){
	$clan = $this->getClan($player);
if($clan->get("leader") == strtolower($player->getName()){
return true;	
}else{
	return false;
	}
   }
	
public function setClan($player, $clanname)
{
    $players = new Config($this->getDataFolder() . "players.yml", Config::YAML);
    $pname = strtolower($player->getName());
    if(!$clanname == null) {
        $cconfig = new Config($this->getDataFolder() . "clans/$clanname.yml", Config::YAML);
        $members = $cconfig->get("members");
        if($this->isInClan($player)) {
            $aclan = $this->getClan($player);
            $members = $aclan->get("members");
            unset($members[array_search($pname, $members)]);
            $aclan->set("members", $members);
            $aclan->save();
        }
        if (!in_array($pname, $members)) {
            array_push($members, $pname);
            $cconfig->set("members", $members);
            $cconfig->save();
        }
    }
    if($this->isInClan($player)) {
        $clan = $this->getClan($player);
        $members = $clan->get("members");
        unset($members[array_search($pname, $members)]);
        $clan->set("members", $members);
        $clan->save();
    }
    $players->set($pname, $clanname);
    $players->save();
}

public function isInvited(Player $player, Config $clan){
    if(in_array(strtolower($player->getName()), $clan->get("invites"))){
        return true;
    }else{
        return false;
    }
}

public function Config(){
    $config = new Config($this->getDataFolder()."config.yml", Config::YAML);
    return $config;
}

    public function ConfigArray(){
        $config = new Config($this->getDataFolder()."config.yml", Config::YAML);
        $config = $config->getAll();
        return $config;
    }

    public function allClans(){
    $clans = glob($this->getDataFolder(). "clans/*.yml");
    return $clans;
    }

	public function onDisable() : void{
		$this->getLogger()->info("Bye");
		foreach ($this->allClans() as $clan){
		    $clan = new Config($clan, Config::YAML);
		    $clan->set("invites", []);
		    $clan->save();
        }
	}
}
