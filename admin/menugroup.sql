/*
Navicat MySQL Data Transfer

Source Server         : Access Solutions Server
Source Server Version : 50717
Source Host           : localhost:3306
Source Database       : rent_a_dress

Target Server Type    : MYSQL
Target Server Version : 50717
File Encoding         : 65001

Date: 2021-04-13 14:19:13
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for `menugroup`
-- ----------------------------
DROP TABLE IF EXISTS `menugroup`;
CREATE TABLE `menugroup` (
  `role_id` varchar(5) NOT NULL,
  `menu_id` varchar(5) NOT NULL,
  PRIMARY KEY (`role_id`,`menu_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 6144 kB; InnoDB free: 6144 kB; InnoDB free: 614';

-- ----------------------------
-- Records of menugroup
-- ----------------------------
INSERT INTO `menugroup` VALUES ('001', '003');
INSERT INTO `menugroup` VALUES ('001', '004');
INSERT INTO `menugroup` VALUES ('001', '006');
INSERT INTO `menugroup` VALUES ('001', '007');
INSERT INTO `menugroup` VALUES ('001', '016');
INSERT INTO `menugroup` VALUES ('001', '018');
INSERT INTO `menugroup` VALUES ('001', '19');
INSERT INTO `menugroup` VALUES ('001', '21');
INSERT INTO `menugroup` VALUES ('001', '22');
INSERT INTO `menugroup` VALUES ('001', '23');
INSERT INTO `menugroup` VALUES ('001', '24');
INSERT INTO `menugroup` VALUES ('001', '25');
INSERT INTO `menugroup` VALUES ('001', '26');
INSERT INTO `menugroup` VALUES ('001', '27');
INSERT INTO `menugroup` VALUES ('002', '004');
INSERT INTO `menugroup` VALUES ('002', '006');
INSERT INTO `menugroup` VALUES ('002', '016');
INSERT INTO `menugroup` VALUES ('002', '19');
INSERT INTO `menugroup` VALUES ('002', '21');
INSERT INTO `menugroup` VALUES ('002', '22');
INSERT INTO `menugroup` VALUES ('002', '23');
INSERT INTO `menugroup` VALUES ('002', '24');
INSERT INTO `menugroup` VALUES ('002', '25');
INSERT INTO `menugroup` VALUES ('002', '26');
INSERT INTO `menugroup` VALUES ('002', '27');
INSERT INTO `menugroup` VALUES ('003', '016');
INSERT INTO `menugroup` VALUES ('003', '19');
INSERT INTO `menugroup` VALUES ('003', '21');
INSERT INTO `menugroup` VALUES ('003', '22');
INSERT INTO `menugroup` VALUES ('003', '23');
INSERT INTO `menugroup` VALUES ('003', '26');
INSERT INTO `menugroup` VALUES ('003', '27');
INSERT INTO `menugroup` VALUES ('004', '016');
INSERT INTO `menugroup` VALUES ('004', '19');
INSERT INTO `menugroup` VALUES ('004', '21');
INSERT INTO `menugroup` VALUES ('004', '22');
INSERT INTO `menugroup` VALUES ('004', '23');
INSERT INTO `menugroup` VALUES ('004', '26');
INSERT INTO `menugroup` VALUES ('004', '27');
INSERT INTO `menugroup` VALUES ('005', '016');
INSERT INTO `menugroup` VALUES ('005', '19');
INSERT INTO `menugroup` VALUES ('005', '21');
INSERT INTO `menugroup` VALUES ('005', '22');
INSERT INTO `menugroup` VALUES ('005', '23');
INSERT INTO `menugroup` VALUES ('005', '26');
INSERT INTO `menugroup` VALUES ('005', '27');
INSERT INTO `menugroup` VALUES ('2', '008');
INSERT INTO `menugroup` VALUES ('2', '010');
INSERT INTO `menugroup` VALUES ('2', '011');
INSERT INTO `menugroup` VALUES ('2', '013');
INSERT INTO `menugroup` VALUES ('2', '014');
INSERT INTO `menugroup` VALUES ('2', '015');
INSERT INTO `menugroup` VALUES ('2', '016');
INSERT INTO `menugroup` VALUES ('2', '017');
INSERT INTO `menugroup` VALUES ('3', '004');
INSERT INTO `menugroup` VALUES ('3', '005');
INSERT INTO `menugroup` VALUES ('3', '006');
INSERT INTO `menugroup` VALUES ('3', '016');
INSERT INTO `menugroup` VALUES ('3', '017');
INSERT INTO `menugroup` VALUES ('4', '004');
INSERT INTO `menugroup` VALUES ('4', '009');
INSERT INTO `menugroup` VALUES ('4', '010');
INSERT INTO `menugroup` VALUES ('4', '011');
INSERT INTO `menugroup` VALUES ('4', '012');
INSERT INTO `menugroup` VALUES ('4', '016');
INSERT INTO `menugroup` VALUES ('4', '017');
