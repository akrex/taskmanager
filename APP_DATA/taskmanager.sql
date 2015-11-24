SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for task
-- ----------------------------
DROP TABLE IF EXISTS `task`;
CREATE TABLE `task` (
  `task_id` int(4) NOT NULL AUTO_INCREMENT,
  `name` varchar(256) NOT NULL,
  `description` varchar(512) DEFAULT NULL,
  `start` datetime DEFAULT NULL,
  `end` datetime DEFAULT NULL,
  `notify_time` time DEFAULT NULL,
  PRIMARY KEY (`task_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for task_to_user
-- ----------------------------
DROP TABLE IF EXISTS `task_to_user`;
CREATE TABLE `task_to_user` (
  `ttu_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `email_notify` bit(1) NOT NULL DEFAULT b'0',
  `phone_notify` bit(1) NOT NULL DEFAULT b'0',
  `email_sended` bit(1) NOT NULL DEFAULT b'0',
  `phone_sended` bit(1) NOT NULL DEFAULT b'0',
  PRIMARY KEY (`ttu_id`),
  KEY `task_to_user_ibfk_2` (`task_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `task_to_user_ibfk_2` FOREIGN KEY (`task_id`) REFERENCES `task` (`task_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `task_to_user_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for token
-- ----------------------------
DROP TABLE IF EXISTS `token`;
CREATE TABLE `token` (
  `token_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `token` varchar(32) NOT NULL,
  `data_expired` datetime NOT NULL,
  PRIMARY KEY (`token_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `token_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `user_id` int(4) NOT NULL AUTO_INCREMENT,
  `username` varchar(64) NOT NULL,
  `FIO` varchar(128) DEFAULT NULL,
  `email` varchar(128) DEFAULT NULL,
  `email_checked` bit(1) NOT NULL DEFAULT b'0',
  `password` varchar(32) NOT NULL,
  `phone` varchar(128) DEFAULT NULL,
  `mail_verefication_token` varchar(32) DEFAULT NULL,
  `restore_token` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;
