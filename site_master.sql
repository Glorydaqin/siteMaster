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

 Date: 02/12/2019 21:59:38
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for site_account
-- ----------------------------
DROP TABLE IF EXISTS `site_account`;
CREATE TABLE `site_account` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site` varchar(100) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of site_account
-- ----------------------------
BEGIN;
INSERT INTO `site_account` VALUES (1, 'ahrefs', '1912037638@qq.com', 'Ranqinghua1', '2019-12-02 11:46:48', NULL);
COMMIT;

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `deleted` tinyint(2) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of user
-- ----------------------------
BEGIN;
INSERT INTO `user` VALUES (1, 'admin', 'admin', 0, '2019-12-02 11:46:48', '2019-12-02 11:49:24');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
