-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 18, 2017 at 09:46 PM
-- Server version: 5.5.54-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `biocorepipe`
--

-- --------------------------------------------------------

--
-- Table structure for table `biocorepipe_save`
--

CREATE DATABASE biocorepipe;
USE biocorepipe;

CREATE TABLE IF NOT EXISTS `biocorepipe_save` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` text NOT NULL,
  `edges` text NOT NULL,
  `mainG` text NOT NULL,
  `nodes` text NOT NULL,
  `name` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=52 ;

--
-- Dumping data for table `biocorepipe_save`
--

INSERT INTO `biocorepipe_save` (`id`, `user`, `edges`, `mainG`, `nodes`, `name`) VALUES
(26, 'docker', '{''edges'':["o-10-0-10-0_i-11-1-10-1"]}', '{''mainG'':[0,0,1]}', '{"g-0":[194.66665649414,98.666664123535,"10","Build Index"],"g-1":[434.66668701172,101.66666412354,"11","Map"]}', 'sss'),
(43, 'docker', '{''edges'':["o-10-0-10-0_i-11-1-10-1","o-10-0-10-0_i-11-1-10-2","o-11-1-11-1_i-11-2-11-2","o-11-0-13-1_i-12-0-13-3"]}', '{''mainG'':[-45,182,1]}', '{"g-0":[194.66665649414,98.666664123535,"10","Build Index"],"g-1":[461.66668701172,130.66665649414,"11","Map"],"g-2":[683.66668701172,-31.333343505859,"11","Map"],"g-3":[929.66668701172,53.66667175293,"12","Make_Transcript"]}', 'new5'),
(44, 'docker', '{''edges'':["o-10-0-10-5_i-11-1-10-4","o-11-1-11-3_i-11-2-11-4","o-11-1-11-4_i-18-0-11-6","o-10-0-10-0_i-11-1-10-3","o-18-0-13-6_i-16-0-13-7","o-16-0-13-7_i-12-0-13-1","o-18-0-13-6_i-19-0-13-8"]}', '{''mainG'':[2.6866507530212,55.04264831543,0.64685297012329]}', '{"g-0":[138.90986633301,328.63970947266,"10","Build_Index_rRNA"],"g-1":[1439.0686035156,136.75831604004,"12","Make_Transcript"],"g-3":[429.66665649414,332.66665649414,"11","rRNA_Map"],"g-4":[637.70208740234,99.412544250488,"11","tRNA_Map"],"g-5":[273.52389526367,93.884727478027,"10","Build_Index_tRNA"],"g-6":[922.32873535156,132.94046020508,"18","tophat_align"],"g-7":[1182.6651611328,136.43853759766,"16","samtools_sort"],"g-8":[1284.9837646484,333.58999633789,"19","RSeQC"]}', 'tophat_pipeline'),
(45, 'docker', '{''edges'':["o-10-0-10-0_i-11-1-10-3","o-10-0-10-5_i-11-1-10-4","o-11-1-11-3_i-11-2-11-4","o-11-1-11-4_i-18-0-11-6","o-18-0-13-6_i-16-0-13-7","o-16-0-13-7_i-12-0-13-1"]}', '{''mainG'':[191.91102600098,93.744384765625,0.34615948796272]}', '{"g-0":[165.93701171875,359.98693847656,"10","Build_Index_rRNA"],"g-1":[1351.3243408203,238.73760986328,"12","Make_Transcript"],"g-3":[429.66665649414,359.86514282227,"11","rRNA_Map"],"g-4":[651.00604248047,126.07795715332,"11","tRNA_Map"],"g-5":[302.14791870117,198.26303100586,"10","Build_Index_tRNA"],"g-6":[892.80377197266,149.03009033203,"18","tophat_align"],"g-7":[1123.3330078125,237.12690734863,"16","samtools_sort"]}', 'tophat_pipeline_V2'),
(51, 'docker', '{''edges'':["o-10-0-10-0_i-11-1-10-1","o-11-1-11-1_i-11-2-11-2","o-11-0-13-1_i-16-0-13-3","o-11-0-13-2_i-16-0-13-4","o-10-0-10-5_i-11-1-10-2"]}', '{''mainG'':[19.99976348877,5.2368230819702,0.95000040531158]}', '{"g-0":[205.66667175293,166.66667175293,"10","Build_Index_rRNA"],"g-1":[482.35092163086,243.29827880859,"11","rRNA_Map"],"g-2":[808.66668701172,424.66668701172,"11","tRNA_Map"],"g-3":[783.66668701172,134.66665649414,"16","samtools_sort_rRNA"],"g-4":[1096.6667480469,309.66668701172,"16","samtools_sort_tRNA"],"g-5":[492.50872802734,486.5087890625,"10","Build_Index_tRNA"]}', 'my_sequential');

-- --------------------------------------------------------

--
-- Table structure for table `matchid`
--

CREATE TABLE IF NOT EXISTS `matchid` (
  `id1` int(11) NOT NULL,
  `id2` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `matchid`
--

INSERT INTO `matchid` (`id1`, `id2`) VALUES
(42, 39),
(43, 40),
(49, 39),
(50, 40),
(51, 46),
(57, 54),
(58, 55),
(80, 61),
(85, 60),
(90, 81);

-- --------------------------------------------------------

--
-- Table structure for table `parameter`
--

CREATE TABLE IF NOT EXISTS `parameter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(256) NOT NULL,
  `channel_name` varchar(256) DEFAULT NULL,
  `file_type` varchar(256) DEFAULT NULL,
  `file_path` varchar(256) DEFAULT NULL,
  `version` varchar(256) DEFAULT NULL,
  `qualifier` varchar(256) DEFAULT NULL,
  `input_text` text,
  `date_created` datetime DEFAULT NULL,
  `date_modified` datetime DEFAULT NULL,
  `last_modified_user` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

--
-- Dumping data for table `parameter`
--

INSERT INTO `parameter` (`id`, `name`, `channel_name`, `file_type`, `file_path`, `version`, `qualifier`, `input_text`, `date_created`, `date_modified`, `last_modified_user`) VALUES
(9, 'genome', 'genome_file', 'fasta', '"$baseDir/data/ggal/ggal_1_48850000_49020000.Ggal71.500bpflank.fa"', '1', 'file', '', '2017-04-23 22:07:04', '2017-04-23 22:07:04', 'docker'),
(10, 'genome_index', 'genome_index', 'index', '', '1', 'file', '', '2017-04-23 22:10:53', '2017-04-23 22:10:53', 'docker'),
(11, 'read_pairs', 'read_pairs', 'fq', '"$baseDir/data/ggal/*_{1,2}.fq"', '1', 'set', '', '2017-04-23 22:13:42', '2017-04-23 22:13:42', 'docker'),
(13, 'mapped_read_pairs', 'bam_files', 'bam', '', '1', 'set', '', '2017-04-23 22:31:59', '2017-04-23 22:31:59', 'docker'),
(14, 'transcripts', 'transcripts', 'gtf', '', '1', 'set', '', '2017-04-23 22:34:26', '2017-04-23 22:34:26', 'root'),
(15, 'RSeQC', 'RSeQC', 'tsv', '', '1.0.0', 'file', '', '2017-07-12 03:36:42', '2017-07-12 03:36:42', 'root'),
(16, 'inputparam', '', 'NA', '', '', '', '', '2017-07-12 03:36:42', '2017-07-12 03:36:42', 'root');
(17, 'outputparam', '', 'NA', '', '', '', '', '2017-07-12 03:36:42', '2017-07-12 03:36:42', 'root');

-- --------------------------------------------------------

--
-- Table structure for table `pipeline`
--

CREATE TABLE IF NOT EXISTS `pipeline` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(256) NOT NULL,
  `version` varchar(256) NOT NULL,
  `date_created` datetime DEFAULT NULL,
  `date_modified` datetime DEFAULT NULL,
  `last_modified_user` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=22 ;

--
-- Dumping data for table `pipeline`
--

INSERT INTO `pipeline` (`id`, `name`, `version`, `date_created`, `date_modified`, `last_modified_user`) VALUES
(20, 'RNA-Seq', 'v0.0.1', '2017-05-18 21:30:40', '2017-05-18 21:30:40', 'root'),
(21, 'Tophat', 'v1.0.0', '2017-05-19 18:48:51', '2017-05-19 18:48:51', 'root');

-- --------------------------------------------------------

--
-- Table structure for table `pipeline_process`
--

CREATE TABLE IF NOT EXISTS `pipeline_process` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `process_id` int(11) NOT NULL,
  `pipeline_id` int(11) NOT NULL,
  `name` varchar(256) NOT NULL,
  `date_created` datetime DEFAULT NULL,
  `date_modified` datetime DEFAULT NULL,
  `last_modified_user` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `process_id` (`process_id`),
  KEY `pipeline_id` (`pipeline_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=68 ;

--
-- Dumping data for table `pipeline_process`
--

INSERT INTO `pipeline_process` (`id`, `process_id`, `pipeline_id`, `name`, `date_created`, `date_modified`, `last_modified_user`) VALUES
(57, 10, 20, 'new_name', '2017-05-18 21:38:01', '2017-05-18 21:38:01', 'root'),
(58, 11, 20, 'new_mapping', '2017-05-18 21:38:36', '2017-05-18 21:38:36', 'root'),
(59, 11, 20, 'rRNA', '2017-05-18 21:40:04', '2017-05-18 21:40:04', 'root'),
(60, 10, 21, 'new_build', '2017-05-19 18:49:24', '2017-05-19 18:49:24', 'root'),
(61, 11, 21, 'rRNA', '2017-05-19 18:52:45', '2017-05-19 18:52:45', 'root'),
(64, 11, 21, 'tRNA', '2017-05-19 18:56:03', '2017-05-19 18:56:03', 'root'),
(67, 19, 21, 'RSeQC_tRNA', '2017-07-12 03:58:13', '2017-07-12 03:58:13', 'root');

-- --------------------------------------------------------

--
-- Table structure for table `pipeline_process_parameter`
--

CREATE TABLE IF NOT EXISTS `pipeline_process_parameter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `process_id` int(11) NOT NULL,
  `pipeline_id` int(11) NOT NULL,
  `parameter_id` int(11) NOT NULL,
  `name` varchar(256) NOT NULL,
  `process_name` varchar(256) NOT NULL,
  `date_created` datetime DEFAULT NULL,
  `date_modified` datetime DEFAULT NULL,
  `last_modified_user` varchar(45) DEFAULT NULL,
  `type` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `process_id` (`process_id`),
  KEY `pipeline_id` (`pipeline_id`),
  KEY `parameter_id` (`parameter_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=92 ;

--
-- Dumping data for table `pipeline_process_parameter`
--

INSERT INTO `pipeline_process_parameter` (`id`, `process_id`, `pipeline_id`, `parameter_id`, `name`, `process_name`, `date_created`, `date_modified`, `last_modified_user`, `type`) VALUES
(39, 10, 20, 9, 'genome_file', 'new_name', '2017-05-18 21:38:01', '2017-05-18 21:38:01', 'root', 'input'),
(40, 10, 20, 10, 'new_gen', 'new_name', '2017-05-18 21:38:01', '2017-05-18 21:38:01', 'root', 'output'),
(42, 11, 20, 9, 'genome_file', 'new_mapping', '2017-05-18 21:38:36', '2017-05-18 21:38:36', 'root', 'input'),
(43, 11, 20, 10, 'new_gen', 'new_mapping', '2017-05-18 21:38:36', '2017-05-18 21:38:36', 'root', 'input'),
(44, 11, 20, 11, 'new_mapping', 'new_mapping', '2017-05-18 21:38:36', '2017-05-18 21:38:36', 'root', 'input'),
(45, 11, 20, 13, 'new_mapping', 'new_mapping', '2017-05-18 21:38:36', '2017-05-18 21:38:36', 'root', 'output'),
(46, 11, 20, 11, 'new_name', 'new_mapping', '2017-05-18 21:38:36', '2017-05-18 21:38:36', 'root', 'output'),
(49, 11, 20, 9, 'genome_file', 'rRNA', '2017-05-18 21:40:04', '2017-05-18 21:40:04', 'root', 'input'),
(50, 11, 20, 10, 'new_gen', 'rRNA', '2017-05-18 21:40:04', '2017-05-18 21:40:04', 'root', 'input'),
(51, 11, 20, 11, 'new_name', 'rRNA', '2017-05-18 21:40:04', '2017-05-18 21:40:04', 'root', 'input'),
(52, 11, 20, 13, 'rRNA', 'rRNA', '2017-05-18 21:40:04', '2017-05-18 21:40:04', 'root', 'output'),
(53, 11, 20, 11, 'rRNA', 'rRNA', '2017-05-18 21:40:04', '2017-05-18 21:40:04', 'root', 'output'),
(54, 10, 21, 9, 'new_name2', 'new_build', '2017-05-19 18:49:24', '2017-05-19 18:49:24', 'root', 'input'),
(55, 10, 21, 10, 'new_name3', 'new_build', '2017-05-19 18:49:24', '2017-05-19 18:49:24', 'root', 'output'),
(57, 11, 21, 9, 'new_name2', 'rRNA', '2017-05-19 18:52:45', '2017-05-19 18:52:45', 'root', 'input'),
(58, 11, 21, 10, 'new_name3', 'rRNA', '2017-05-19 18:52:45', '2017-05-19 18:52:45', 'root', 'input'),
(59, 11, 21, 11, 'rRNA', 'rRNA', '2017-05-19 18:52:45', '2017-05-19 18:52:45', 'root', 'input'),
(60, 11, 21, 13, 'out_bam', 'rRNA', '2017-05-19 18:52:45', '2017-05-19 18:52:45', 'root', 'output'),
(61, 11, 21, 11, 'filtered', 'rRNA', '2017-05-19 18:52:45', '2017-05-19 18:52:45', 'root', 'output'),
(78, 11, 21, 9, 'tRNA', 'tRNA', '2017-05-19 18:56:03', '2017-05-19 18:56:03', 'root', 'input'),
(79, 11, 21, 10, 'tRNA', 'tRNA', '2017-05-19 18:56:03', '2017-05-19 18:56:03', 'root', 'input'),
(80, 11, 21, 11, 'filtered', 'tRNA', '2017-05-19 18:56:03', '2017-05-19 18:56:03', 'root', 'input'),
(81, 11, 21, 13, 'RSEQC_results', 'tRNA', '2017-05-19 18:56:03', '2017-05-19 18:56:03', 'root', 'output'),
(82, 11, 21, 11, 'tRNA', 'tRNA', '2017-05-19 18:56:03', '2017-05-19 18:56:03', 'root', 'output'),
(85, 16, 21, 13, 'out_bam', '', '2017-05-22 16:57:00', '2017-05-22 16:57:00', 'root', 'input'),
(86, 16, 21, 13, '', '', '2017-05-22 16:57:00', '2017-05-22 16:57:00', 'root', 'output'),
(90, 19, 21, 13, 'RSEQC_results', 'RSeQC_tRNA', '2017-07-12 03:58:13', '2017-07-12 03:58:13', 'root', 'input'),
(91, 19, 21, 15, 'RSeQC_tRNA', 'RSeQC_tRNA', '2017-07-12 03:58:13', '2017-07-12 03:58:13', 'root', 'output');

-- --------------------------------------------------------

--
-- Table structure for table `process`
--

CREATE TABLE IF NOT EXISTS `process` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `process_group_id` int(11) NOT NULL,
  `name` varchar(256) NOT NULL,
  `version` varchar(256) DEFAULT NULL,
  `summary` varchar(255) NOT NULL,
  `script` text,
  `date_created` datetime DEFAULT NULL,
  `date_modified` datetime DEFAULT NULL,
  `last_modified_user` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=20 ;

--
-- Dumping data for table `process`
--

INSERT INTO `process` (`id`, `process_group_id`, `name`, `version`, `summary`, `script`, `date_created`, `date_modified`, `last_modified_user`) VALUES
(10, 2, 'Build_Index', '1', '', 'bowtie2-build ${genome} genome.index', '2017-04-23 22:36:16', '2017-04-23 22:36:16', 'root'),
(11, 3, 'Map_Tophat2', '0.0.1', 'TopHat is a program that aligns RNA-Seq reads to a genome in order to identify exon-exon splice junctions. It is built on the ultrafast short read mapping program Bowtie. TopHat runs on Linux and OS X.', 'tophat2 genome.index $reads', '2017-04-23 22:37:23', '2017-04-23 22:37:23', 'docker'),
(12, 4, 'Make_Transcript', '1', 'Cufflinks assembles transcripts, estimates their abundances, and tests for differential expression and regulation in RNA-Seq samples. It accepts aligned RNA-Seq reads and assembles the alignments into a parsimonious set of transcripts.', 'cufflinks ${bam_file}', '2017-04-23 22:38:04', '2017-04-23 22:38:04', 'root'),
(16, 5, 'samtools_sort', '0.1.19', 'Samtools sort functionality.', 'samtools sort ${initial_alignment} ${sorted_bam}\r\n', '2017-04-28 18:40:07', '2017-04-28 18:40:07', 'root'),
(17, 6, 'splitSequences', '0.0.1', 'Module to split your fastq files. \nYou can specify the number of reads per file.', '', '2017-05-04 19:26:30', '2017-05-04 19:26:30', 'docker'),
(18, 3, 'Map_Bowtie', '0.0.1', '', 'bowtie2 -x ${genome.index} -1 $forward -2 $reverse -S ${dataset_id}_alignment.sam', '2017-05-08 15:08:51', '2017-05-08 15:08:51', 'root'),
(19, 7, 'RSeQC', '1.0.0', '', 'rseqc ${bam_file}', '2017-07-12 03:35:23', '2017-07-12 03:35:23', 'root');

-- --------------------------------------------------------

--
-- Table structure for table `process_group`
--

CREATE TABLE IF NOT EXISTS `process_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `process_group`
--

INSERT INTO `process_group` (`id`, `group_name`) VALUES
(1, 'NF_pipelines'),
(2, 'Index'),
(3, 'Alignment'),
(4, 'Transcript'),
(5, 'Samtools'),
(6, 'Misc.'),
(7, 'QC');

-- --------------------------------------------------------

--
-- Table structure for table `process_parameter`
--

CREATE TABLE IF NOT EXISTS `process_parameter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `process_id` int(11) NOT NULL,
  `parameter_id` int(11) NOT NULL,
  `type` varchar(10) NOT NULL,
  `name` varchar(256) NOT NULL,
  `date_created` datetime DEFAULT NULL,
  `date_modified` datetime DEFAULT NULL,
  `last_modified_user` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `process_id` (`process_id`),
  KEY `parameter_id` (`parameter_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=44 ;

--
-- Dumping data for table `process_parameter`
--

INSERT INTO `process_parameter` (`id`, `process_id`, `parameter_id`, `type`, `name`, `date_created`, `date_modified`, `last_modified_user`) VALUES
(19, 10, 9, 'input', 'genome', '2017-04-23 22:39:12', '2017-04-23 22:39:12', 'docker'),
(20, 10, 10, 'output', '''genome.index*''', '2017-04-23 22:39:53', '2017-04-23 22:39:53', 'docker'),
(21, 11, 9, 'input', 'genome', '2017-04-23 22:40:57', '2017-04-23 22:40:57', 'docker'),
(22, 11, 10, 'input', 'index', '2017-04-23 22:41:37', '2017-04-23 22:41:37', 'docker'),
(23, 11, 11, 'input', 'file(reads)', '2017-04-23 22:42:54', '2017-04-23 22:42:54', 'docker'),
(24, 11, 13, 'output', '"tophat_out/accepted_hits.bam"', '2017-04-23 22:47:36', '2017-04-23 22:47:36', 'docker'),
(25, 12, 13, 'input', 'bam_file', '2017-04-23 22:49:05', '2017-04-23 22:49:05', 'docker'),
(26, 12, 14, 'output', '''transcripts.gtf''', '2017-04-23 22:49:39', '2017-04-23 22:49:39', 'docker'),
(29, 16, 13, 'input', 'initial_alignment', '2017-04-28 18:41:22', '2017-04-28 18:41:22', 'docker'),
(35, 18, 11, 'input', 'dd', '2017-05-08 15:09:16', '2017-05-08 15:09:16', 'root'),
(36, 17, 11, 'input', 'initial_seq', '2017-05-08 15:10:33', '2017-05-08 15:10:33', 'root'),
(37, 17, 11, 'output', 'splited_seq', '2017-05-08 15:10:50', '2017-05-08 15:10:50', 'root'),
(40, 16, 13, 'output', 'sorted_bam', '2017-07-11 00:50:05', '2017-07-11 00:50:05', 'root'),
(41, 18, 13, 'output', 'tophat_bam', '2017-07-11 00:51:50', '2017-07-11 00:51:50', 'root'),
(42, 19, 13, 'input', 'bam', '2017-07-12 03:35:46', '2017-07-12 03:35:46', 'root'),
(43, 19, 15, 'output', 'RSeQC_table', '2017-07-12 03:37:26', '2017-07-12 03:37:26', 'root');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `pipeline_process`
--
ALTER TABLE `pipeline_process`
  ADD CONSTRAINT `pipeline_process_ibfk_1` FOREIGN KEY (`process_id`) REFERENCES `process` (`id`),
  ADD CONSTRAINT `pipeline_process_ibfk_2` FOREIGN KEY (`pipeline_id`) REFERENCES `pipeline` (`id`);

--
-- Constraints for table `pipeline_process_parameter`
--
ALTER TABLE `pipeline_process_parameter`
  ADD CONSTRAINT `pipeline_process_parameter_ibfk_1` FOREIGN KEY (`process_id`) REFERENCES `process` (`id`),
  ADD CONSTRAINT `pipeline_process_parameter_ibfk_2` FOREIGN KEY (`pipeline_id`) REFERENCES `pipeline` (`id`),
  ADD CONSTRAINT `pipeline_process_parameter_ibfk_3` FOREIGN KEY (`parameter_id`) REFERENCES `parameter` (`id`);

--
-- Constraints for table `process_parameter`
--
ALTER TABLE `process_parameter`
  ADD CONSTRAINT `process_parameter_ibfk_1` FOREIGN KEY (`process_id`) REFERENCES `process` (`id`),
  ADD CONSTRAINT `process_parameter_ibfk_2` FOREIGN KEY (`parameter_id`) REFERENCES `parameter` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
