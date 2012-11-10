CREATE TABLE IF NOT EXISTS `prefix_commentwatcher_watcher_data` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `comment_id` int(11) unsigned DEFAULT NULL,
  `commented_id` int(11) unsigned DEFAULT NULL,
  `container_id` int(11) unsigned NOT NULL,
  `container_type` enum('core') NOT NULL DEFAULT 'core',
  `replier_id` int(11) unsigned NOT NULL,
  `owner_id` int(11) unsigned NOT NULL,
  `date_add` datetime NOT NULL,
  `comment_active` tinyint(1) NOT NULL DEFAULT '1',
  `comment_type` enum('direct','later','indirect','favority') NOT NULL DEFAULT 'indirect',
  PRIMARY KEY (`id`),
  KEY `owner_id` (`owner_id`),
  KEY `o_ct_at` (`owner_id`,`container_type`,`comment_type`),
  KEY `prefix_commentwatcher_watcher_data_fk1` (`commented_id`),
  KEY `fsearch` (`owner_id`,`container_type`,`comment_id`,`comment_type`,`comment_active`)
) ENGINE=InnoDB;

ALTER TABLE `prefix_commentwatcher_watcher_data`
  ADD CONSTRAINT `prefix_commentwatcher_watcher_data_fk0` FOREIGN KEY (`comment_id`) REFERENCES `prefix_comment` (`comment_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `prefix_commentwatcher_watcher_data_fk1` FOREIGN KEY (`commented_id`) REFERENCES `prefix_comment` (`comment_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `prefix_commentwatcher_watcher_data_fk2` FOREIGN KEY (`replier_id`) REFERENCES `prefix_user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `prefix_commentwatcher_watcher_data_fk3` FOREIGN KEY (`owner_id`) REFERENCES `prefix_user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
