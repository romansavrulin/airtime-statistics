GRANT ALL PRIVILEGES ON *.* TO monty@localhost
    ->     IDENTIFIED BY 'some_pass' WITH GRANT OPTION;
    
GRANT ALL PRIVILEGES ON rvkstat.* TO rvk@localhost
    ->     IDENTIFIED BY 'some_pass' WITH GRANT OPTION;

CREATE TABLE `rvkstat`.`minute-stat` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `channel` INTEGER NOT NULL,
  `users` INTEGER NOT NULL,
  PRIMARY KEY (`id`)
)
ENGINE = InnoDB;

CREATE TABLE `rvkstat`.`channels` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `color` VARCHAR(45) NOT NULL,
  `data_source` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`)
)

ALTER TABLE `rvkstat`.`minute-stat` ADD CONSTRAINT `FK_minute-stat_channel` FOREIGN KEY `FK_minute-stat_channel` (`channel`)
    REFERENCES `channels` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE;
ENGINE = InnoDB;