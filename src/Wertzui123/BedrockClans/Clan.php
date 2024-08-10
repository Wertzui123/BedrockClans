<?php

declare(strict_types=1);

namespace Wertzui123\BedrockClans;

use pocketmine\entity\Location;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use Wertzui123\BedrockClans\events\clan\ClanColorChangeEvent;
use Wertzui123\BedrockClans\events\clan\ClanMinimumInviteRankChangeEvent;
use Wertzui123\BedrockClans\events\player\OfflinePlayerRankChangeEvent;
use Wertzui123\BedrockClans\events\player\PlayerRankChangeEvent;
use Wertzui123\BedrockClans\tasks\InviteTask;

class Clan
{

    /** @var Main */
    private $plugin;
    /** @var string */
    private $name;
    /** @var Config */
    private $file;
    /** @var int */
    private $creationDate;
    /** @var array */
    private $members;
    /** @var string */
    private $leader;
    /** @var string */
    public $color = 'f';
    /** @var BCPlayer[] */
    private $invites = [];
    /** @var string */
    private $minimumInviteRank;
    /** @var int */
    private $bank;
    /** @var Location|null */
    private $home = null;
    /** @var string */
    public $homeLevel = null;
    /** @var bool */
    public bool $deleted = false;

    /**
     * Clan constructor.
     * @param Main $plugin
     * @param string $name
     * @param Config|null $file
     * @param string|null $leader
     * @param string[]|null $members
     * @param string|null $minimumInviteRank
     * @param string|null $color
     * @param int|null $bank
     * @param Location|null $home
     */
    public function __construct(Main $plugin, string $name, ?Config $file = null, ?int $creationDate = null, ?string $leader = null, ?array $members = null, ?string $minimumInviteRank = null, ?string $color = null, ?int $bank = null, ?Location $home = null)
    {
        $this->plugin = $plugin;
        $this->name = $name;
        $this->file = $file ?? new Config($this->plugin->getDataFolder() . 'clans/' . $this->name . '.json', Config::JSON);
        if (file_exists($this->plugin->getDataFolder() . 'clans/' . $this->name . '.yml')) {
            $this->file->setAll((new Config($this->plugin->getDataFolder() . 'clans/' . $this->name . '.yml', Config::YAML))->getAll());
        }
        $this->creationDate = $creationDate ?? $this->getFile()->get('creationDate', -1);
        $this->leader = $leader ?? $this->getFile()->get('leader', '');
        $this->members = $members ?? $this->getFile()->get('members', []);
        if (!empty($this->members) && is_int(array_keys($this->members)[0])) {
            $members = [];
            foreach ($this->members as $member) {
                if ($this->getLeader() === strtolower($member)) {
                    $members[strtolower($member)] = 'leader';
                } else {
                    $members[strtolower($member)] = 'member';
                }
            }
            $this->members = $members;
        }
        $this->minimumInviteRank = $minimumInviteRank ?? $this->getFile()->get('minimumInviteRank', $plugin->getConfig()->get('default_minimum_invite_rank', 'member'));
        $this->color = $color ?? $this->getFile()->get('color', 'f');
        $this->bank = $bank ?? $this->getFile()->get('bank', 0);
        if ($this->plugin->getConfig()->getNested('home.enabled', true) === true) { // TODO: This will delete already existing homes when the config value is changed to false
            if (is_null($home) && $this->getFile()->exists('home')) {
                $this->homeLevel = $this->getFile()->getNested('home.world', 'world');
                $this->home = new Location($this->getFile()->getNested('home.x', 0), $this->getFile()->getNested('home.y', 0), $this->getFile()->getNested('home.z', 0), $this->plugin->getServer()->getWorldManager()->getWorldByName($this->homeLevel), $this->getFile()->getNested('home.yaw', 0), $this->getFile()->getNested('home.pitch', 0));
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
     * Returns the clans name with all formatting already done
     * @param string $color [optional]
     * @return string
     */
    public function getDisplayName(string $color = null)
    {
        return '§' . ($color ?? $this->getColor()) . $this->name . '§f';
    }

    /**
     * Returns the date this clan was created (unix timestamp)
     * @return int
     */
    public function getCreationDate()
    {
        return $this->creationDate;
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
     * @param bool $force [optional]
     * @return bool
     */
    public function setRank($player, $rank, $force = false)
    {
        $playerName = strtolower($player instanceof BCPlayer ? $player->getPlayer()->getName() : ($player instanceof Player ? $player->getName() : $player));
        if ($this->members[$playerName] !== null && !$force) {
            if ($player instanceof BCPlayer) $player = $player->getPlayer();
            if ($player instanceof Player) {
                $event = new PlayerRankChangeEvent($player, $this->members[$playerName], $rank);
                $event->call();
                if ($event->isCancelled()) return false;
            } else {
                $event = new OfflinePlayerRankChangeEvent($playerName, $this->members[$playerName], $rank);
                $event->call();
                if ($event->isCancelled()) return false;
            }
        }
        $this->members[$playerName] = $rank;
        return true;
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
     * Returns the color of this clan
     * @return string
     */
    public function getColor(): string
    {
        return $this->color;
    }

    /**
     * Changes the color of this clan
     * @param string $color
     * @return bool
     */
    public function setColor(string $color)
    {
        $event = new ClanColorChangeEvent($this, $this->color, $color);
        $event->call();
        if ($event->isCancelled()) return false;
        $this->color = $color;
        return true;
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
     * Returns the minimum rank required to invite players to the clan
     * @return string
     */
    public function getMinimumInviteRank(): string
    {
        return $this->minimumInviteRank;
    }

    /**
     * Changes the minimum rank required to invite players to the clan
     * @param string $rank
     * @return bool
     */
    public function setMinimumInviteRank(string $rank)
    {
        $event = new ClanMinimumInviteRankChangeEvent($this, $this->minimumInviteRank, $rank);
        $event->call();
        if ($event->isCancelled()) return false;
        $this->minimumInviteRank = $rank;
        return true;
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
        $target->getPlayer()->sendMessage(Main::getInstance()->getMessage('command.invite.target', ['{clan}' => $this->getDisplayName(), '{player}' => $sender->getPlayer()->getName()]));
        $this->addInvite($target);
        $task = new InviteTask(Main::getInstance(), $sender, $target, $sender->getClan());
        $handle = Main::getInstance()->getScheduler()->scheduleDelayedTask($task, Main::getInstance()->getConfig()->get('invitation_expire_time') * 20);
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
        $file->set('creationDate', $this->getCreationDate());
        $file->set('members', $this->members); // not getMembers() because it doesn't return the ranks
        $file->set('leader', $this->getLeader());
        $file->set('minimumInviteRank', $this->getMinimumInviteRank());
        $file->set('color', $this->getColor());
        $file->set('bank', $this->getBank());
        if (!is_null($home = $this->getHome())) {
            $file->set('home', ['x' => $home->getX(), 'y' => $home->getY(), 'z' => $home->getZ(), 'world' => $home->getWorld()->getFolderName(), 'yaw' => $home->getYaw(), 'pitch' => $home->getPitch()]);
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
     * Converts a rank to a number (for later arithmetic comparison)
     * @param string $rank
     * @return int
     */
    public static function rankToNumber(string $rank)
    {
        switch ($rank) {
            case 'member':
                return 0;
            case 'vim':
                return 1;
            case 'coleader':
                return 2;
            case 'leader':
                return 3;
            default:
                throw new \InvalidArgumentException("There is no rank called $rank");
        }
    }

    /**
     * Returns whether the given name is a valid clan name
     * @param string $name
     * @return bool
     */
    public static function isValidName(string $name): bool
    {
        return preg_match(Main::getInstance()->getConfig()->get('clan_name_regex'), $name) && !in_array($name, Main::getInstance()->getConfig()->get('banned_clan_names'));
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