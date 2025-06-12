CREATE TABLE IF NOT EXISTS system_attacks (
	id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
	name TINYBLOB,
        type TINYINT UNSIGNED,
	power TINYINT UNSIGNED,
	pp TINYINT UNSIGNED,
	speed TINYINT UNSIGNED,
	accuracy TINYINT UNSIGNED,
        target TINYINT UNSIGNED,
        damage_type TINYINT UNSIGNED,
	script_select BLOB,
	script_execute BLOB,
	description BLOB)
