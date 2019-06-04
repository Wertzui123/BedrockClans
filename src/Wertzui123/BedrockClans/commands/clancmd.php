<?php

declare(strict_types=1);

namespace Wertzui123\BedrockClans\commands;

use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\event\Listener;
use pocketmine\scheduler\TaskScheduler;
use pocketmine\utils\Config;
use pocketmine\player;
use pocketmine\level\Position;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use Wertzui123\BedrockClans\Main;
use pocketmine\OfflinePlayer;

class clancmd extends Command
{

    public $plugin;

    public function __construct(Main $plugin)
    {
        parent::__construct("clan", "Create, delete and manage clans", null, ["clans", "bclan"]);
        $this->setPermission("bedrockclans.cmd");
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        $messages = $this->plugin->getMessagesArray();
        $config = $this->plugin->ConfigArray();
        $configstring = $this->plugin->Config();

        if($sender instanceof Player){

            $pconfig = new Config($this->plugin->getDataFolder()."players.yml", Config::YAML);

            if(empty($args[0])){
                $sender->sendMessage($configstring->get("usage"));
            } else {
                if($args[0] == "create"){

                    if($this->plugin->isInClan($sender) == false){

                        if(empty($args[1])){

                            $sender->sendMessage($messages["create_provide_name"]);
                        }else{

                            if(!$this->plugin->ClanExist($args[1])){
                                if(!in_array($args[1], $config["banned_clan_names"])) {

                                    $sender->sendMessage($messages["create_create_succes"]);
                                    $cconfig = new Config($this->plugin->getDataFolder() . "clans/$args[1].yml", Config::YAML);
                                    $pname = strtolower($sender->getName());
                                    $pconfig = new Config($this->plugin->getDataFolder() . "players.yml", Config::YAML);
                                    $members = [$pname];

                                    $cconfig->set("members", $members);
                                    $cconfig->set("leader", $pname);
                                    $cconfig->set("name", $args[1]);
                                    $cconfig->set("invites", []);
                                    $cconfig->save();
                                    $pconfig->set("$pname", $args[1]);
                                    $pconfig->save();
                                }else{
                                    $sender->sendMessage($messages["create_name_is_banned"]);
                                }
                            }else{
                                $sender->sendMessage($messages["create_already_exist"]);
                            }
                        }

                    }else{
                        $sender->sendMessage($messages["create_already_in_clan"]);
                    }


                    //    /CLAN INFO    //
                }elseif($args[0] == "info"){

                    if(!isset($args[1])){
                        if(!$this->plugin->isInClan($sender)){
                            $sender->sendMessage($messages["info_not_in_clan"]);
                        }else{

                            $clan = $this->plugin->getClan($sender);
                            $members = $clan->get("members");
                            $leader = $clan->get("leader");
                            $cname = $clan->get("name");
                            $memberss = implode(", ", $members);
                            $m = str_replace("{clanname}", $cname, $messages["info_info_about_your_clan"]);
                            $m = str_replace("{leader}", $leader, $m);
                            $m = str_replace("{members}", $memberss, $m);

                            $sender->sendMessage($m);
                        }
                    }else{

                        if(!$this->plugin->clanExist($args[1])){

                            $sender->sendMessage($messages["info_clan_does_not_exist"]);

                        }else{

                            $clan =  $this->plugin->getClanByName($args[1]);
                            $members = $clan->get("members");
                            $leader = $clan->get("leader");
                            $cname = $clan->get("name");
                            $memberss = implode(", ", $members);
                            $m = str_replace("{clanname}", $cname, $messages["info_info_about_an_other_clan"]);
                            $m = str_replace("{leader}", $leader, $m);
                            $m = str_replace("{members}", $memberss, $m);

                            $sender->sendMessage($m);

                        }

                    }

                }elseif($args[0] == "join"){
                    if($sender->hasPermission("bedrockclans.join")) {
                        if (!empty($args[1])) {

                            if (!$this->plugin->clanExist($args[1])) {

                                $sender->sendMessage($messages["join_clan_does_not_exist"]);

                            } else {

                                if ($this->plugin->isInClan($sender) == false) {
                                    $this->plugin->joinClan($sender, $args[1]);
                                    $jc = str_replace("{clan}", $args[1], $messages["join_joined_clan"]);
                                    $sender->sendMessage($jc);
                                } else {
                                    $sender->sendMessage($messages["join_already_in_clan"]);
                                }
                            }

                        }else{
                            $sender->sendMessage($messages["join_provide_clan"]);
                        }
                    }else{
                        $sender->sendMessage($config["usage"]);
                    }

                }elseif($args[0] == "leave"){

                    $name = strtolower($sender->getName());
                    $cnname = $pconfig->get($name);
                    $cconfig = new Config($this->plugin->getDataFolder()."clans/$cnname.yml", Config::YAML);

                    if($this->plugin->isInClan($sender)){
                        if($cconfig->get("leader") !== $name){

                            $this->plugin->setClan($sender, null);
                            $sender->sendMessage($messages["leave_leaved_clan"]);
                            foreach($cconfig->get("members") as $member){
                                if($this->plugin->getServer()->getPlayerExact($member)){
                                    $p = $this->plugin->getServer()->getPlayerExact($member);
                                    $msg = str_replace("{player}", $name, $messages["leave_player_leaved_clan"]);
                                    $p->sendMessage($msg);
                                }
                            }

                        }else{
                            $sender->sendMessage($messages["leave_cannot_leave"]);
                        }
                    }else{
                        $sender->sendMessage($messages["leave_not_in_clan"]);
                    }

                }elseif($args[0] == "leader"){


                    $name = strtolower($sender->getName());
                    $cnname = $pconfig->get($name);
                    $cconfig = $this->plugin->getClan($sender);
                    $members = $cconfig->get("members");

                    if($this->plugin->isInClan($sender)){
                        if($cconfig->get("leader") == $name){
                            if(!empty($args[1])){
                                if(in_array(strtolower($args[1]), $members)){

                                    $cconfig->set("leader", strtolower($args[1]));
                                    $ps = str_replace("{player}", $args[1], $messages["leader_promoted_to_leader"]);
                                    $sender->sendMessage($ps);
                                    if($this->plugin->getServer()->getPlayerExact($args[1]) or $this->plugin->getServer()->getOfflinePlayer(strtolower($args[1]))){
                                        if($this->plugin->getServer()->getPlayerExact($args[1])) {
                                            $target = $this->plugin->getServer()->getPlayerExact($args[1]);
                                            $target->sendMessage($messages["leader_now_leader"]);
                                        }
                                    }
                                    $cconfig->save();

                                }else{
                                    $message = str_replace("{player}", $args[1], $messages["leader_not_in_your_clan"]);
                                    $sender->sendMessage($message);
                                }
                            }else{
                                $sender->sendMessage($messages["leader_provide_player"]);
                            }

                        }else{
                            $sender->sendMessage($messages["leader_not_leader"]);
                        }
                    }else{
                        $sender->sendMessage($messages["leader_not_in_clan"]);
                    }

                }elseif($args[0] == "chat"){
                    if(!empty($args[1])){

                        if(!$this->plugin->isInClan($sender)){
                            $sender->sendMessage($messages["chat_not_in_clan"]);
                        }else{

                            $clan = $this->plugin->getClan($sender);
                            $members = $clan->get("members");
                            $memberstring = implode(", ", $members);
                            $argschat = $args;
                            unset($argschat[0]);
                            $message = implode(" ", $argschat);
                            foreach($members as $membersforeach){
                                $leader = $clan->get("leader");
                                $cname = $clan->get("name");

                                    $member = $this->plugin->getServer()->getPlayer($membersforeach);

                                    $format = str_replace(["{name}", "{message}"], [strtolower($sender->getName()), $message], $config["clan_chat_format"]);
                                    if($member instanceof Player){
$member->sendMessage($format);
                                    }
                            }
                        }
                    }else{
                        $sender->sendMessage($messages["chat_provide_message"]);
                    }

                }elseif($args[0] == "kick"){
                    if(!empty($args[1])){

                        $name = strtolower($sender->getName());
                        $kicked = strtolower($args[1]);

                        if($this->plugin->isInClan($sender)){
                            $cconfig = $this->plugin->getClan($sender);
                            if($cconfig->get("leader") == $name){

                                $members = $cconfig->get("members");

                                if($kicked == $cconfig->get("leader")){

                                    $sender->sendMessage($messages["kick_cannot_kick_leader"]);

                                }else{

                                    if(in_array($kicked, $members)){
                                        if($this->plugin->getServer()->getOfflinePlayer($kicked)){ //player is offline
                                            $kicked = $this->plugin->getServer()->getOfflinePlayer($kicked);
                                            $this->plugin->setClan($kicked, null);
                                            $msg = str_replace("{player}", $kicked->getName(), $messages["kick_kicked_sender"]);
                                            $sender->sendMessage($msg);
                                        }elseif($this->plugin->getServer()->getPlayer($kicked)){ //player is online
                                            $kicked = $this->plugin->getServer()->getPlayer($kicked);
                                            $this->plugin->setClan($kicked, null);
                                            $sname = strtolower($sender->getName());
                                            $message = str_replace("{player}", $sname, $messages["kick_kicked"]);
                                            $kicked->sendMessage($message);
                                            $msg = str_replace("{player}", $kicked, $messages["kick_kicked_sender"]);
                                            $sender->sendMessage($msg);
                                        }else{
                                            $sender->sendMessage($messages["kick_not_in_clan"]);
                                        }
                                    }else{
                                        $sender->sendMessage($messages["kick_player_not_exists"]);

                                    }
                                }
                            }else{
                                $sender->sendMessage($messages["kick_not_leader"]);
                            }

                        }else{
                            $sender->sendMessage($messages["kick_not_in_clan"]);
                        }

                    }else{
                        $sender->sendMessage($messages["kick_provide_player"]);
                    }

                }elseif($args[0] == "delete"){

                    if($this->plugin->isInClan($sender)){

                        $name = strtolower($sender->getName());
                        $cnname = $pconfig->get($name);
                        $cconfig = $this->plugin->getClan($sender);

                        if($cconfig->get("leader") == $name){

                            $this->plugin->deleteClan($cnname);

                        }else{
                            $sender->sendMessage($messages["delete_not_leader"]);
                        }
                    }else{
                        $sender->sendMessage($messages["delete_not_in_clan"]);
                    }
                }elseif($args[0] == "invite"){

                    if($this->plugin->isInClan($sender)){

                        if(!empty($args[1])){

                            $sname = strtolower($sender->getName());
                            $cconfig = $this->plugin->getClan($sender);
                            $cname = $cconfig->get("name");
                            $members = $cconfig->get("members");

                            if(!in_array($args[1], $members)){
                                    if ($this->plugin->getServer()->getPlayerExact($args[1])) {
                                        if(!$this->plugin->isInClan($this->plugin->getServer()->getPlayerExact($args[1]))) {

                                        $invited = $this->plugin->getServer()->getPlayerExact($args[1]);
                                        $this->plugin->invite($sender, $invited);
                                        $msg  = str_replace("{player}", strtolower($invited->getName()), $messages["invite_invited"]);
                                        $sender->sendMessage($msg);

                                    } else {
                                        $sender->sendMessage($messages["invite_already_in_clan"]);
                                    }
                                }else{
                                        $sender->sendMessage($messages["invite_player_does_not_exist"]);
                                }
                            }else{
                                $sender->sendMessage($messages["invite_in_same_clan"]);
                            }
                        }else{
                            $sender->sendMessage($messages["invite_provide_player"]);
                        }
                    }else{
                        $sender->sendMessage($messages["invite_not_in_clan"]); //Message file???
                    }

                }elseif($args[0] == "accept"){

                    if(!empty($args[1])){

                        if(!$this->plugin->isInClan($sender)) {

                            if ($this->plugin->clanExist($args[1])) {

                                $cconfig = new Config($this->plugin->getDataFolder() . "clans/$args[1].yml", Config::YAML);
                                $invites = [];
                                $invites = $cconfig->get("invites");
                                $sname = strtolower($sender->getName());
                                $cname = $cconfig->get("name");
                                if (in_array($sname, $invites)) {

                                    $message = str_replace("{clan}", $cname, $messages["accept_accepted_invite"]);
                                    $sender->sendMessage($message);
                                    unset($invites[array_search($args[1], $invites)]);
                                    $cconfig->set("invites", $invites);
                                    $cconfig->save();
                                    $this->plugin->joinClan($sender, $cname);

                                } else {
                                    $sender->sendMessage($messages["accept_not_invited"]);
                                }
                            } else {
                                $sender->sendMessage($messages["accept_clan_does_not_exist"]);
                            }
                        }else{
                            $sender->sendMessage($messages["accept_already_in_clan"]);
                        }
                    }else{
                        $sender->sendMessage($messages["accept_provide_clan"]);
                    }

                }elseif($args[0] == "about"){

                    $sender->sendMessage("§cBedrock§bClans §awas written by Wertzui123. Your using version §71.0§a. Thanks for downloading (:");

                }elseif($args[0] == "help"){
                    $sender->sendMessage($messages["help_help_list"]);

                }else{
                    $sender->sendMessage($config["usage"]);
                }
            }
        }else{
            $sender->sendMessage($messages["run_in_game"]);
        }
    }
}
