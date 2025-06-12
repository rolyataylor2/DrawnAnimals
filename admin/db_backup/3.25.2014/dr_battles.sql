--
-- MySQL 5.1.71
-- Tue, 25 Mar 2014 20:45:52 +0000
--

CREATE TABLE `actions` (
   `bid` double unsigned,
   `uid` bigint(20) unsigned,
   `data` tinyblob
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- [Table `actions` is empty]

CREATE TABLE `comments` (
   `bid` double unsigned,
   `uid` bigint(20) unsigned,
   `timing` bigint(20) unsigned default '0',
   `content` tinyblob
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- [Table `comments` is empty]

CREATE TABLE `drawnimals` (
   `trainername` tinyblob not null,
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
   `st_partypos` tinyint(4) not null default '0',
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
   `info_dislikes` blob not null,
   `info_likes` blob not null,
   PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

-- [Table `drawnimals` is empty]

CREATE TABLE `info` (
   `id` double unsigned not null auto_increment,
   `type` tinyint(3) unsigned default '0',
   `environment` tinyint(3) unsigned default '0',
   `active_round` int(10) unsigned,
   `player_total` tinyint(3) unsigned default '0',
   `player_ai_total` tinyint(3) unsigned default '0',
   `player0` tinyblob not null,
   `player1` tinyblob not null,
   `player2` tinyblob not null,
   `player3` tinyblob not null,
   PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

-- [Table `info` is empty]

CREATE TABLE `log` (
   `bid` double unsigned not null auto_increment,
   `date_started` bigint(20) unsigned default '0',
   `date_ended` bigint(20) unsigned default '0',
   `species` blob,
   `damage` int(10) unsigned default '0',
   `winner` bigint(20) unsigned,
   PRIMARY KEY (`bid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

-- [Table `log` is empty]

CREATE TABLE `queue` (
   `bid` double unsigned,
   `id` int(10) unsigned,
   `data` mediumblob
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- [Table `queue` is empty]

CREATE TABLE `trainer_catalog` (
   `id` bigint(20) unsigned not null auto_increment,
   `name` tinyblob,
   PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

-- [Table `trainer_catalog` is empty]