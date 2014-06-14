-- MySQL dump 10.13  Distrib 5.6.15, for osx10.8 (x86_64)
--
-- Host: localhost    Database: bysj_fcc
-- ------------------------------------------------------
-- Server version	5.6.15

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `tb_exams`
--

DROP TABLE IF EXISTS `tb_exams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_exams` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增长ID',
  `s_name` varchar(300) DEFAULT NULL COMMENT '试卷名称',
  `n_score` int(10) NOT NULL COMMENT '试卷总分',
  `n_subject` int(10) DEFAULT NULL COMMENT '科目',
  `dt_create` int(10) DEFAULT NULL COMMENT '试卷创建时间',
  `n_spend` int(10) DEFAULT NULL COMMENT '考试时间（单位：秒）',
  `s_content` varchar(2000) DEFAULT NULL COMMENT '试卷内容（json格式）',
  `n_user_id` int(11) DEFAULT NULL COMMENT '创建用户',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='试卷表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_exams`
--

LOCK TABLES `tb_exams` WRITE;
/*!40000 ALTER TABLE `tb_exams` DISABLE KEYS */;
INSERT INTO `tb_exams` VALUES (1,'数据结构10级考试A卷',31,NULL,1401763740,7200,'[{\"title\":\"一、选择题（每小题1分，共5分）\",\"data\":[{\"id\":9},{\"id\":8},{\"id\":12},{\"id\":11},{\"id\":10}]},{\"title\":\"二、填空题（每空2分，共10分）\",\"data\":[{\"id\":15},{\"id\":14},{\"id\":13}]},{\"title\":\"三、简答题（每小题8分，共16分）\",\"data\":[{\"id\":17},{\"id\":16}]}]',1);
/*!40000 ALTER TABLE `tb_exams` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_question_sort`
--

DROP TABLE IF EXISTS `tb_question_sort`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_question_sort` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `s_name` varchar(100) NOT NULL COMMENT '分类名',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='试题分类表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_question_sort`
--

LOCK TABLES `tb_question_sort` WRITE;
/*!40000 ALTER TABLE `tb_question_sort` DISABLE KEYS */;
INSERT INTO `tb_question_sort` VALUES (1,'单选题'),(2,'多选题'),(3,'填空题'),(4,'判断题'),(5,'简答题');
/*!40000 ALTER TABLE `tb_question_sort` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_questions`
--

DROP TABLE IF EXISTS `tb_questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `s_title` varchar(1000) NOT NULL COMMENT '试题题目',
  `s_options` varchar(500) NOT NULL COMMENT '问题选项',
  `n_options_num` int(2) DEFAULT NULL COMMENT '选项数目',
  `s_answer` varchar(500) NOT NULL COMMENT '问题答案',
  `s_analyse` varchar(500) NOT NULL COMMENT '问题解析',
  `n_sort` int(11) NOT NULL COMMENT '试题分类',
  `f_score` decimal(5,2) NOT NULL COMMENT '试题分值',
  `dt_insert` int(10) NOT NULL COMMENT '插入时间',
  `n_subject_id` int(10) NOT NULL COMMENT '科目',
  `n_user_id` int(11) NOT NULL COMMENT '创建试题的用户id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COMMENT='试题表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_questions`
--

LOCK TABLES `tb_questions` WRITE;
/*!40000 ALTER TABLE `tb_questions` DISABLE KEYS */;
INSERT INTO `tb_questions` VALUES (1,'<p>在一个单链表HL中，若要向表头插入一个由指针p指向的结点，则执行( )。B</p>','<p>A． &nbsp;HL＝ps p一&gt;next＝HL &nbsp; &nbsp;</p><p>B． &nbsp;p一&gt;next＝HL；HL＝p3 &nbsp; &nbsp;</p><p>C． &nbsp;p一&gt;next＝Hl；p＝HL；&nbsp;</p><p>D． &nbsp;p一&gt;next＝HL一&gt;next;HL一&gt;next＝p；</p>',4,'B','<p>答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B答案是B</p>',1,2.00,1401463801,4,1),(2,'<p>n个顶点的强连通图中至少含有( &nbsp; &nbsp;)。B</p>','<p>&nbsp;A.n&mdash;l条有向边 &nbsp; &nbsp;B.n条有向边&nbsp;<br />&nbsp;C.n(n&mdash;1)／2条有向边 &nbsp; &nbsp;D.n(n一1)条有向边</p>',4,'B','<p>B</p>',1,2.00,1401463864,4,1),(3,'<p>从一棵二叉搜索树中查找一个元素时，其时间复杂度大致为( &nbsp; &nbsp;)。 C</p>','<p>A.O(1) &nbsp; &nbsp;B.O(n)&nbsp;<br />C.O(1Ogzn) &nbsp; &nbsp;D.O(n2)&nbsp;</p>',5,'C','<p>答案是C</p>',1,2.00,1401463939,4,1),(4,'<p>由权值分别为3，8，6，2，5的叶子结点生成一棵哈夫曼树，它的带权路径长度为( )。D</p>','<p>A．24 &nbsp; &nbsp; &nbsp;B．48&nbsp;<br />C．72 &nbsp; &nbsp; &nbsp;D.&nbsp;53&nbsp;</p>',4,'D','',1,2.00,1401463992,4,1),(5,'<p>数据的存储结构被分为_A_、_B_、_C_和_D_四种。</p>','',4,'{\"A\":\"顺序结构\",\"B\":\"    链接结构\",\"C\":\"    索引结构\",\"D\":\"    散列结构(次序无先后) \"}','<p>填空题哦</p>',3,4.00,1401465180,4,1),(8,'<p>以下数据结构中，()是线性结构。</p>\n','<p>A）栈 &nbsp; &nbsp; &nbsp; &nbsp;B）树 &nbsp; &nbsp; &nbsp; C）二叉树 &nbsp; &nbsp; D）图</p>\n',4,'A','',1,1.00,1401762570,4,1),(9,'<p>在所有排序方法中，关键字比较的次数与记录的初始排列次序无关的是<u> &nbsp; &nbsp; &nbsp;</u>。</p>\n','<p>A、选择排序 &nbsp;B、冒泡排序 &nbsp; C、插入排序 &nbsp; D、希尔排序</p>\n',4,'A','',1,1.00,1401762634,4,1),(10,'<p>下面<u> &nbsp; &nbsp; &nbsp; </u>是顺序存储结构的优点。</p>\n','<p>A）存储密度大 &nbsp;B）插入运算方便 &nbsp;C）查找方便 &nbsp;D）适合各种逻辑结构的存储表示</p>\n',4,'C','',1,1.00,1401762682,4,1),(11,'<p>用链式方式存储的队列，在进行插入运算时，<u> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </u>。&nbsp;</p>\n','<p>A）仅修改头指针&nbsp; &nbsp; &nbsp;&nbsp;</p>\n\n<p>B）仅修改尾指针 &nbsp; &nbsp;</p>\n\n<p>C）头、尾指针都要修改 &nbsp; &nbsp; &nbsp;&nbsp;</p>\n\n<p>D）头、尾指针可能都要修改</p>\n',4,'A','',1,1.00,1401762780,4,1),(12,'<p>从未排序序列中依次取出一个元素与已排序序列中的元素依次进行比较，然后将其放在已排序序列的合适位置，该排序方法称为<u> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</u>排序法。</p>\n','<p>A）插入 &nbsp; &nbsp; &nbsp; &nbsp;B）选择 &nbsp; &nbsp; &nbsp; C）冒泡 &nbsp; &nbsp; &nbsp; &nbsp;D）都不是&nbsp;</p>\n',4,'A','',1,1.00,1401762879,4,1),(13,'<p>对于一个长度为n的顺序存储的线性表，在表头插入元素的时间复杂度为<u>&nbsp; &nbsp; A &nbsp; &nbsp; &nbsp;</u>，在表尾插入元素的时间复杂度为<u>&nbsp; &nbsp; B &nbsp; &nbsp;&nbsp;</u>。</p>\n','',2,'{\"A\":\" O（N）\",\"B\":\"  O（1）\"}','',3,4.00,1401763104,4,1),(14,'<p>队列的插入操作在<u>&nbsp; &nbsp; &nbsp;A &nbsp; &nbsp; &nbsp;</u>进行，栈的删除操作在<u>&nbsp; &nbsp; B &nbsp; &nbsp; &nbsp;</u>进行。</p>\n','',2,'{\"A\":\"队尾\",\"B\":\"栈顶\"}','',3,4.00,1401763154,4,1),(15,'<p>设字符串S1=&lsquo;ABCDEFG&rsquo;，S2=&lsquo;PQRST&rsquo;，则运算S=CONCAT（SUB（S1，2，LEN（S2）），SUB（S1，LEN（S2），2））后的串值为<u>&nbsp; &nbsp; &nbsp;A &nbsp; &nbsp; &nbsp;</u>&nbsp;。</p>\n','',1,'{\"A\":\"BCDEFEF\"}','',3,2.00,1401763220,4,1),(16,'<p>分别给出对下图进行深度优先和广度优先遍历的结果。</p>\n\n<p><img alt=\"\" src=\"./app/public/uploads/1401763394.png\" style=\"height:168px; width:452px\" /></p>\n','',4,'<p>深度：125963784 &nbsp;（不唯一） &nbsp; &nbsp;</p>\n\n<p>广度：123456789 &nbsp;（不唯一）</p>\n','',5,8.00,1401763408,4,1),(17,'<p>已知序列（12，4，17，10，7，30），用直接选择排序法对其进行递增排序，写出每一趟的排序结果。</p>\n','',4,'<p>第1趟：4 12 17 10 7 30 &nbsp; &nbsp;</p>\n\n<p>第2趟：4 7 17 10 12 30</p>\n\n<p>第3趟：4 7 10 17 12 30</p>\n\n<p>第4趟：4 7 10 12 17 30</p>\n\n<p>第5趟：4 7 10 12 17 30</p>\n','',5,8.00,1401763450,4,1);
/*!40000 ALTER TABLE `tb_questions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_subjects`
--

DROP TABLE IF EXISTS `tb_subjects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_subjects` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `s_name` varchar(200) NOT NULL COMMENT '科目名称',
  `dt_insert` int(10) NOT NULL COMMENT '创建时间',
  `n_user` int(11) NOT NULL COMMENT '创建人id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='科目表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_subjects`
--

LOCK TABLES `tb_subjects` WRITE;
/*!40000 ALTER TABLE `tb_subjects` DISABLE KEYS */;
INSERT INTO `tb_subjects` VALUES (1,'语文',1400640778,1),(2,'数学',1400640778,1),(3,'C语言',1400640842,1),(4,'数据结构',1400640842,1);
/*!40000 ALTER TABLE `tb_subjects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_user`
--

DROP TABLE IF EXISTS `tb_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `s_account` varchar(200) DEFAULT NULL COMMENT '账号',
  `s_nickname` varchar(200) DEFAULT NULL COMMENT '昵称',
  `s_password` char(32) DEFAULT NULL COMMENT '密码',
  `s_email` varchar(200) DEFAULT NULL COMMENT 'email',
  `n_login_count` int(5) DEFAULT '0' COMMENT '登陆次数',
  `dt_last_login` int(10) DEFAULT NULL COMMENT '上次登陆时间',
  `s_last_login_ip` varchar(15) DEFAULT NULL COMMENT '上次登陆ip',
  `n_role` int(1) NOT NULL DEFAULT '1' COMMENT '用户角色1：学生 2：教师',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COMMENT='用户表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_user`
--

LOCK TABLES `tb_user` WRITE;
/*!40000 ALTER TABLE `tb_user` DISABLE KEYS */;
INSERT INTO `tb_user` VALUES (1,'admin','教师用户','202cb962ac59075b964b07152d234b70',NULL,72,1402108687,'127.0.0.1',2),(5,'111','fdaaaaa','202cb962ac59075b964b07152d234b70',NULL,6,1401650787,'127.0.0.1',1),(6,'fda','fda','1bb1dbb613d0db2041e35f52fea672c7',NULL,0,NULL,NULL,1),(8,'fda','fdas','1bb1dbb613d0db2041e35f52fea672c7',NULL,0,NULL,NULL,1),(9,'dfdafda','fds','dac7c47ed74eade0cbf3d5b06ac14f3f',NULL,0,NULL,NULL,1),(10,'fas','fda','c5341e883d09ced169abfac23dc13abc',NULL,0,NULL,NULL,1),(11,'fas','fas','c5341e883d09ced169abfac23dc13abc',NULL,0,NULL,NULL,1),(12,'fa','fda','89e6d2b383471fc370d828e552c19e65',NULL,0,NULL,NULL,1),(13,'f','f','8fa14cdd754f91cc6554c9e71929cce7',NULL,0,NULL,NULL,1),(15,'js','fd','32981a13284db7a021131df49e6cd203',NULL,1,1401646299,'127.0.0.1',2),(16,'aaaaaaaaa','fda','552e6a97297c53e592208cf97fbb3b60',NULL,0,NULL,NULL,1),(18,'10101020122','杨涛','bb3f572dd995a4d2e3a3562a845a2a1c',NULL,2,1401804037,'192.168.1.110',1);
/*!40000 ALTER TABLE `tb_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_user_answers`
--

DROP TABLE IF EXISTS `tb_user_answers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_user_answers` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `s_examing` varchar(20) NOT NULL COMMENT '唯一参考序号',
  `n_exam_id` int(11) NOT NULL COMMENT '试卷ID',
  `n_question_id` int(11) NOT NULL COMMENT '试题ID',
  `n_user_id` int(11) NOT NULL COMMENT '用户ID',
  `s_answer` varchar(2000) DEFAULT NULL COMMENT '答案',
  `f_score` decimal(5,2) DEFAULT NULL COMMENT '得分',
  `n_error` tinyint(1) NOT NULL COMMENT '错误',
  `n_verifyed` tinyint(1) DEFAULT '0' COMMENT '是否阅卷',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8 COMMENT='用户已考试题答案';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_user_answers`
--

LOCK TABLES `tb_user_answers` WRITE;
/*!40000 ALTER TABLE `tb_user_answers` DISABLE KEYS */;
INSERT INTO `tb_user_answers` VALUES (1,'b723c720421401804075',1,8,18,'C',0.00,1,1),(2,'b723c720421401804075',1,9,18,'C',0.00,1,1),(3,'b723c720421401804075',1,10,18,'C',1.00,0,1),(4,'b723c720421401804075',1,11,18,'C',0.00,1,1),(5,'b723c720421401804075',1,12,18,'C',0.00,1,1),(6,'b723c720421401804075',1,13,18,'{\"A\":\"fdsa\",\"B\":\"fdsa\"}',0.00,1,1),(7,'b723c720421401804075',1,14,18,'{\"A\":\"fda\",\"B\":\"fds\"}',0.00,1,1),(8,'b723c720421401804075',1,15,18,'{\"A\":\"bacfd\"}',0.00,1,1),(9,'b723c720421401804075',1,16,18,'fdsa',0.00,1,1),(10,'b723c720421401804075',1,17,18,'fdsafas',0.00,1,1),(11,'f5ebaa7f9e1401892749',1,8,1,'',0.00,1,0),(12,'f5ebaa7f9e1401892749',1,9,1,'',0.00,1,0),(13,'f5ebaa7f9e1401892749',1,10,1,'',0.00,1,0),(14,'f5ebaa7f9e1401892749',1,11,1,'',0.00,1,0),(15,'f5ebaa7f9e1401892749',1,12,1,'',0.00,1,0),(16,'f5ebaa7f9e1401892749',1,13,1,'{\"A\":\"\",\"B\":\"\"}',0.00,1,0),(17,'f5ebaa7f9e1401892749',1,14,1,'{\"A\":\"\",\"B\":\"\"}',0.00,1,0),(18,'f5ebaa7f9e1401892749',1,15,1,'{\"A\":\"\"}',0.00,1,0),(19,'f5ebaa7f9e1401892749',1,16,1,'',0.00,1,0),(20,'f5ebaa7f9e1401892749',1,17,1,'',0.00,1,0),(21,'26d4306c761401892865',1,8,1,'',0.00,1,1),(22,'26d4306c761401892865',1,9,1,'',0.00,1,1),(23,'26d4306c761401892865',1,10,1,'',0.00,1,1),(24,'26d4306c761401892865',1,11,1,'',0.00,1,1),(25,'26d4306c761401892865',1,12,1,'',0.00,1,1),(26,'26d4306c761401892865',1,13,1,'{\"A\":\"\",\"B\":\"\"}',0.00,1,1),(27,'26d4306c761401892865',1,14,1,'{\"A\":\"\",\"B\":\"\"}',0.00,1,1),(28,'26d4306c761401892865',1,15,1,'{\"A\":\"\"}',0.00,1,1),(29,'26d4306c761401892865',1,16,1,'',0.00,1,1),(30,'26d4306c761401892865',1,17,1,'',0.00,1,1);
/*!40000 ALTER TABLE `tb_user_answers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_user_examed`
--

DROP TABLE IF EXISTS `tb_user_examed`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_user_examed` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `s_examing` varchar(20) NOT NULL COMMENT '唯一参考序号',
  `n_exam_id` int(11) NOT NULL DEFAULT '0' COMMENT '试卷ID',
  `n_user_id` int(11) DEFAULT NULL COMMENT '用户ID',
  `dt_start` int(10) DEFAULT NULL COMMENT '参考时间',
  `dt_end` int(10) NOT NULL COMMENT '结束考试时间',
  `f_score` decimal(5,2) DEFAULT NULL COMMENT '得分',
  `n_verifyed` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否阅卷',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8 COMMENT='用户参考信息';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_user_examed`
--

LOCK TABLES `tb_user_examed` WRITE;
/*!40000 ALTER TABLE `tb_user_examed` DISABLE KEYS */;
INSERT INTO `tb_user_examed` VALUES (60,'b723c720421401804075',1,18,1401804045,1401804075,1.00,1),(61,'f5ebaa7f9e1401892749',1,1,1401892651,1401892749,0.00,0),(62,'26d4306c761401892865',1,1,1401892824,1401892865,0.00,1);
/*!40000 ALTER TABLE `tb_user_examed` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-06-07 11:06:00
