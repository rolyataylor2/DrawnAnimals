--
-- MySQL 5.1.71
-- Wed, 09 Apr 2014 18:26:33 +0000
--

CREATE TABLE `actions` (
   `id` double unsigned not null auto_increment,
   `bid` double unsigned,
   `uid` bigint(20) unsigned,
   `action` tinyint(3) unsigned,
   `args` tinyblob,
   PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=49;


CREATE TABLE `drawnimals` (
   `battleid` double unsigned,
   `uid` tinyblob,
   `id` bigint(20) unsigned not null auto_increment,
   `name` tinyblob not null,
   `species` tinyblob not null,
   `paint` tinyblob not null,
   `form` tinyint(3) unsigned not null default '0',
   `move_0` tinyblob not null,
   `move_1` tinyblob not null,
   `move_2` tinyblob not null,
   `move_3` tinyblob not null,
   `move_pp_0` tinyint(3) unsigned not null default '0',
   `move_pp_1` tinyint(3) unsigned not null default '0',
   `move_pp_2` tinyint(3) unsigned not null default '0',
   `move_pp_3` tinyint(3) unsigned not null default '0',
   `ailments` blob not null,
   `ailments_default` blob not null,
   `st_exp` smallint(5) unsigned not null default '0',
   `st_level` smallint(5) unsigned not null default '0',
   `st_hp` smallint(5) unsigned not null default '0',
   `st_hunger` smallint(5) unsigned not null default '32000',
   `st_energy` smallint(5) unsigned not null default '32000',
   `st_friendship` smallint(5) unsigned not null,
   `st_mood_datetime` int(10) unsigned not null,
   `st_helditem` int(10) unsigned not null default '0',
   `st_partypos` tinyint(4) not null default '1',
   `st_battlepos` tinyint(3) unsigned default '0',
   `ev_hp` smallint(5) unsigned not null default '0',
   `ev_atk` smallint(5) unsigned not null default '0',
   `ev_def` smallint(5) unsigned not null default '0',
   `ev_spatk` smallint(5) unsigned not null default '0',
   `ev_spdef` smallint(5) unsigned not null default '0',
   `ev_speed` smallint(5) unsigned not null default '0',
   `iv_hp` tinyint(3) unsigned not null default '0',
   `iv_atk` tinyint(3) unsigned not null default '0',
   `iv_def` tinyint(3) unsigned not null default '0',
   `iv_spatk` tinyint(3) unsigned not null default '0',
   `iv_spdef` tinyint(3) unsigned not null default '0',
   `iv_speed` tinyint(3) unsigned not null default '0',
   `md_hp` tinyint(4) not null default '0',
   `md_atk` tinyint(4) not null default '0',
   `md_def` tinyint(4) not null default '0',
   `md_spatk` tinyint(4) not null default '0',
   `md_spdef` tinyint(4) not null default '0',
   `md_speed` tinyint(4) not null default '0',
   `md_acc` tinyint(4) not null default '0',
   `md_evv` tinyint(4) not null default '0',
   `info_gender` tinyint(3) unsigned not null,
   PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=22;


CREATE TABLE `info` (
   `id` double unsigned not null auto_increment,
   `date` bigint(20) unsigned,
   `type` tinyint(3) unsigned default '0',
   `environment` tinyint(3) unsigned default '0',
   `active_round` int(10) unsigned default '1',
   `player_total` tinyint(3) unsigned default '0',
   `player_ai_total` tinyint(3) unsigned default '0',
   `player0` tinyblob not null,
   `player1` tinyblob not null,
   `player2` tinyblob not null,
   `player3` tinyblob not null,
   PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=29;


CREATE TABLE `queue` (
   `id` double unsigned not null auto_increment,
   `bid` double unsigned,
   `round` mediumint(8) unsigned,
   `data` mediumblob,
   PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=70;