CREATE TABLE IF NOT EXISTS `{tablename}` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `gallery_name` varchar(255) DEFAULT NULL,
  `dstamp` datetime DEFAULT NULL,
  `width` int(10) unsigned NOT NULL DEFAULT '400',
  `height` int(10) unsigned NOT NULL DEFAULT '200',
  `timeout` int(10) unsigned NOT NULL DEFAULT '4000',
  `items` text NOT NULL,
  `transition_speed` int(10) unsigned NOT NULL DEFAULT '1000',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;