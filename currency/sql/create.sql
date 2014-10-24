CREATE DATABASE IF NOT EXISTS atwd_currency;

CREATE TABLE IF NOT EXISTS atwd_currency.currencies (
code char(3) NOT NULL,
curr varchar(24) NOT NULL,
loc varchar(255) NOT NULL,
rate decimal(7,2) NOT NULL,
PRIMARY KEY(code) );

INSERT INTO atwd_currency.currencies (code, curr, loc, rate) VALUES
('USD', 'United States Dollar', 'American Samoa, British Indian Ocean Territory, Ecuador, El Salvador, Guam, Marshall Islands, Micronesia, Northern Mariana Islands, Palau, Panama, Puerto Rico, Timor-Leste, Turks and Caicos Islands, United States, Virgin Islands', '1.00'),
('GBP', 'Pound sterling', 'Crown Dependencies (the Isle of Man and the Channel Islands) certain British Overseas Territories (South Georgia and the South Sandwich Islands, British Antarctic Territory and British Indian Ocean Territory), United Kingdom', '0.62'),
('EUR', 'Euro', 'Andorra, Austria, Belgium, Cyprus, Finland, France, Germany, Greece, Hungary, Ireland, Italy, Kosovo, Luxembourg, Malta, Monaco, Montenegro, Netherlands, Portugal, San Marino, Slovakia, Slovenia, Spain, Vatican City', '0.73'),
('JPY', 'Japanese yen', 'Japan', '82.42');