SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS ___PREFIX___achievements (
  uid int(11) NOT NULL,
  gameid int(11) NOT NULL,
  season int(11) NOT NULL DEFAULT '-1',
  aid int(11) NOT NULL,
  `value` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS ___PREFIX___battle (
  bid int(11) NOT NULL,
  gameid int(11) NOT NULL,
  season int(11) NOT NULL,
  fixed tinyint(1) NOT NULL,
  data mediumblob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS ___PREFIX___contests (
  contest_id varchar(16) NOT NULL,
  user_id int(11) NOT NULL,
  game_id int(11) NOT NULL,
  points int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS ___PREFIX___games (
  gameid int(11) NOT NULL COMMENT 'Local game ID',
  `timestamp` int(11) NOT NULL COMMENT 'Last access timestamp',
  `lock` int(11) NOT NULL DEFAULT '0',
  gamedata longblob NOT NULL COMMENT 'Game dataset'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS ___PREFIX___games_cloud (
  gameid int(11) DEFAULT NULL,
  uin int(11) NOT NULL,
  `data` mediumblob
) ENGINE=InnoDB DEFAULT CHARSET=latin1 PACK_KEYS=0;

CREATE TABLE IF NOT EXISTS ___PREFIX___karma (
  `subject` int(11) NOT NULL,
  rater int(11) NOT NULL,
  `value` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS ___PREFIX___language (
  `id` int(11) NOT NULL,
  `hash` binary(16) NOT NULL,
  `de` text NOT NULL,
  `en` text,
  `es` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS ___PREFIX___mentor (
  uid int(11) NOT NULL,
  mentor int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS ___PREFIX___mp_lockouts (
  uid int(11) NOT NULL,
  `timestamp` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS ___PREFIX___multiplayer_lobby (
  gameid int(11) NOT NULL,
  lang varchar(2) NOT NULL DEFAULT 'de',
  slots int(11) NOT NULL,
  `name` varchar(96) NOT NULL,
  `password` varchar(64) DEFAULT NULL,
  `timestamp` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS ___PREFIX___profiles_xref (
  provider varchar(32) NOT NULL,
  rid int(11) NOT NULL,
  zvid int(11) NOT NULL,
  var1 varchar(128) DEFAULT NULL,
  var2 varchar(128) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS ___PREFIX___ranking (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS ___PREFIX___qr (
  uid int(11) NOT NULL,
  pin varchar(4) NOT NULL,
  `timestamp` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS ___PREFIX___ranking_mp (
  season int(11) NOT NULL,
  board int(11) NOT NULL,
  gameid int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  points int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS ___PREFIX___users (
  uid int(11) NOT NULL COMMENT 'Local player ID',
  `name` varchar(24) CHARACTER SET utf8 NOT NULL COMMENT 'Username',
  avatar varchar(511) DEFAULT NULL,
  univsp int(11) NOT NULL DEFAULT '0',
  `session` varchar(32) COLLATE utf8_bin NOT NULL COMMENT 'Last active session ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS ___PREFIX___user_flags (
  autoid int(11) NOT NULL,
  `user` int(11) NOT NULL,
  relation enum('LOGIN','ALLOW','DENY','DISABLE') NOT NULL,
  `data` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS ___PREFIX___xref_game_player (
  gameid int(11) NOT NULL,
  uid int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE ___PREFIX___achievements
  ADD PRIMARY KEY (uid,gameid,season,aid);

ALTER TABLE ___PREFIX___battle
  ADD KEY bid (bid);

ALTER TABLE ___PREFIX___contests
  ADD PRIMARY KEY (contest_id,user_id) USING BTREE;

ALTER TABLE ___PREFIX___games
  ADD PRIMARY KEY (gameid) USING BTREE;

ALTER TABLE ___PREFIX___games_cloud
  ADD PRIMARY KEY (uin);

ALTER TABLE ___PREFIX___karma
  ADD PRIMARY KEY (`subject`,rater);

ALTER TABLE ___PREFIX___language
ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `hash` (`hash`);

ALTER TABLE ___PREFIX___language
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE ___PREFIX___mentor
  ADD PRIMARY KEY (uid);

ALTER TABLE ___PREFIX___multiplayer_lobby
  ADD PRIMARY KEY (gameid);

ALTER TABLE ___PREFIX___profiles_xref
  ADD UNIQUE KEY (`provider`,`rid`);

ALTER TABLE ___PREFIX___qr
ADD PRIMARY KEY (uid), ADD UNIQUE KEY pin (pin);

ALTER TABLE ___PREFIX___ranking
  ADD PRIMARY KEY (season,uid,gameid), ADD KEY uid (uid) USING BTREE;

ALTER TABLE ___PREFIX___ranking_mp
  ADD PRIMARY KEY (season,gameid);

ALTER TABLE ___PREFIX___users
  ADD PRIMARY KEY (uid) USING BTREE;

ALTER TABLE ___PREFIX___user_flags
  ADD PRIMARY KEY (autoid);

ALTER TABLE ___PREFIX___xref_game_player
  ADD PRIMARY KEY (uid);

ALTER TABLE ___PREFIX___battle
  MODIFY bid int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE ___PREFIX___games
  MODIFY gameid int(11) NOT NULL AUTO_INCREMENT COMMENT 'Local game ID';

ALTER TABLE ___PREFIX___games_cloud
  MODIFY uin int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE ___PREFIX___users
  MODIFY uid int(11) NOT NULL AUTO_INCREMENT COMMENT 'Local player ID';

ALTER TABLE ___PREFIX___user_flags
  MODIFY autoid int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE ___PREFIX___ranking
  ADD CONSTRAINT ___PREFIX___ranking_ibfk_1 FOREIGN KEY (uid) REFERENCES ___PREFIX___users (uid) ON DELETE CASCADE ON UPDATE CASCADE;