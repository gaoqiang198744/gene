/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50621
Source Host           : localhost:3306
Source Database       : gene

Target Server Type    : MYSQL
Target Server Version : 50621
File Encoding         : 65001

Date: 2015-09-27 22:00:57
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `gene_nutrition`
-- ----------------------------
DROP TABLE IF EXISTS `gene_nutrition`;
CREATE TABLE `gene_nutrition` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sid` mediumint(10) DEFAULT NULL,
  `daye` varchar(10) DEFAULT NULL,
  `gs` varchar(10) DEFAULT NULL,
  `gs_example` varchar(50) DEFAULT NULL,
  `vegetables` varchar(50) DEFAULT NULL,
  `mem` varchar(50) DEFAULT NULL,
  `oil` varchar(50) DEFAULT NULL,
  `breakfast` varchar(500) DEFAULT NULL,
  `breakfaste` varchar(50) DEFAULT NULL,
  `lunch` varchar(500) DEFAULT NULL,
  `lunche` varchar(50) DEFAULT NULL,
  `dinner` varchar(500) DEFAULT NULL,
  `dinnere` varchar(50) DEFAULT NULL,
  `add_time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of gene_nutrition
-- ----------------------------

-- ----------------------------
-- Table structure for `gene_result`
-- ----------------------------
DROP TABLE IF EXISTS `gene_result`;
CREATE TABLE `gene_result` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sid` mediumint(10) DEFAULT NULL,
  `disease_cat` varchar(20) DEFAULT NULL,
  `disease_name` varchar(100) DEFAULT NULL,
  `disease_level` varchar(50) DEFAULT NULL,
  `add_time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of gene_result
-- ----------------------------

-- ----------------------------
-- Table structure for `gene_sport`
-- ----------------------------
DROP TABLE IF EXISTS `gene_sport`;
CREATE TABLE `gene_sport` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sid` mediumint(10) DEFAULT NULL,
  `aerobic` varchar(20) DEFAULT NULL,
  `power_frequency` varchar(20) DEFAULT NULL,
  `week1` varchar(500) DEFAULT NULL,
  `week2` varchar(500) DEFAULT NULL,
  `week3` varchar(500) DEFAULT NULL,
  `week4` varchar(500) DEFAULT NULL,
  `week5` varchar(500) DEFAULT NULL,
  `week6` varchar(500) DEFAULT NULL,
  `week7` varchar(500) DEFAULT NULL,
  `max_heat` smallint(5) unsigned DEFAULT NULL,
  `reserve_heat` smallint(5) unsigned DEFAULT NULL,
  `fast_lower` varchar(50) DEFAULT NULL,
  `fast_upper` varchar(50) DEFAULT NULL,
  `slow_lower` varchar(50) DEFAULT NULL,
  `slow_upper` varchar(50) DEFAULT NULL,
  `add_time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of gene_sport
-- ----------------------------
