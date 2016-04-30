--
-- Table structure for table `jos_users`
--

CREATE TABLE `jos_users` (
  `id` int(11) PRIMARY KEY NOT NULL,
  `username` varchar(200) NOT NULL,
  `first_name` varchar(200) NOT NULL,
  `last_name` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
