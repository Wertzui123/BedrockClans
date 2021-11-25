<?php

declare(strict_types=1);

namespace Wertzui123\BedrockClans;

use pocketmine\Player;
use pocketmine\Server;
use Wertzui123\BedrockClans\events\player\ClanJoinEvent;
use Wertzui123\BedrockClans\events\player\PlayerClanChatEvent;

class BCPlayer
{

    /** @var Main */
    private $plugin;
    /** @var Player */
    private $player;
    /** @var Clan|null */
    private $clan;
    /** @var int */
    private $nextPossibleWithdrawTimestamp;
    /** @var bool */
    private $chatting = false;

    /**
     * BCPlayer constructor.
     * @param Main $plugin
     * @param Player $player
     * @param Clan|null $clan
     * @param int|null $withdrawCooldown
     */
    public function __construct(Main $plugin, Player $player, ?Clan $clan = null, ?int $withdrawCooldown = null)
    {
        $this->plugin = $plugin;
        $this->player = $player;
        $this->clan = $clan ?? $plugin->getPlayersFile()->get(strtolower($player->getName())) !== false ? $this->plugin->getClan($plugin->getPlayersFile()->get(strtolower($player->getName()))) : null;
        $this->nextPossibleWithdrawTimestamp = $withdrawCooldown ?? $plugin->getWithdrawCooldownsFile()->get(strtolower($player->getName()), 0);
    }

    /**
     * Returns the player instance of this player
     * @return Player
     */
    public function getPlayer(): Player
    {
        return $this->player;
    }

    /**
     * Returns whether the player is in a clan
     * @return bool
     */
    public function isInClan(): bool
    {
        return $this->clan !== null;
    }

    /**
     * Returns the players clan
     * @return Clan|null
     */
    public function getClan(): ?Clan
    {
        return $this->clan;
    }

    /**
     * Updates the players clan
     * @param Clan|null $clan
     */
    public function setClan(?Clan $clan)
    {
        $this->clan = $clan;
    }

    /**
     * Returns whether this player is currently in clan chat mode
     * @return bool
     */
    public function isChatting(): bool
    {
        return $this->chatting;
    }

    /**
     * Defines whether the given player is currently in chat mode
     * @param bool $value
     */
    public function setChatting(bool $value = true)
    {
        $this->chatting = $value;
    }

    /**
     * Returns whether the player is the leader of their clan or false if the player is not in a clan
     * @return bool
     */
    public function isLeader(): bool
    {
        return $this->isInClan() ? $this->getClan()->getLeader() === strtolower($this->getPlayer()->getName()) : false;
    }

    /**
     * Returns whether the player is allowed to invite people into their clan
     * @return bool
     */
    public function canInvite(): bool
    {
        return Clan::rankToNumber($this->getClan()->getRank($this)) >= Clan::rankToNumber($this->plugin->getConfig()->get('minimum_invitation_rank', 'member'));
    }

    /**
     * Adds a withdraw cooldown of the given seconds
     * @param int $seconds
     */
    public function addWithdrawCooldown(int $seconds)
    {
        $this->nextPossibleWithdrawTimestamp = time() + $seconds;
    }

    /**
     * @api
     * Returns the number of seconds until the player can withdraw from the clan bank again
     * @return int
     */
    public function getWithdrawCooldown(): int
    {
        return $this->nextPossibleWithdrawTimestamp - time();
    }

    /**
     * @api
     * Returns the whether the player cannot withdraw from the clan bank
     * @return bool
     */
    public function hasWithdrawCooldown(): bool
    {
        return $this->getWithdrawCooldown() > 0;
    }

    /**
     * @internal
     * Returns how much money is on the player's economy api account
     * @return float
     */
    public function getMoney(): float
    {
        if (!is_null(Server::getInstance()->getPluginManager()->getPlugin('EconomyAPI'))) {
            return Server::getInstance()->getPluginManager()->getPlugin('EconomyAPI')->myMoney($this->getPlayer());
        }
        return 0;
    }

    /**
     * @internal
     * Adds money to the given player's economy api account
     * @param float $amount
     */
    public function addMoney(float $amount)
    {
        if (!is_null(Server::getInstance()->getPluginManager()->getPlugin('EconomyAPI'))) {
            Server::getInstance()->getPluginManager()->getPlugin('EconomyAPI')->addMoney($this->getPlayer(), $amount);
        }
    }

    /**
     * @internal
     * Removes money from the given player's economy api account
     * @param float $amount
     */
    public function removeMoney(float $amount)
    {
        if (!is_null(Server::getInstance()->getPluginManager()->getPlugin('EconomyAPI'))) {
            Server::getInstance()->getPluginManager()->getPlugin('EconomyAPI')->reduceMoney($this->getPlayer(), $amount);
        }
    }

    /**
     * Makes this player join the given clan
     * @param Clan $clan
     * @return bool
     */
    public function joinClan(Clan $clan): bool
    {
        $event = new ClanJoinEvent($this->getPlayer(), $clan);
        $event->call();
        if ($event->isCancelled()) return false;
        foreach ($clan->getMembers() as $member) {
            if (!is_null($m = Server::getInstance()->getPlayerExact(Main::getInstance()->getPlayerName($member)))) {
                $m->sendMessage(Main::getInstance()->getMessage('clan.join.members', ['{player}' => $this->getPlayer()->getName()]));
            }
        }
        $clan->addMember($this);
        $this->setClan($clan);
        return true;
    }

    /**
     * Makes the player send a message to the clan chat
     * @param string $message
     * @return bool
     */
    public function chat(string $message): bool
    {
        if (!$this->isInClan()) return false;
        $event = new PlayerClanChatEvent($this->getPlayer(), $message);
        $event->call();
        if ($event->isCancelled()) return false;
        foreach ($this->getClan()->getMembersWithRealName() as $member) {
            if (($m = $this->plugin->getServer()->getPlayerExact($member)) instanceof Player) {
                $m->sendMessage($this->plugin->getMessage('clan.chat.members', ['{player}' => Clan::getRankColor($this->getClan()->getRank($this)) . $this->getPlayer()->getName(), '{message}' => $message]));
            }
        }
        return true;
    }

    /**
     * Saves the player to the file system
     */
    public function save()
    {
        $file = $this->plugin->getPlayersFile();
        $file->set(strtolower($this->getPlayer()->getName()), $this->getClan() === null ? null : $this->getClan()->getName());
        $file->save();

        $file = $this->plugin->getWithdrawCooldownsFile();
        $file->set(strtolower($this->getPlayer()->getName()), $this->nextPossibleWithdrawTimestamp);
        $file->save();
    }

}