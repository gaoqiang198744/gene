
SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `gene_sn`
-- ----------------------------
DROP TABLE IF EXISTS `gene_sn`;
CREATE TABLE `gene_sn` (
  `id` mediumint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sn` varchar(20) NOT NULL DEFAULT '',
  `password` varchar(20) NOT NULL DEFAULT '',
  `add_time` int(10) DEFAULT NULL,
  `member_id` mediumint(10) DEFAULT '0',
  `use_time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of gene_sn
-- ----------------------------
