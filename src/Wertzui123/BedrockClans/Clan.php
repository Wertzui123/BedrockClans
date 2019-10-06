<?php

declare(strict_types=1);

namespace Wertzui123\BedrockClans;

use pocketmine\Player;
use pocketmine\utils\Config;

class Clan
{

    private $plugin;
    private $name;
    private $config;
    private $members;
    private $leader;
    public $invites = [];

    public function __construct(Main $plugin, $name, $cfg = null, $leader = null, $members = null)
    {
        $this->plugin = $plugin;
        $this->name = $name;
        $this->config = $cfg ?? new Config($this->plugin->getDataFolder() . "clans/" . $this->name . ".yml", Config::YAML);
        $this->leader = $leader ?? $this->getConfig()->get('leader');
        $this->members = $members ?? $this->getConfig()->get('members');
    }


    public function getConfig(): Config
    {
        return $this->config;
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string[]
     */

    public function getMembers(): array
    {
        return $this->members;
    }

    /**
     * @return string[]
     */

    public function getMembersWithRealName(): array
    {
        $members = [];
        foreach ($this->members as $index => $member) {
            $members[$index] = $this->plugin->getPlayerName($member);
        }
        return $members;
    }

    public function addMember($player)
    {
        if($player instanceof BCPlayer) $player = $player->getPlayer()->getName(); else if($player instanceof Player) $player = $player->getName();
        $player = strtolower($player);
        if (!in_array($player, $this->members)) {
            array_push($this->members, $player);
        }
    }

    public function removeMember($player)
    {
        if($player instanceof BCPlayer) $player = $player->getPlayer()->getName(); else if($player instanceof Player) $player = $player->getName();
        $player = strtolower($player);
        if (in_array($player, $this->members)) {
            unset($this->members[array_search($player, $this->members)]);
        }
    }

    public function setMembers(array $members){
        $this->members = $members;
    }

    public function getLeaderWithRealName(){
        return $this->plugin->getPlayerName($this->leader);
    }

    public function getLeader(){
        return $this->leader;
    }

    public function setLeader($player){
        if($player instanceof BCPlayer){
            $player = strtolower($player->getPlayer()->getName());
        }elseif($player instanceof Player){
            $player = strtolower($player->getName());
        }else {
            $player = strtolower($player);
        }
        $this->leader = $player;
    }


    /**
     * @return BCPlayer[]
     */
    public function getInvites() : array {
        return $this->invites;
    }

    public function setInvites(array $invites){
        $this->invites = $invites;
    }

    public function invite(BCPlayer $player){
        if(!in_array($player, $this->invites)) $this->invites[] = $player;
    }

    public function removeInvite(BCPlayer $player){
        unset($this->invites[array_search($player, $this->invites)]);
    }

    public function isInvited(BCPlayer $player){
        return in_array($player, $this->invites);
    }

    public function save(){
        $cfg = $this->getConfig();
        $cfg->set("name", $this->getName());
        $cfg->set("members", $this->getMembers());
        $cfg->set("leader", $this->getLeader());
        $cfg->save();
        unset($cfg);
        //Save's the clan members, the clan name and the clan leader.
    }
}