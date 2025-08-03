<h1 align="center">BedrockClans</h1>
<p align="center">
An advanced PocketMine-MP clan plugin with many creative features.
<br>The .phar can be found on <a href="https://poggit.pmmp.io/ci/Wertzui123/BedrockClans/BedrockClans/">Poggit</a>.
</p>

# JetBrains PHPStorm
JetBrains supports me in the development of BedrockClans.
<br>I can highly recommend <a href="https://jetbrains.com?from=BedrockClans">their products</a>.

# Features
* Highly customizable
* Multiple different clan ranks
* Advanced economy integration
    * Support for different economy plugins
    * Clan bank
    * Clan creation costs
* RankSystem integration
* ScoreHud integration
* Clan chat
* Clan homes
* Flexible restrictions on clan names
* ...

# Commands
**Main command:** /clan

| Subcommand           | Usage                           | Description                                                        |
|----------------------|---------------------------------|--------------------------------------------------------------------|
| help                 | help                            | Displays a list of available commands                              |
| create               | create <clanname>               | Creates a clan                                                     |
| demote               | demote <playername>             | Demotes a player                                                   |
| deposit              | deposit <amount>                | Deposits money to the clan bank                                    |
| info                 | info [clanname]                 | Gives you information about your/a clan                            |
| invite               | invite <playername>             | Invites a player to your clan                                      |
| accept               | accept <clanname>               | Accepts a clan invitation                                          |
| join                 | join <clanname>                 | Joins a clan (only for staff)                                      |
| leave                | leave                           | Leaves your clan clan                                              |
| leader               | leader <playername>             | Promotes a player to the new clan leader                           |
| chat                 | chat <message>                  | Sends a message to all online members of your clan                 |
| kick                 | kick <playername>               | Kicks the given player from your clan                              |
| delete               | delete                          | Deletes your clan                                                  |
| promote              | promote <playername>            | Promotes a player                                                  |
| home                 | home                            | Teleports you to your clan's home                                  |
| sethome              | sethome                         | Updates your clan's home                                           |
| setminimuminviterank | setminimuminviterank <clanrank> | Specifies the minimum rank required to invite people into the clan |
| withdraw             | withdraw <amount>               | Withdraws money from the clan bank                                 |
| about                | about                           | Shows credits and the version of BedrockClans                      |

# Permissions
| Permission                      | Description                                  | Default |
|---------------------------------|----------------------------------------------|---------|
| bedrockclans.command.clan       | Allows you to use `/clan`                    | true    |
| bedrockclans.command.join       | Allows you to use `/clan join`               | op      |
| bedrockclans.command.create     | Allows you to use `/clan create`             | op      |
| bedrockclans.create.cost.bypass | Allows you to bypass the clan creation costs | false   |

# Switching to BedrockClans
If you want to switch from another clan/factions plugin to BedrockClans, you can use these scripts to convert your old database to a BedrockClans compatible one:
* <a href="https://github.com/Wertzui123/FactionsPro2BedrockClans">FactionsPro</a>

# RankSystem integration
Check out https://github.com/Wertzui123/BedrockClansTags4RankSystem if you want to use BedrockClans with RankSystem.

# PureChat integration
Check out https://github.com/fernanACM/PureChat if you want to use BedrockClans with PureChat.

# ScoreHud integration
BedrockClans has built-in support for <a href="https://github.com/Ifera/ScoreHud">ScoreHud</a>.
<br>Available tags are:

| Tag                       | Description                                     |
|---------------------------|-------------------------------------------------|
| bedrockclans.clan.name    | The display name of the player's clan (colored) |
| bedrockclans.player.rank  | The rank of the player in their clan (colored)  |
| bedrockclans.clan.members | The number of members in the player's clan      |

# Ideas, Questions and Support
You can contact me by <a href="https://discord.gg/azPt6eJ">joining my Discord server</a> or by <a href="https://github.com/Wertzui123/BedrockClans/issues/new">creating an issue</a>/<a href="https://github.com/Wertzui123/BedrockClans/discussions/new">starting a discussion</a> here on GitHub.

# License
BedrockClans is licensed under the GNU General Public License v3.0.
<br>For more information: https://choosealicense.com/licenses/gpl-3.0
<br><code>© 2019 - 2025 Wertzui123</code>

# Credits
BedrockClans was written by Wertzui123.
<br>Thanks to Wertzui12345 for lots of testing and to everyone else who has contributed to BedrockClans.
<br>You can help me too by reporting bugs and making suggestions!