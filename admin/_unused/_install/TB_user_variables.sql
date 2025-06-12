
CREATE TABLE IF NOT EXISTS user_variables (
	uid BIGINT UNSIGNED,
	name TINYBLOB,
	value BLOB,
	datetime INT UNSIGNED,
        INDEX (uid)
)

