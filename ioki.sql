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

--
-- Dumping data for table `credentials`
--

INSERT INTO `credentials` (`id`, `user`, `link`, `type`, `login`, `password`) VALUES
	(7, 2, 'thapar.edu', b'0', 'ptD9mpOAK5g=', 'ptD9mpOAK5g='),
	(8, 2, 'thapar.edu', b'0', '7edR8Pecdlg=', '7edR8Pecdlg=');

-- --------------------------------------------------------

--
-- Table structure for table `locks`
--

CREATE TABLE `locks` (
	`id` int(11) NOT NULL,
	`name` varchar(255) DEFAULT NULL,
	`secret` varchar(255) NOT NULL,
	`user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `locks`
--

INSERT INTO `locks` (`id`, `name`, `secret`, `user`) VALUES
	(1, 'lock', 'e90kpl+IVXm6ZB7MVO0tSw==', 1),
	(2, NULL, 'WV8Y2UffTmJVs8CW8IfeXA==', NULL);

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
-- Table structure for table `shared_locks`
--

CREATE TABLE `shared_locks` (
	`id` int(11) NOT NULL,
	`lock_id` int(11) NOT NULL,
	`shared_by` int(11) NOT NULL,
	`shared_to` int(11) NOT NULL,
	`shared_on` varchar(255) NOT NULL,
	`shared_till` varchar(255) NOT NULL,
	`approved` bit(1) NOT NULL DEFAULT b'0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `shared_locks`
--

INSERT INTO `shared_locks` (`id`, `lock_id`, `shared_by`, `shared_to`, `shared_on`, `shared_till`, `approved`) VALUES
	(3, 1, 1, 2, '1538841898', '1576105920', b'1');

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
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `email`, `phone`, `password`, `pin`, `confirm_email`, `confirm_phone`, `code`, `otp`, `code_sent_on`, `otp_sent_on`, `old_hash`, `flag`) VALUES
	(1, 'akhil mittal', 'amone4', 'mittalakhil182@gmail.com', '9878277277', '$2y$10$2iA2R1V.qjtcwgFjM7LM5uuO4ooXqDJS4U83c3QOJmGpSpub2lf7y', 0, b'1', b'1', '0', '0', '1527615807', '1527616137', '', b'00'),
	(2, 'vineet', 'vini', 'test@email.com', '1234567890', '$2y$10$2pJ7.vCk4Q8lgtNYv8Xq7.LYN8qgeWEpI9ykYuRhCl6mXAao3Aj0u', 0, b'1', b'0', '0', '0', '1527635073', '0', '', b'00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `credentials`
--
ALTER TABLE `credentials`
	ADD PRIMARY KEY (`id`);

--
-- Indexes for table `locks`
--
ALTER TABLE `locks`
	ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shared`
--
ALTER TABLE `shared`
	ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shared_locks`
--
ALTER TABLE `shared_locks`
	ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
	ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `credentials`
--
ALTER TABLE `credentials`
	MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `locks`
--
ALTER TABLE `locks`
	MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `shared`
--
ALTER TABLE `shared`
	MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `shared_locks`
--
ALTER TABLE `shared_locks`
	MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
	MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;