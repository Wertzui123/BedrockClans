<?php

declare(strict_types=1);

namespace Wertzui123\BedrockClans\listener;

use pocketmine\event\Listener;
use Wertzui123\BedrockClans\Clan;
use Wertzui123\BedrockClans\events\clan\ClanColorChangeEvent;
use Wertzui123\BedrockClans\events\clan\ClanCreateEvent;
use Wertzui123\BedrockClans\events\clan\ClanDeleteEvent;
use Wertzui123\BedrockClans\events\player\OfflinePlayerClanLeaveEvent;
use Wertzui123\BedrockClans\events\player\PlayerClanJoinEvent;
use Wertzui123\BedrockClans\events\player\PlayerClanLeaveEvent;
use Wertzui123\BedrockClans\events\player\PlayerRankChangeEvent;
use Wertzui123\BedrockClans\Main;
use Ifera\ScoreHud\event\TagsResolveEvent;
use Ifera\ScoreHud\event\PlayerTagUpdateEvent;
use Ifera\ScoreHud\event\PlayerTagsUpdateEvent;
use Ifera\ScoreHud\scoreboard\ScoreTag;

class ScoreHudListener implements Listener
{

    private $plugin;

    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
    }

    public function onTagsResolve(TagsResolveEvent $event)
    {
        $player = $event->getPlayer();
        $tag = $event->getTag();
        switch ($tag->getName()) {
            case 'bedrockclans.clan.name':
                $tag->setValue($this->plugin->getPlayer($player)->getClan()?->getDisplayName() ?? '/');
                break;
            case 'bedrockclans.player.rank':
                if ($this->plugin->getPlayer($player)->getClan() !== null) {
                    $tag->setValue(Clan::getRankName($this->plugin->getPlayer($player)->getClan()->getRank($player), true));
                } else {
                    $tag->setValue('/');
                }
                break;
            case 'bedrockclans.clan.members':
                if ($this->plugin->getPlayer($player)->getClan() !== null) {
                    $tag->setValue((string)count($this->plugin->getPlayer($player)->getClan()->getMembers()));
                } else {
                    $tag->setValue('/');
                }
                break;
        }
    }

    public function onPlayerJoinClan(PlayerClanJoinEvent $event)
    {
        $ev = new PlayerTagsUpdateEvent(
            $event->getPlayer(),
            [
                new ScoreTag('bedrockclans.clan.name', $event->getClan()->getDisplayName()),
                new ScoreTag('bedrockclans.player.rank', Clan::getRankName('member', true)),
                new ScoreTag('bedrockclans.clan.members', (string)(count($event->getClan()->getMembers()) + 1))
            ]
        );
        $ev->call();

        foreach ($event->getClan()->getMembersWithRealName() as $member) {
            if ($this->plugin->getServer()->getPlayerExact($member) !== null) {
                $ev->call();
                $ev = new PlayerTagUpdateEvent(
                    $this->plugin->getServer()->getPlayerExact($member),
                    new ScoreTag('bedrockclans.clan.members', (string)(count($event->getClan()->getMembers()) + 1))
                );
                $ev->call();
            }
        }
    }

    public function onPlayerLeaveClan(PlayerClanLeaveEvent $event)
    {
        $ev = new PlayerTagsUpdateEvent(
            $event->getPlayer(),
            [
                new ScoreTag('bedrockclans.clan.name', '/'),
                new ScoreTag('bedrockclans.player.rank', '/'),
                new ScoreTag('bedrockclans.clan.members', '/')
            ]
        );
        $ev->call();

        foreach ($event->getClan()->getMembersWithRealName() as $member) {
            if ($this->plugin->getServer()->getPlayerExact($member) !== null && $this->plugin->getServer()->getPlayerExact($member) !== $event->getPlayer()) {
                $ev = new PlayerTagUpdateEvent(
                    $this->plugin->getServer()->getPlayerExact($member),
                    new ScoreTag('bedrockclans.clan.members', (string)(count($event->getClan()->getMembers()) - 1))
                );
                $ev->call();
            }
        }
    }

    public function onOfflinePlayerLeaveClan(OfflinePlayerClanLeaveEvent $event)
    {
        foreach ($event->getClan()->getMembersWithRealName() as $member) {
            if ($this->plugin->getServer()->getPlayerExact($member) !== null) {
                $ev = new PlayerTagUpdateEvent(
                    $this->plugin->getServer()->getPlayerExact($member),
                    new ScoreTag('bedrockclans.clan.members', (string)(count($event->getClan()->getMembers()) - 1))
                );
                $ev->call();
            }
        }
    }

    public function onPlayerCreateClan(ClanCreateEvent $event)
    {
        $player = $event->getPlayer();
        $ev = new PlayerTagsUpdateEvent(
            $event->getPlayer(),
            [
                new ScoreTag('bedrockclans.clan.name', $event->getClan()->getDisplayName()),
                new ScoreTag('bedrockclans.player.rank', Clan::getRankName($event->getClan()->getRank($player), true)),
                new ScoreTag('bedrockclans.clan.members', (string)count($event->getClan()->getMembers()))
            ]
        );
        $ev->call();
    }

    public function onPlayerDeleteClan(ClanDeleteEvent $event)
    {
        foreach ($event->getClan()->getMembersWithRealName() as $member) {
            if ($this->plugin->getServer()->getPlayerExact($member) !== null) {
                $ev = new PlayerTagsUpdateEvent(
                    $this->plugin->getServer()->getPlayerExact($member),
                    [
                        new ScoreTag('bedrockclans.clan.name', '/'),
                        new ScoreTag('bedrockclans.player.rank', '/'),
                        new ScoreTag('bedrockclans.clan.members', '/')
                    ]
                );
                $ev->call();
            }
        }
    }

    /**
     * @param PlayerRankChangeEvent $event
     * @priority MONITOR
     */
    public function onPlayerRankChange(PlayerRankChangeEvent $event)
    {
        $ev = new PlayerTagUpdateEvent(
            $event->getPlayer(),
            new ScoreTag('bedrockclans.player.rank', Clan::getRankName($event->getNewRank(), true))
        );
        $ev->call();
    }

    public function onClanColorChange(ClanColorChangeEvent $event)
    {
        foreach ($event->getClan()->getMembersWithRealName() as $member) {
            if ($this->plugin->getServer()->getPlayerExact($member) !== null) {
                $ev = new PlayerTagUpdateEvent(
                    $this->plugin->getServer()->getPlayerExact($member),
                    new ScoreTag('bedrockclans.clan.name', $event->getClan()->getDisplayName($event->getNewColor()))
                );
                $ev->call();
            }
        }
    }

}