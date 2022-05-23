/*
Navicat MySQL Data Transfer

Source Server         : Access Solutions Server
Source Server Version : 50717
Source Host           : localhost:3306
Source Database       : rent_a_dress

Target Server Type    : MYSQL
Target Server Version : 50717
File Encoding         : 65001

Date: 2021-04-13 14:18:05
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for `role`
-- ----------------------------
DROP TABLE IF EXISTS `role`;
CREATE TABLE `role` (
  `role_id` varchar(5) NOT NULL DEFAULT '',
  `role_name` varchar(60) DEFAULT NULL,
  `role_enabled` char(1) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of role
-- ----------------------------
INSERT INTO `role` VALUES ('001', 'Super Administrator', '', '2009-10-31 18:54:57');
INSERT INTO `role` VALUES ('002', 'CEO', '', '2016-10-26 16:47:09');
INSERT INTO `role` VALUES ('003', 'Manager', '', '2016-10-26 16:47:28');
INSERT INTO `role` VALUES ('004', 'Cashier', null, '2021-03-27 08:23:09');
INSERT INTO `role` VALUES ('005', 'Customer Relations', null, '2021-03-27 08:23:30');
