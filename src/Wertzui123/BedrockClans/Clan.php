<?php

declare(strict_types=1);

namespace Wertzui123\BedrockClans;

use pocketmine\level\Location;
use pocketmine\Player;
use pocketmine\utils\Config;
use Wertzui123\BedrockClans\tasks\invitetask;

class Clan
{

    private $plugin;
    private $name;
    private $file;
    private $members;
    private $leader;
    private $invites = [];
    private $bank;
    private $home = null;
    public $homeLevel = null;

    /**
     * Clan constructor.
     * @param Main $plugin
     * @param string $name
     * @param Config|null $file
     * @param string|null $leader
     * @param string[]|null $members
     * @param int|null $bank
     * @param Location|null $home
     */
    public function __construct(Main $plugin, $name, $file = null, $leader = null, $members = null, $bank = null, $home = null)
    {
        $this->plugin = $plugin;
        $this->name = $name;
        $this->file = $file ?? new Config($this->plugin->getDataFolder() . 'clans/' . $this->name . '.json', Config::JSON);
        if (file_exists($this->plugin->getDataFolder() . 'clans/' . $this->name . '.yml')) {
            $this->file->setAll((new Config($this->plugin->getDataFolder() . "clans/" . $this->name . ".yml", Config::YAML))->getAll());
        }
        $this->leader = $leader ?? $this->getFile()->get('leader', '');
        $this->members = $members ?? $this->getFile()->get('members', []);
        if (!empty($this->members) && is_int(array_keys($this->members)[0])) {
            $members = [];
            foreach ($this->members as $member) {
                if ($this->getLeader() === strtolower($member)) {
                    $members[strtolower($member)] = "leader";
                } else {
                    $members[strtolower($member)] = "member";
                }
            }
            $this->members = $members;
        }
        $this->bank = $bank ?? $this->getFile()->get('bank', 0);
        if ($this->plugin->getConfig()->getNested('home.enabled', true) === true) {
            if (is_null($home) && $this->getFile()->exists('home')) {
                $this->homeLevel = $this->getFile()->getNested('home.world', 'world');
                $this->home = new Location($this->getFile()->getNested('home.x', 0), $this->getFile()->getNested('home.y', 0), $this->getFile()->getNested('home.z', 0), $this->getFile()->getNested('home.yaw', 0), $this->getFile()->getNested('home.pitch', 0), $this->plugin->getServer()->getLevelByName($this->homeLevel));
            } else {
                $this->home = $home;
            }
        }
    }

    /**
     * Returns the clan file
     * @return Config
     */
    public function getFile(): Config
    {
        return $this->file;
    }

    /**
     * Returns the clans name
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns a list of all members of the clan
     * The returned array contains the save-names of the players
     * @return string[]
     */

    public function getMembers(): array
    {
        return array_keys($this->members);
    }

    /**
     * Returns a list of all members of the clan
     * The returned array contains the real names of the players
     * @param bool $color [optional]
     * @return string[]
     */

    public function getMembersWithRealName($color = false): array
    {
        return array_map(function ($member) use ($color) {
            return ($color ? self::getRankColor($this->getRank($member)) : '') . $this->plugin->getPlayerName($member);
        }, array_keys($this->members));
    }

    /**
     * Adds a player to the clans members
     * @param BCPlayer|Player|string $player
     * @param string $rank
     * @return bool
     */
    public function addMember($player, $rank = 'member')
    {
        $player = strtolower($player instanceof BCPlayer ? $player->getPlayer()->getName() : ($player instanceof Player ? $player->getName() : $player));
        if (!in_array($player, $this->members)) {
            $this->members[$player] = strtolower($rank);
            return true;
        }
        return false;
    }

    /**
     * Adds a player to the clans members
     * @param BCPlayer|Player|string $player
     * @return bool
     */
    public function removeMember($player)
    {
        $player = strtolower($player instanceof BCPlayer ? $player->getPlayer()->getName() : ($player instanceof Player ? $player->getName() : $player));
        if (isset($this->members[$player])) {
            unset($this->members[$player]);
            return true;
        }
        return false;
    }

    /**
     * Updates the list of members of the clan
     * @param string[] $members
     */
    public function setMembers(array $members)
    {
        $this->members = $members;
    }

    /**
     * Returns the clan rank of the given player
     * @param BCPlayer|Player|string $player
     * @return string|null
     */
    public function getRank($player)
    {
        $player = strtolower($player instanceof BCPlayer ? $player->getPlayer()->getName() : ($player instanceof Player ? $player->getName() : $player));
        return $this->members[$player] ?? null;
    }

    /**
     * Updates the clan rank of the given player
     * @param BCPlayer|Player|string $player
     * @param string $rank
     */
    public function setRank($player, $rank)
    {
        $player = strtolower($player instanceof BCPlayer ? $player->getPlayer()->getName() : ($player instanceof Player ? $player->getName() : $player));
        $this->members[$player] = $rank;
    }

    /**
     * Returns the save-name of the clan leader
     * @return string
     */
    public function getLeader()
    {
        return $this->leader;
    }

    /**
     * Returns the real name of the clan leader
     * @return string
     */
    public function getLeaderWithRealName()
    {
        return $this->plugin->getPlayerName($this->leader);
    }

    /**
     * Updates the clan leader
     * @param BCPlayer|Player|string $player
     */
    public function setLeader($player)
    {
        $this->leader = strtolower($player instanceof BCPlayer ? $player->getPlayer()->getName() : ($player instanceof Player ? $player->getName() : $player));
    }

    /**
     * Returns whether a given player is invited to this clan
     * @param BCPlayer $player
     * @return bool
     */
    public function isInvited(BCPlayer $player)
    {
        return in_array($player, $this->invites, true);
    }

    /**
     * Returns a list of all players currently invited to this clan
     * @return BCPlayer[]
     */
    public function getInvites(): array
    {
        return $this->invites;
    }

    /**
     * Updates the list of all players invited to this clan
     * @param BCPlayer[] $invites
     */
    public function setInvites(array $invites)
    {
        $this->invites = $invites;
    }

    /**
     * Invites a player to the clan
     * @param BCPlayer $player
     */
    public function addInvite(BCPlayer $player)
    {
        if (!in_array($player, $this->invites, true)) $this->invites[] = $player;
    }

    /**
     * Removes a player from being invited to the clan
     * @param BCPlayer $player
     */
    public function removeInvite(BCPlayer $player)
    {
        unset($this->invites[array_search($player, $this->invites, true)]);
    }

    /**
     * Returns how much money is stored on the clan bank
     * @return int
     */
    public function getBank(): int
    {
        return $this->bank;
    }

    /**
     * Updates how much money is stored on the clan bank
     * @param int $bank
     */
    public function setBank(int $bank)
    {
        $this->bank = $bank;
    }

    /**
     * Invites a player to this clan
     * @param BCPlayer $sender
     * @param BCPlayer $target
     */
    public function invite(BCPlayer $sender, BCPlayer $target)
    {
        $target->getPlayer()->sendMessage(Main::getInstance()->getMessage('command.invite.target', ['{clan}' => $this->getName(), '{player}' => $sender->getPlayer()->getName()]));
        $this->addInvite($target);
        $task = new invitetask(Main::getInstance(), $sender, $target, Main::getInstance()->getConfig()->get('invitation_expire_time') * 20);
        $handle = Main::getInstance()->getScheduler()->scheduleRepeatingTask($task, 1);
        $task->setHandler($handle);
    }

    /**
     * Returns the clan's home
     * @return Location|null
     */
    public function getHome(): ?Location
    {
        return $this->home;
    }

    /**
     * Updates the clan's home
     * @param Location|null $position
     */
    public function setHome(?Location $position)
    {
        $this->home = $position;
    }

    /**
     * Saves the clan to memory
     */
    public function save()
    {
        $file = $this->getFile();
        $file->set('name', $this->getName());
        $file->set('members', $this->members); // not getMembers() because it doesn't return the ranks
        $file->set('leader', $this->getLeader());
        $file->set('bank', $this->getBank());
        if (!is_null($home = $this->getHome())) {
            $file->set('home', ['x' => $home->getX(), 'y' => $home->getY(), 'z' => $home->getZ(), 'world' => $home->getLevel()->getFolderName(), 'yaw' => $home->getYaw(), 'pitch' => $home->getPitch()]);
        }
        $file->save();
        unset($file);
    }

    /**
     * Returns the save (or display) name fpr all clan-ranks
     * @param bool $displayName [optional}
     * @return string[]
     */
    public static function getRanks(bool $displayName = false): array
    {
        if ($displayName) {
            return ['member' => self::getRankName('member'), 'vim' => self::getRankName('vim'), 'coleader' => self::getRankName('coleader'), 'leader' => self::getRankName('leader')];
        } else {
            return ['member', 'vim', 'coleader', 'leader'];
        }
    }

    /**
     * Returns the display name of the given rank
     * @param string $rank
     * @param bool $color [optional]
     * @return string|false
     */
    public static function getRankName(string $rank, bool $color = false)
    {
        if ($color) {
            return self::getRankColor($rank) . Main::getInstance()->getConfig()->getNested('ranks.' . strtolower($rank) . '.name');
        }
        return Main::getInstance()->getConfig()->getNested('ranks.' . strtolower($rank) . '.name');
    }

    /**
     * Returns the color of the given rank
     * @param string $rank
     * @return string|false
     */
    public static function getRankColor(string $rank)
    {
        return Main::getInstance()->getConfig()->getNested('ranks.' . strtolower($rank) . '.color');
    }

    /**
     * Returns whether the given name is a valid clan name
     * @param string $name
     * @return bool
     */
    public static function isValidName(string $name): bool
    {
        return !in_array($name, Main::getInstance()->getConfig()->get('banned_clan_names'));
    }

    /**
     * Returns the maximum amount of money which can be withdrawn from the clan bank with the given rank
     * @param string $rank
     * @return int
     */
    public static function getMaxWithdrawAmount(string $rank): int
    {
        return Main::getInstance()->getConfig()->getNested('bank.withdraw.maximum.' . $rank, 0);
    }

}