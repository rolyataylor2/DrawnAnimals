CREATE TABLE IF NOT EXISTS system_aitrainers (
	id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
	name TINYBLOB,
	type TINYBLOB,
	team BLOB, -- TEAM LIKE THIS 'pikachu|pikachu|pikachu';
	levels BLOB, -- LEVELS LIKE THIS '10|10|10';
	difficulty TINYINT UNSIGNED,
	dialog_intro BLOB,
	dialog_lost BLOB,
	dialog_won BLOB,
	dialog_taunt BLOB)
