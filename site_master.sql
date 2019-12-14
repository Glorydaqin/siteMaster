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

 Date: 13/12/2019 11:15:30
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
-- Table structure for site
-- ----------------------------
DROP TABLE IF EXISTS `site`;
CREATE TABLE `site` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of site
-- ----------------------------
BEGIN;
INSERT INTO `site` VALUES (1, 'ahrefs', NULL, '2019-12-12 14:18:22', NULL);
INSERT INTO `site` VALUES (2, 'kwfinder', NULL, '2019-12-12 14:18:30', NULL);
COMMIT;

-- ----------------------------
-- Table structure for site_account
-- ----------------------------
DROP TABLE IF EXISTS `site_account`;
CREATE TABLE `site_account` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) NOT NULL,
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
INSERT INTO `site_account` VALUES (1, 1, '1912037638@qq.com', 'Ranqinghua1', NULL, '2019-12-02 11:46:48', '2019-12-12 14:19:18');
INSERT INTO `site_account` VALUES (2, 2, '1039937183@qq.com', 'daqing', '2019-12-27 09:43:14', '2019-12-09 16:02:43', '2019-12-12 14:19:20');
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
  `expired_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of user
-- ----------------------------
BEGIN;
INSERT INTO `user` VALUES (1, 'admin', 'admin', '2019-12-28 17:07:01', '[{\'site_id\'=>1,\'expired_at\'=>\'2020-20-20 00:00:00\'}]', '2019-12-02 11:46:48', '2019-12-13 02:58:44');
COMMIT;

-- ----------------------------
-- Table structure for user_record
-- ----------------------------
DROP TABLE IF EXISTS `user_record`;
CREATE TABLE `user_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `site_id` int(11) NOT NULL DEFAULT 0,
  `account_id` int(11) NOT NULL,
  `date` date DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of user_record
-- ----------------------------
BEGIN;
INSERT INTO `user_record` VALUES (1, 1, 0, 2, '2019-12-11', 'dashboard', '2019-12-11 07:27:56');
INSERT INTO `user_record` VALUES (2, 1, 0, 2, '2019-12-11', 'assets/fonts/Montserrat-Medium.woff2', '2019-12-11 07:28:39');
INSERT INTO `user_record` VALUES (3, 1, 0, 2, '2019-12-11', 'mangools_domain/analytics/sources/set_cookie?referrer=http%3A%2F%2Fkwfinder.sitemaster.com%2F_in_ds_%2Fchoose%2F&page=http%3A%2F%2Fkwfinder.sitemaster.com%2Fdashboard&r=1576049304985', '2019-12-11 07:28:39');
INSERT INTO `user_record` VALUES (4, 1, 0, 2, '2019-12-11', 'assets/fonts/Montserrat-Medium.woff', '2019-12-11 07:28:43');
INSERT INTO `user_record` VALUES (5, 1, 0, 2, '2019-12-11', 'dashboard', '2019-12-11 07:41:09');
INSERT INTO `user_record` VALUES (6, 1, 0, 2, '2019-12-11', 'mangools_domain/analytics/sources/set_cookie?referrer=&page=http%3A%2F%2Fkwfinder.sitemaster.com%2Fdashboard&r=1576050106034', '2019-12-11 07:41:46');
INSERT INTO `user_record` VALUES (7, 1, 0, 2, '2019-12-11', 'assets/fonts/Montserrat-Medium.woff2', '2019-12-11 07:42:03');
INSERT INTO `user_record` VALUES (8, 1, 0, 2, '2019-12-11', 'dashboard', '2019-12-11 07:42:04');
INSERT INTO `user_record` VALUES (9, 1, 0, 2, '2019-12-11', 'assets/fonts/Montserrat-Medium.woff2', '2019-12-11 07:43:08');
INSERT INTO `user_record` VALUES (10, 1, 0, 2, '2019-12-11', 'mangools_domain/analytics/sources/set_cookie?referrer=&page=http%3A%2F%2Fkwfinder.sitemaster.com%2Fdashboard&r=1576050154981', '2019-12-11 07:43:08');
INSERT INTO `user_record` VALUES (11, 1, 0, 2, '2019-12-11', 'assets/favicon.ico', '2019-12-11 07:43:10');
INSERT INTO `user_record` VALUES (12, 1, 0, 2, '2019-12-11', 'dashboard', '2019-12-11 07:44:50');
INSERT INTO `user_record` VALUES (13, 1, 0, 2, '2019-12-11', 'dashboard', '2019-12-11 07:44:51');
INSERT INTO `user_record` VALUES (14, 1, 0, 2, '2019-12-11', 'mangools_domain/analytics/sources/set_cookie?referrer=&page=http%3A%2F%2Fkwfinder.sitemaster.com%2Fdashboard&r=1576050329615', '2019-12-11 07:45:29');
INSERT INTO `user_record` VALUES (15, 1, 0, 2, '2019-12-11', 'assets/favicon.ico', '2019-12-11 07:45:46');
INSERT INTO `user_record` VALUES (16, 1, 0, 2, '2019-12-11', 'dashboard', '2019-12-11 08:38:42');
INSERT INTO `user_record` VALUES (17, 1, 0, 2, '2019-12-11', 'mangools_domain/analytics/sources/set_cookie?referrer=http%3A%2F%2Fkwfinder.sitemaster.com%2F_in_ds_%2Fchoose%2F&page=http%3A%2F%2Fkwfinder.sitemaster.com%2Fdashboard&r=1576053523205', '2019-12-11 08:38:46');
INSERT INTO `user_record` VALUES (18, 1, 0, 2, '2019-12-11', 'dashboard', '2019-12-11 08:43:17');
INSERT INTO `user_record` VALUES (19, 1, 0, 2, '2019-12-11', 'dashboard', '2019-12-11 08:43:18');
INSERT INTO `user_record` VALUES (20, 1, 0, 2, '2019-12-11', 'mangools_domain/analytics/sources/set_cookie?referrer=&page=http%3A%2F%2Fkwfinder.sitemaster.com%2Fdashboard&r=1576053799294', '2019-12-11 08:43:21');
INSERT INTO `user_record` VALUES (21, 1, 0, 2, '2019-12-11', 'assets/fonts/Montserrat-Medium.woff2', '2019-12-11 08:48:14');
INSERT INTO `user_record` VALUES (22, 1, 0, 2, '2019-12-11', 'dashboard', '2019-12-11 08:48:16');
INSERT INTO `user_record` VALUES (23, 1, 0, 2, '2019-12-11', 'dashboard', '2019-12-11 09:01:59');
INSERT INTO `user_record` VALUES (24, 1, 0, 2, '2019-12-11', 'assets/108c967953c5939495c4c79693266102.svg', '2019-12-11 10:19:51');
INSERT INTO `user_record` VALUES (25, 1, 0, 1, '2019-12-12', 'dashboard', '2019-12-12 14:42:23');
INSERT INTO `user_record` VALUES (26, 1, 0, 1, '2019-12-12', 'dashboard', '2019-12-12 15:09:14');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
