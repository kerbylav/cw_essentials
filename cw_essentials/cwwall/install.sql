CREATE TABLE IF NOT EXISTS `prefix_commentwatcher_watcher_wall_watch` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `wall_id` int(11) NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `wall_id` (`wall_id`),
  KEY `uw` (`wall_id`,`user_id`),
  KEY `prefix_commentwatcher_watcher_wall_watch_fk0` (`user_id`)
) ENGINE=InnoDB;

ALTER TABLE `prefix_commentwatcher_watcher_wall_watch`
  ADD CONSTRAINT `prefix_commentwatcher_watcher_wall_watch_fk0` FOREIGN KEY (`user_id`) REFERENCES `prefix_user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `prefix_commentwatcher_watcher_wall_watch_fk1` FOREIGN KEY (`wall_id`) REFERENCES `prefix_wall` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
