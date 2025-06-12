CREATE TABLE IF NOT EXISTS system_items (
	id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
	name TINYBLOB,
	catagory TINYBLOB,
	description BLOB,
	script_select BLOB,
	script_execute BLOB,
	script_battle_select BLOB,
	script_battle_execute BLOB,
        script_requirement BLOB,
        datetime_release INT)
