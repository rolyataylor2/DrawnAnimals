CREATE TABLE IF NOT EXISTS content_submissions (
        uid BIGINT UNSIGNED DEFAULT 0,
        type TINYINT UNSIGNED DEFAULT 0,
        datastring BLOB NOT NULL,
        submitdate INT UNSIGNED,
        auid BIGINT UNSIGNED DEFAULT 0,
        approved TINYINT UNSIGNED DEFAULT 0,
        approveddate INT UNSIGNED
)
