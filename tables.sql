-- run this code to create the user table;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `preferred_mode` varchar(10) DEFAULT 'light'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


-- run this one to create the messages table;
CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- IF it doesnt seem to work on the register code run these

-- Modify the 'id' column to be an auto-incremented primary key
ALTER TABLE `users` MODIFY `id` INT AUTO_INCREMENT PRIMARY KEY;

-- Modify the 'id' column to be an auto-incremented primary key
ALTER TABLE `messages` MODIFY `id` INT AUTO_INCREMENT PRIMARY KEY;
