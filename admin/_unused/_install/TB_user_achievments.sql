CREATE TABLE IF NOT EXISTS user_achievments (
    uid BIGINT UNSIGNED,
    type TINYINT,
    title TINYBLOB,
    description BLOB,
    PRIMARY KEY (uid,type)
)
