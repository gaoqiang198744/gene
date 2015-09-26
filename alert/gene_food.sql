/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 50542
 Source Host           : localhost
 Source Database       : gene

 Target Server Type    : MySQL
 Target Server Version : 50542
 File Encoding         : utf-8

 Date: 09/26/2015 11:12:09 AM
*/

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `gene_food`
-- ----------------------------
DROP TABLE IF EXISTS `gene_food`;
CREATE TABLE `gene_food` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `food_type` mediumint(10) DEFAULT NULL,
  `food_name` varchar(100) DEFAULT NULL,
  `food_another` varchar(100) DEFAULT NULL,
  `weight` double DEFAULT NULL,
  `eat_part` smallint(6) DEFAULT NULL,
  `heat` smallint(6) DEFAULT NULL,
  `protein` varchar(255) DEFAULT NULL,
  `fat` varchar(255) DEFAULT NULL,
  `carbohydrate` varchar(255) DEFAULT NULL,
  `renieratene` varchar(255) DEFAULT NULL,
  `niacin` varchar(255) DEFAULT NULL,
  `fibre` varchar(255) DEFAULT NULL,
  `cholestenone` varchar(255) DEFAULT NULL,
  `va` varchar(255) DEFAULT NULL,
  `vc` varchar(255) DEFAULT NULL,
  `vd` varchar(255) DEFAULT NULL,
  `ve` varchar(255) DEFAULT NULL,
  `oryzanin` varchar(255) DEFAULT NULL,
  `actochrome` varchar(255) DEFAULT NULL,
  `acid` int(11) DEFAULT NULL,
  `ca` varchar(255) DEFAULT NULL,
  `p` varchar(255) DEFAULT NULL,
  `k` varchar(255) DEFAULT NULL,
  `na` varchar(255) DEFAULT NULL,
  `mg` varchar(255) DEFAULT NULL,
  `fe` varchar(255) DEFAULT NULL,
  `zn` varchar(255) DEFAULT NULL,
  `se` varchar(255) DEFAULT NULL,
  `cu` varchar(255) DEFAULT NULL,
  `mn` varchar(255) DEFAULT NULL,
  `add_time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

SET FOREIGN_KEY_CHECKS = 1;
