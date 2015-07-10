-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 21-Maio-2015 às 00:43
-- Versão do servidor: 5.6.21
-- PHP Version: 5.6.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `gamepesca`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `gamepesca`
--

CREATE TABLE IF NOT EXISTS `gamepesca` (
`id` bigint(20) NOT NULL,
  `mensagem` varchar(600) NOT NULL,
  `palavras` varchar(300) NOT NULL,
  `palpesca` varchar(300) NOT NULL,
  `score` int(100) NOT NULL,
  `nivel` int(100) NOT NULL,
  `imagem` varchar(300) NOT NULL,
  `tempo` int(50) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `gamepesca`
--

INSERT INTO `gamepesca` (`id`, `mensagem`, `palavras`, `palpesca`, `score`, `nivel`, `imagem`, `tempo`) VALUES
(26, 'pesque algo', 'gato', 'gato,mesa', 0, 0, 'upload/', 1200),
(27, 'Pesque uma palavra que corresponde com um veiculo', 'boi', 'b,o,i', 0, 0, 'upload/', 1200),
(28, 'mesa', 'mesa', 'm,e,s,a', 0, 0, 'upload/', 1200);

-- --------------------------------------------------------

--
-- Estrutura da tabela `user`
--

CREATE TABLE IF NOT EXISTS `user` (
`id` bigint(20) NOT NULL,
  `user` varchar(500) NOT NULL,
  `score` int(11) NOT NULL,
  `nivelGamepesca` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `user`
--

INSERT INTO `user` (`id`, `user`, `score`, `nivelGamepesca`) VALUES
(1, '01', 4, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `gamepesca`
--
ALTER TABLE `gamepesca`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
 ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `gamepesca`
--
ALTER TABLE `gamepesca`
MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=29;
--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
