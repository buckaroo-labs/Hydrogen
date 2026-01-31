

-- schema creation script for MySQL
-- --------------------------------------------------------

--
-- Table structure for table `m_role_privilege`
--

CREATE TABLE `m_role_privilege` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `privilege_id` int(11) NOT NULL,
  `ins_user` varchar(30) NOT NULL DEFAULT 'system',
  `ins_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `m_user_role`
--

CREATE TABLE `m_user_role` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ins_user` varchar(30) NOT NULL DEFAULT 'system',
  `ins_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `page_usage`
--

CREATE TABLE `page_usage` (
  `id` int(11) NOT NULL,
  `server` varchar(250) DEFAULT NULL,
  `ip` varchar(50) DEFAULT NULL,
  `remote_host` varchar(250) DEFAULT NULL,
  `uri` varchar(999) DEFAULT NULL,
  `username` varchar(99) DEFAULT NULL,
  `server_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `request_method` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `privilege`
--

CREATE TABLE `privilege` (
  `id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `description` varchar(100) NOT NULL,
  `ins_user` varchar(30) NOT NULL DEFAULT 'system',
  `ins_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `description` varchar(100) NOT NULL,
  `ins_user` varchar(30) NOT NULL DEFAULT 'system',
  `ins_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--
--password and names must be allowed as null for self-registration process
CREATE TABLE `user` (
`id` INT NOT NULL AUTO_INCREMENT , 
  `username` varchar(50) NOT NULL,
  `email` varchar(99) NOT NULL,
  `password_hash` varchar(500)  DEFAULT NULL COMMENT 'php password_hash(password,PASSWORD_BCRYPT)' ,
  `first_name` VARCHAR(30) DEFAULT NULL ,
`last_name` VARCHAR(30) DEFAULT NULL ,
  `reset_code` varchar(25) DEFAULT NULL,
  `session_id` varchar(50) DEFAULT NULL,
  `access_token` varchar(500) DEFAULT NULL,
  `last_ip` varchar(30) DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `ins_user` varchar(30) NOT NULL DEFAULT 'system',
  `ins_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
    , PRIMARY KEY (`id`),
 UNIQUE KEY `username` (`username`),
UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;




--
-- Indexes for dumped tables
--

--
-- Indexes for table `m_role_privilege`
--
ALTER TABLE `m_role_privilege`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_priv_unq` (`privilege_id`,`role_id`),
  ADD KEY `role_privilige_role_fk` (`role_id`);

--
-- Indexes for table `m_user_role`
--
ALTER TABLE `m_user_role`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_role_unq` (`user_id`,`role_id`),
  ADD KEY `user_role_role_fk` (`role_id`);

--
-- Indexes for table `page_usage`
--
ALTER TABLE `page_usage`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `privilege`
--
ALTER TABLE `privilege`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id`);



--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `m_role_privilege`
--
ALTER TABLE `m_role_privilege`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `m_user_role`
--
ALTER TABLE `m_user_role`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `page_usage`
--
ALTER TABLE `page_usage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `privilege`
--
ALTER TABLE `privilege`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `role`
--
ALTER TABLE `role`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


--
-- Constraints for dumped tables
--

--
-- Constraints for table `m_role_privilege`
--
ALTER TABLE `m_role_privilege`
  ADD CONSTRAINT `role_privilige_priv_fk` FOREIGN KEY (`privilege_id`) REFERENCES `privilege` (`id`),
  ADD CONSTRAINT `role_privilige_role_fk` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`);

--
-- Constraints for table `m_user_role`
--
ALTER TABLE `m_user_role`
  ADD CONSTRAINT `user_role_role_fk` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`),
  ADD CONSTRAINT `user_role_user_fk` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);
  
create table saved_sql (
`id` INT NOT NULL AUTO_INCREMENT , 
`session_id` VARCHAR(120) NOT NULL  ,
`sqltext` VARCHAR(2000) NOT NULL  ,
`created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'date created',
PRIMARY KEY (`id`)
);

--test/foo
insert into user(username,email,first_name,last_name,password_hash) VALUES ('Test','y@x.com','John','Doe','$2y$10$JA6W8MpTPLlwt4nXg7yJKeZYF15L3qmFDXJ42hKBc9fHWmBKKzxv6');



  

COMMIT;

