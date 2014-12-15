-- -----------------------------------------------------
-- Creation of Scheme
-- -----------------------------------------------------

CREATE TABLE IF NOT EXISTS champion (
  id INT NOT NULL AUTO_INCREMENT,
  name VARCHAR(45) NOT NULL ,
  enwikilink VARCHAR(150) NULL ,
  PRIMARY KEY (id)
)
  DEFAULT CHARACTER SET = utf8
  COLLATE = utf8_bin;

CREATE UNIQUE INDEX enwikilink_UNIQUE ON champion (enwikilink ASC) ;

CREATE TABLE IF NOT EXISTS reign (
  id INT NOT NULL AUTO_INCREMENT,
  champion_id INT NOT NULL ,
  # Can not use YEAR type below as range is only 1901 to 2155.
  # See http://dev.mysql.com/doc/refman/5.0/en/year.html
  start_year INT(4) NOT NULL ,
  end_year INT(4) NOT NULL ,
  type VARCHAR(10) NULL ,
  PRIMARY KEY (id),
  CONSTRAINT fk_reign_champion1
  FOREIGN KEY (champion_id )
  REFERENCES champion (id )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
)
  DEFAULT CHARACTER SET = utf8
  COLLATE = utf8_bin;

CREATE INDEX fk_reign_champion1_idx ON reign (champion_id ASC);

CREATE TABLE IF NOT EXISTS location (
  id INT NOT NULL AUTO_INCREMENT,
  country VARCHAR(45) NOT NULL ,
  country_link VARCHAR(150) NULL ,
  historical VARCHAR(45) NULL ,
  historical_link VARCHAR(150) NULL ,
  flag_img VARCHAR(250) NULL ,
  PRIMARY KEY (id)
)
  DEFAULT CHARACTER SET = utf8
  COLLATE = utf8_bin;

CREATE TABLE IF NOT EXISTS champion_location (
  champion_id INT NOT NULL ,
  location_id INT NOT NULL ,
  PRIMARY KEY (location_id, champion_id) ,
  CONSTRAINT fk_champion_location_champion1
  FOREIGN KEY (champion_id )
  REFERENCES champion (id )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT fk_champion_location_location1
  FOREIGN KEY (location_id )
  REFERENCES location (id )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
)
  DEFAULT CHARACTER SET = utf8
  COLLATE = utf8_bin;

CREATE INDEX fk_champion_location_champion1_idx ON champion_location (champion_id ASC) ;

CREATE INDEX fk_champion_location_location1_idx ON champion_location (location_id ASC) ;