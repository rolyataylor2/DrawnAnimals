CREATE TABLE IF NOT EXISTS user_species (
        uid BIGINT UNSIGNED,
        species TINYBLOB,
        caught TINYINT UNSIGNED,
        datetime INT UNSIGNED,
        INDEX (uid)
)
