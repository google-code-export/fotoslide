CREATE TABLE  IF NOT EXISTS  `{tablename_galleries}` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `gallery_name` varchar(255) DEFAULT NULL,
  `dstamp` datetime DEFAULT NULL,
  `width` int(10) unsigned NOT NULL DEFAULT '400',
  `height` int(10) unsigned NOT NULL DEFAULT '200',
  `pauseTime` int(10) unsigned NOT NULL DEFAULT '4000',
  `animSpeed` int(10) unsigned NOT NULL DEFAULT '1000',
  `effect` varchar(15) NOT NULL DEFAULT 'random',
  `captionOpacity` float(2,1) NOT NULL DEFAULT '0.8',
  `class_attribute` varchar(120) NOT NULL DEFAULT 'fotoslide',
  `manualAdvance` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `pauseOnHover` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `keyboardNav` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `randomize_first` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `slices` tinyint(3) unsigned NOT NULL DEFAULT '15',
  `directionNav` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `directionNavHide` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `controlNav` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `controlNavThumbs` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `controlNavThumbsFromRel` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `controlNavThumbsSearch` varchar(45) NOT NULL DEFAULT '.jpg',
  `controlNavThumbsReplace` varchar(45) NOT NULL DEFAULT '_thumb.jpg',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
@next
CREATE TABLE  IF NOT EXISTS  `{tablename_items}` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `caption_text` text NOT NULL,
  `href` text NOT NULL,
  `gallery_id` int(10) unsigned NOT NULL DEFAULT '0',
  `order_num` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;