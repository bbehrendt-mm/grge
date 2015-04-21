# Ban
INSERT INTO grg_user_flags (user,relation,data) SELECT uid AS user, 'DENY' as relation, 'WHITELIST' as data FROM grg_users WHERE ban = 1;

# Drop old stuff
ALTER TABLE grg_users DROP ban;