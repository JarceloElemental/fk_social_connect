CREATE TABLE /*TABLE_PREFIX*/social_connect (
    fk_i_user_id INT(10) UNSIGNED NOT NULL,
    social_uid VARCHAR(30) NULL,
    via VARCHAR(30) NULL,

        PRIMARY KEY (fk_i_user_id),
        FOREIGN KEY (fk_i_user_id) REFERENCES /*TABLE_PREFIX*/t_user (pk_i_id)
) ENGINE=InnoDB DEFAULT CHARACTER SET 'UTF8' COLLATE 'UTF8_GENERAL_CI';