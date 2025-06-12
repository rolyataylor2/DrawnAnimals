CREATE TABLE IF NOT EXISTS system_drawnimals_learnset (
        id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
        species TINYBLOB,
        form TINYINT UNSIGNED DEFAULT 0,
        level SMALLINT,
        movename TINYBLOB)