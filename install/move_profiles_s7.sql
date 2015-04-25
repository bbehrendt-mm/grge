# Prepare
CREATE TABLE IF NOT EXISTS ___PREFIX___login_supplicant (
  origin enum('de','en','fr','es','cs') NOT NULL,
  `key` varchar(48) NOT NULL,
  `name` varchar(24) NOT NULL,
  mtid int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

# Die Verdammten
INSERT INTO ___PREFIX___profiles_xref (zvid,rid,provider,var1)
  SELECT ___PREFIX___users.uid as zvid, ___PREFIX___users.mtid as rid,'Model_Auth_Hordesde' as provider,___PREFIX___login_supplicant.key as var1
  FROM ___PREFIX___users LEFT JOIN ___PREFIX___login_supplicant ON ___PREFIX___users.mtid = ___PREFIX___login_supplicant.mtid AND ___PREFIX___users.origin = ___PREFIX___login_supplicant.origin
  WHERE ___PREFIX___users.origin='de';

# Die2Nite
INSERT INTO ___PREFIX___profiles_xref (zvid,rid,provider,var1)
  SELECT ___PREFIX___users.uid as zvid, ___PREFIX___users.mtid as rid,'Model_Auth_Hordesen' as provider,___PREFIX___login_supplicant.key as var1
  FROM ___PREFIX___users LEFT JOIN ___PREFIX___login_supplicant ON ___PREFIX___users.mtid = ___PREFIX___login_supplicant.mtid AND ___PREFIX___users.origin = ___PREFIX___login_supplicant.origin
  WHERE ___PREFIX___users.origin='en';

# Drop old stuff
DROP TABLE ___PREFIX___login_supplicant;
ALTER TABLE ___PREFIX___users DROP mtid, DROP origin;