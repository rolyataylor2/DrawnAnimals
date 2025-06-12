CREATE TABLE IF NOT EXISTS social_likes (
        uid BIGINT UNSIGNED PRIMARY KEY,
        onwhat TINYBLOB, -- [pet:1] [battle:10039382938404] [user:1] [comment:10393920394]
        datetime INT UNSIGNED
)
