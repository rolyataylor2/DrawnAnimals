CREATE TABLE IF NOT EXISTS user_accounts (
    uid BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    username TINYBLOB,
    password BLOB,
    email TINYBLOB,
    country TINYBLOB,
    timezone TINYBLOB,
    datecreated INT UNSIGNED DEFAULT 0,
    datelastseen INT UNSIGNED DEFAULT 0,
    datebirth INT UNSIGNED DEFAULT 0,
    ipaddress TINYBLOB,
    sessionid BLOB,
    status BLOB,
    type BLOB);
