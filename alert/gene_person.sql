/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50621
Source Host           : localhost:3306
Source Database       : gene

Target Server Type    : MYSQL
Target Server Version : 50621
File Encoding         : 65001

Date: 2015-09-25 23:03:53
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `gene_person`
-- ----------------------------
DROP TABLE IF EXISTS `gene_person`;
CREATE TABLE `gene_person` (
  `id` mediumint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sid` mediumint(20) NOT NULL,
  `member_name` varchar(50) NOT NULL,
  `sex` varchar(10) NOT NULL,
  `birthday` char(10) NOT NULL,
  `age` smallint(4) DEFAULT NULL,
  `height` smallint(4) DEFAULT NULL,
  `weight` smallint(4) DEFAULT NULL,
  `waistline` smallint(4) DEFAULT NULL,
  `hip` smallint(4) DEFAULT NULL,
  `heart` smallint(4) DEFAULT NULL,
  `job` smallint(4) DEFAULT NULL,
  `sport` smallint(1) DEFAULT NULL,
  `add_time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
