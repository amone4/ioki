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
	(6, 1, 'thapar.edu', b'0', 'W+WF67hVuoI=', 'W+WF67hVuoI=');

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

--
-- Dumping data for table `shared`
--

INSERT INTO `shared` (`id`, `credential`, `shared_by`, `shared_to`, `shared_on`, `shared_till`, `approved`, `link`, `type`, `login`, `password`) VALUES
	(14, 6, 1, 2, '1534218831', '1607778000', b'1', 'thapar.edu', b'1', 'W+WF67hVuoI=', 'W+WF67hVuoI=');

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
	(1, 'akhil mittal', 'amone4', 'mittalakhil182@gmail.com', '9878277277', '$2y$10$yPzWV3ItoL.dUGG8WwUax.JORhbbcziJVIZXTLhKm9F9NUHN3M6H2', 0, b'1', b'1', '0', '0', '1527615807', '1527616137', '', b'00'),
	(2, 'vineet', 'vini', 'test@email.com', '1234567890', '$2y$10$bkA9qw0TZZNr255OdR7YIeBJ24BvaUNdkTX4GCALyb44df3uk/zkS', 0, b'1', b'0', '0', '0', '1527635073', '0', '', b'00');

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
	MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `shared`
--
ALTER TABLE `shared`
	MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
	MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;