CREATE TABLE IF NOT EXISTS social_timeline (
        id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
        uid BIGINT UNSIGNED PRIMARY KEY,
        type TINYBLOB,
        arguments BLOB,
        datetime INT UNSIGNED
)
