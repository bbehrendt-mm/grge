# Prepare
CREATE TABLE IF NOT EXISTS grg_login_supplicant (
  origin enum('de','en','fr','es','cs') NOT NULL,
  `key` varchar(48) NOT NULL,
  `name` varchar(24) NOT NULL,
  mtid int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

# Die Verdammten
INSERT INTO grg_profiles_xref (zvid,rid,provider,var1)
  SELECT grg_users.uid as zvid, grg_users.mtid as rid,'Model_Auth_Hordesde' as provider,grg_login_supplicant.key as var1
  FROM grg_users LEFT JOIN grg_login_supplicant ON grg_users.mtid = grg_login_supplicant.mtid AND grg_users.origin = grg_login_supplicant.origin
  WHERE grg_users.origin='de';

# Die2Nite
INSERT INTO grg_profiles_xref (zvid,rid,provider,var1)
  SELECT grg_users.uid as zvid, grg_users.mtid as rid,'Model_Auth_Hordesen' as provider,grg_login_supplicant.key as var1
  FROM grg_users LEFT JOIN grg_login_supplicant ON grg_users.mtid = grg_login_supplicant.mtid AND grg_users.origin = grg_login_supplicant.origin
  WHERE grg_users.origin='en';

# Drop old stuff
DROP TABLE grg_login_supplicant;
ALTER TABLE grg_users DROP mtid, DROP origin;