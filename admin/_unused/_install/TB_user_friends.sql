CREATE TABLE IF NOT EXISTS user_friends (
    uid BIGINT UNSIGNED,
    uuid BIGINT UNSIGNED,
    status TINYINT UNSIGNED,
    tag TINYBLOB,
    PRIMARY KEY (uid,uuid)
)
