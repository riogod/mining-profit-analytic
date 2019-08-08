-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Авг 07 2019 г., 02:50
-- Версия сервера: 10.2.7-MariaDB
-- Версия PHP: 5.6.32

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `miningstat`
--

-- --------------------------------------------------------

--
-- Структура таблицы `coins`
--

CREATE TABLE `coins` (
  `id` int(11) NOT NULL,
  `coin_name` varchar(100) NOT NULL,
  `pool_name` varchar(100) NOT NULL,
  `algo` varchar(100) NOT NULL,
  `symbol` varchar(10) NOT NULL,
  `date_add` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `coin_pricehistory`
--

CREATE TABLE `coin_pricehistory` (
  `symbol` varchar(10) NOT NULL,
  `last` decimal(20,10) NOT NULL,
  `volume` decimal(20,10) NOT NULL,
  `ask` decimal(20,10) NOT NULL,
  `bid` decimal(20,10) NOT NULL,
  `exchange` varchar(50) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `coin_prices`
--

CREATE TABLE `coin_prices` (
  `id` int(11) NOT NULL,
  `coin_name` varchar(50) NOT NULL,
  `symbol` varchar(50) NOT NULL,
  `price_btc` double(15,8) DEFAULT NULL,
  `price_usd` double(15,8) DEFAULT NULL,
  `reward_per_b` double(15,8) DEFAULT NULL,
  `not_in` int(1) DEFAULT NULL,
  `no_reward` int(1) DEFAULT NULL,
  `algo` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `pools_stat`
--

CREATE TABLE `pools_stat` (
  `coin_name` varchar(50) NOT NULL,
  `symbol` varchar(20) NOT NULL,
  `workers` int(11) NOT NULL,
  `shares` bigint(20) NOT NULL,
  `hashrate` bigint(50) NOT NULL,
  `estimate` double(15,10) NOT NULL,
  `24h_blocks` int(11) NOT NULL,
  `timesincelast` int(11) NOT NULL,
  `pool` varchar(50) NOT NULL,
  `dateadd` datetime NOT NULL DEFAULT current_timestamp(),
  `algo` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `coins`
--
ALTER TABLE `coins`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `coin_pricehistory`
--
ALTER TABLE `coin_pricehistory`
  ADD KEY `symbol` (`symbol`);

--
-- Индексы таблицы `coin_prices`
--
ALTER TABLE `coin_prices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `algo` (`algo`),
  ADD KEY `symbol` (`symbol`) USING BTREE;

--
-- Индексы таблицы `pools_stat`
--
ALTER TABLE `pools_stat`
  ADD KEY `symbol` (`symbol`),
  ADD KEY `algo` (`algo`),
  ADD KEY `dateadd` (`dateadd`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `coins`
--
ALTER TABLE `coins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1330;

--
-- AUTO_INCREMENT для таблицы `coin_prices`
--
ALTER TABLE `coin_prices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=342;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
