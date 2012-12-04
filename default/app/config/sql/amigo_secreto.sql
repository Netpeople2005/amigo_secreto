/*
Navicat MySQL Data Transfer

Source Server         : mi conexion
Source Server Version : 50051
Source Host           : 192.168.1.3:3306
Source Database       : amigo_secreto

Target Server Type    : MYSQL
Target Server Version : 50051
File Encoding         : 65001

Date: 2012-12-03 13:35:03
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for `chat`
-- ----------------------------
DROP TABLE IF EXISTS `chat`;
CREATE TABLE `chat` (
  `id` int(11) NOT NULL auto_increment,
  `usuarios_id` int(11) NOT NULL,
  `texto` text NOT NULL,
  `style` varchar(100) default NULL,
  `fecha` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of chat
-- ----------------------------

-- ----------------------------
-- Table structure for `equipos_registrados`
-- ----------------------------
DROP TABLE IF EXISTS `equipos_registrados`;
CREATE TABLE `equipos_registrados` (
  `id` int(11) NOT NULL auto_increment,
  `descripcion` varchar(200) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of equipos_registrados
-- ----------------------------

-- ----------------------------
-- Table structure for `usuarios`
-- ----------------------------
DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL auto_increment,
  `personaje` varchar(100) NOT NULL,
  `imagen` varchar(200) default NULL,
  `en_uso` smallint(1) NOT NULL default '0',
  `clave` varchar(50) default NULL,
  `regalo_esperado` text,
  `amigo_asignado` smallint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of usuarios
-- ----------------------------
INSERT INTO `usuarios` VALUES ('13', 'Batichica', 'perfiles/batichica.gif', '0', 'e10adc3949ba59abbe56e057f20f883e', null, '0');
INSERT INTO `usuarios` VALUES ('14', 'Superman', 'perfiles/superman.gif', '0', null, null, '0');
INSERT INTO `usuarios` VALUES ('15', 'Batman', 'perfiles/batman.gif', '0', null, null, '0');
INSERT INTO `usuarios` VALUES ('16', 'Robin', 'perfiles/robin.gif', '0', null, null, '0');
INSERT INTO `usuarios` VALUES ('17', 'Aquaman', 'perfiles/aquaman.gif', '0', null, null, '0');
INSERT INTO `usuarios` VALUES ('18', 'Mujer Maravilla', 'perfiles/mujer_maravilla.gif', '0', null, null, '0');
INSERT INTO `usuarios` VALUES ('19', 'Flash', 'perfiles/flash.gif', '0', null, null, '0');
INSERT INTO `usuarios` VALUES ('20', 'Linterna Verde', 'perfiles/linterna_verde.gif', '0', null, null, '0');
INSERT INTO `usuarios` VALUES ('21', 'Gatubela', 'perfiles/gatubela.gif', '0', null, null, '0');
INSERT INTO `usuarios` VALUES ('22', 'Hiedra Venenosa', 'perfiles/hiedra_venenosa.gif', '0', null, null, '0');
INSERT INTO `usuarios` VALUES ('23', 'El Acertijo', 'perfiles/el_acertijo.gif', '0', null, null, '0');
INSERT INTO `usuarios` VALUES ('24', 'Zan', 'perfiles/zan.gif', '0', null, null, '0');
INSERT INTO `usuarios` VALUES ('25', 'Jayna', 'perfiles/jayna.gif', '0', null, null, '0');
