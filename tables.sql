-- tables
-- 	user
-- 		id (auto increment)
-- 		name
-- 		username
-- 		email
-- 		phone
-- 		password
-- 		pin
-- 		confirm_email (boolean)
-- 		confirm_phone (boolean)
-- 		code
-- 		otp
-- 		code_sent_on
-- 		otp_sent_on
-- 		old_hash
-- 		flag (=0 for normal, =1 for syncing, =2 for reset)
-- 	credentials
-- 		id (auto increment)
-- 		user (foreign key)
-- 		link
-- 		type (boolean) (=0 for username; =1 for email)
-- 		login
-- 		password
-- 	shared
-- 		id (auto increment)
-- 		credential (foreign key)
-- 		shared_by (foreign key)
-- 		shared_to (foreign key)
-- 		shared_on
-- 		shared_till
-- 		approved (boolean)
-- 		link
-- 		type (boolean) (=0 for username; =1 for email)
-- 		login
-- 		password

--
-- Database: `ioki`
--

-- --------------------------------------------------------

--
-- Table structure for table `credentials`
--

CREATE TABLE `credentials` (
  `id` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `link` varchar(255) NOT NULL,
  `type` bit(1) NOT NULL,
  `login` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `shared`
--

CREATE TABLE `shared` (
  `id` int(11) NOT NULL,
  `credential` int(11) NOT NULL,
  `shared_by` int(11) NOT NULL,
  `shared_to` int(11) NOT NULL,
  `shared_on` varchar(255) NOT NULL,
  `shared_till` varchar(255) NOT NULL,
  `approved` bit(1) NOT NULL DEFAULT b'0',
  `link` varchar(255) NOT NULL,
  `type` bit(1) NOT NULL,
  `login` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(127) NOT NULL,
  `username` varchar(127) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `password` varchar(255) NOT NULL,
  `pin` int(7) NOT NULL,
  `confirm_email` bit(1) NOT NULL DEFAULT b'0',
  `confirm_phone` bit(1) NOT NULL DEFAULT b'0',
  `code` varchar(31) NOT NULL DEFAULT '0',
  `otp` varchar(7) NOT NULL DEFAULT '0',
  `code_sent_on` varchar(255) NOT NULL DEFAULT '0',
  `otp_sent_on` varchar(255) NOT NULL DEFAULT '0',
  `old_hash` varchar(255) NOT NULL DEFAULT '0',
  `flag` bit(2) NOT NULL DEFAULT b'0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `credentials`
--
ALTER TABLE `credentials`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shared`
--
ALTER TABLE `shared`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `credentials`
--
ALTER TABLE `credentials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `shared`
--
ALTER TABLE `shared`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;
