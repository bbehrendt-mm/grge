# Ban
INSERT INTO ___PREFIX___user_flags (user,relation,data) SELECT uid AS user, 'DENY' as relation, 'WHITELIST' as data FROM ___PREFIX___users WHERE ban = 1;

# Drop old stuff
ALTER TABLE ___PREFIX___users DROP ban;