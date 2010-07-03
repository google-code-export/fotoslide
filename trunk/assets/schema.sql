CREATE TABLE IF NOT EXISTS `{PREFIX}wps3_galleries` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `gallery_name` varchar(255) DEFAULT NULL,
  `dstamp` datetime DEFAULT NULL,
  `overlay_colour` varchar(7) NOT NULL DEFAULT '#000000',
  `text_colour` varchar(7) NOT NULL DEFAULT '#FFFFFF',
  `opacity` float NOT NULL DEFAULT '0.7',
  `width` int(10) unsigned NOT NULL DEFAULT '400',
  `height` int(10) unsigned NOT NULL DEFAULT '200',
  `timeout` int(10) unsigned NOT NULL DEFAULT '4000',
  `items` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;