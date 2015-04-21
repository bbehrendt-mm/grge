SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS grg_achievements (
  uid int(11) NOT NULL,
  gameid int(11) NOT NULL,
  season int(11) NOT NULL DEFAULT '-1',
  aid int(11) NOT NULL,
  `value` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AVG_ROW_LENGTH=260;

CREATE TABLE IF NOT EXISTS grg_contests (
  contest_id varchar(16) NOT NULL,
  user_id int(11) NOT NULL,
  game_id int(11) NOT NULL,
  points int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AVG_ROW_LENGTH=256;

CREATE TABLE IF NOT EXISTS grg_games (
  gameid int(11) NOT NULL COMMENT 'Local game ID',
  `timestamp` int(11) NOT NULL COMMENT 'Last access timestamp',
  `lock` int(11) NOT NULL DEFAULT '0',
  gamedata longblob NOT NULL COMMENT 'Game dataset'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AVG_ROW_LENGTH=158105;

CREATE TABLE IF NOT EXISTS grg_games_cloud (
  gameid int(11) DEFAULT NULL,
  uin int(11) NOT NULL,
  `data` mediumblob
) ENGINE=InnoDB DEFAULT CHARSET=latin1 PACK_KEYS=0;

CREATE TABLE IF NOT EXISTS grg_karma (
  `subject` int(11) NOT NULL,
  rater int(11) NOT NULL,
  `value` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS grg_mentor (
  uid int(11) NOT NULL,
  mentor int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS grg_mp_lockouts (
  uid int(11) NOT NULL,
  `timestamp` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS grg_multiplayer_lobby (
  gameid int(11) NOT NULL,
  lang varchar(2) NOT NULL DEFAULT 'de',
  slots int(11) NOT NULL,
  `name` varchar(96) NOT NULL,
  `password` varchar(64) DEFAULT NULL,
  `timestamp` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS grg_profiles_xref (
  provider varchar(32) NOT NULL,
  rid int(11) NOT NULL,
  zvid int(11) NOT NULL,
  var1 varchar(128) DEFAULT NULL,
  var2 varchar(128) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS grg_ranking (
  season int(11) NOT NULL,
  uid int(11) NOT NULL,
  gameid int(11) NOT NULL,
  points int(11) NOT NULL,
  ticks int(11) NOT NULL,
  job int(11) NOT NULL,
  board int(11) NOT NULL,
  flow int(11) NOT NULL DEFAULT '0',
  `start` int(11) NOT NULL,
  `end` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AVG_ROW_LENGTH=176;

CREATE TABLE IF NOT EXISTS grg_ranking_mp (
  season int(11) NOT NULL,
  board int(11) NOT NULL,
  gameid int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  points int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS grg_users (
  uid int(11) NOT NULL COMMENT 'Local player ID',
  `name` varchar(24) CHARACTER SET utf8 NOT NULL COMMENT 'Username',
  avatar varchar(511) DEFAULT NULL,
  univsp int(11) NOT NULL DEFAULT '0',
  `session` varchar(32) COLLATE utf8_bin NOT NULL COMMENT 'Last active session ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AVG_ROW_LENGTH=682;

CREATE TABLE IF NOT EXISTS grg_user_flags (
  autoid int(11) NOT NULL,
  `user` int(11) NOT NULL,
  relation enum('LOGIN','ALLOW','DENY','DISABLE') NOT NULL,
  `data` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS grg_xref_game_player (
  gameid int(11) NOT NULL,
  uid int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


ALTER TABLE grg_achievements
  ADD PRIMARY KEY (uid,gameid,season,aid);

ALTER TABLE grg_contests
  ADD PRIMARY KEY (contest_id,user_id) USING BTREE;

ALTER TABLE grg_games
  ADD PRIMARY KEY (gameid) USING BTREE;

ALTER TABLE grg_games_cloud
  ADD PRIMARY KEY (uin);

ALTER TABLE grg_karma
  ADD PRIMARY KEY (`subject`,rater);

ALTER TABLE grg_mentor
  ADD PRIMARY KEY (uid);

ALTER TABLE grg_multiplayer_lobby
  ADD PRIMARY KEY (gameid);

ALTER TABLE grg_profiles_xref
  ADD PRIMARY KEY (rid,provider);

ALTER TABLE grg_ranking
  ADD PRIMARY KEY (season,uid,gameid), ADD KEY uid (uid) USING BTREE;

ALTER TABLE grg_ranking_mp
  ADD PRIMARY KEY (season,gameid);

ALTER TABLE grg_users
  ADD PRIMARY KEY (uid) USING BTREE;

ALTER TABLE grg_user_flags
  ADD PRIMARY KEY (autoid);

ALTER TABLE grg_xref_game_player
  ADD PRIMARY KEY (uid);


ALTER TABLE grg_games
  MODIFY gameid int(11) NOT NULL AUTO_INCREMENT COMMENT 'Local game ID';
ALTER TABLE grg_games_cloud
  MODIFY uin int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE grg_users
  MODIFY uid int(11) NOT NULL AUTO_INCREMENT COMMENT 'Local player ID';
ALTER TABLE grg_user_flags
  MODIFY autoid int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE grg_ranking
ADD CONSTRAINT grg_ranking_ibfk_1 FOREIGN KEY (uid) REFERENCES grg_users (uid) ON DELETE CASCADE ON UPDATE CASCADE;