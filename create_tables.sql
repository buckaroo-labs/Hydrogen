
create table user (
`id` INT NOT NULL AUTO_INCREMENT , 
`username` VARCHAR(30) NOT NULL  ,
`email` VARCHAR(30) NOT NULL  ,
`password` VARCHAR(255) NOT NULL COMMENT 'php password_hash(password,PASSWORD_BCRYPT)' ,
`first_name` VARCHAR(30) NOT NULL ,
`last_name` VARCHAR(30) NOT NULL ,
`created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'date created', 
`last_updated` DATE NULL COMMENT 'date of last change to this record', 
PRIMARY KEY (`id`), 
UNIQUE KEY(`username`)
);

create table saved_sql (
`id` INT NOT NULL AUTO_INCREMENT , 
`session_id` VARCHAR(120) NOT NULL  ,
`sqltext` VARCHAR(2000) NOT NULL  ,
`created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'date created',
PRIMARY KEY (`id`)
);

--test/foo
insert into user(username,email,first_name,last_name,password) VALUES ('Test','y@x.com','John','Doe','$2y$10$JA6W8MpTPLlwt4nXg7yJKeZYF15L3qmFDXJ42hKBc9fHWmBKKzxv6');




