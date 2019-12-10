/*
 Navicat MySQL Data Transfer

 Source Server         : vagrant
 Source Server Type    : MySQL
 Source Server Version : 100314
 Source Host           : 192.168.100.100:3306
 Source Schema         : site_master

 Target Server Type    : MySQL
 Target Server Version : 100314
 File Encoding         : 65001

 Date: 10/12/2019 19:57:23
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for log
-- ----------------------------
DROP TABLE IF EXISTS `log`;
CREATE TABLE `log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `content` mediumtext DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for site_account
-- ----------------------------
DROP TABLE IF EXISTS `site_account`;
CREATE TABLE `site_account` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site` varchar(100) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `expired_at` datetime DEFAULT NULL COMMENT '账号失效时间',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of site_account
-- ----------------------------
BEGIN;
INSERT INTO `site_account` VALUES (1, 'ahrefs', '1912037638@qq.com', 'Ranqinghua1', NULL, '2019-12-02 11:46:48', NULL);
INSERT INTO `site_account` VALUES (2, 'mangools', '1039937183@qq.com', 'daqing', '2019-12-27 09:43:14', '2019-12-09 16:02:43', '2019-12-09 16:02:48');
COMMIT;

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `expired_at` datetime DEFAULT '0000-00-00 00:00:00',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of user
-- ----------------------------
BEGIN;
INSERT INTO `user` VALUES (1, 'admin', 'admin', '2019-12-28 17:07:01', '2019-12-02 11:46:48', '2019-12-07 09:07:10');
COMMIT;

-- ----------------------------
-- Table structure for user_record
-- ----------------------------
DROP TABLE IF EXISTS `user_record`;
CREATE TABLE `user_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `date` date DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;
