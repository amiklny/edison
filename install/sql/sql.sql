-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Авг 28 2021 г., 10:04
-- Версия сервера: 5.7.21-20-beget-5.7.21-20-1-log
-- Версия PHP: 5.6.40

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `arnly_edison`
--

-- --------------------------------------------------------

--
-- Структура таблицы `answers`
--
-- Создание: Авг 28 2021 г., 05:42
-- Последнее обновление: Авг 28 2021 г., 07:03
--

DROP TABLE IF EXISTS `answers`;
CREATE TABLE `answers` (
  `ID` int(11) NOT NULL,
  `USER_ID` int(11) NOT NULL,
  `NUMBER` int(11) NOT NULL,
  `HISTORY_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `authority`
--
-- Создание: Авг 28 2021 г., 05:42
-- Последнее обновление: Авг 28 2021 г., 07:03
--

DROP TABLE IF EXISTS `authority`;
CREATE TABLE `authority` (
  `ID` int(11) NOT NULL,
  `USER_ID` int(11) NOT NULL,
  `CURRENT_AUTORITY` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `history`
--
-- Создание: Авг 28 2021 г., 05:42
-- Последнее обновление: Авг 28 2021 г., 07:03
--

DROP TABLE IF EXISTS `history`;
CREATE TABLE `history` (
  `ID` int(11) NOT NULL,
  `USER_ID` int(11) NOT NULL,
  `NUMBER` int(11) NOT NULL,
  `CLOSED` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--
-- Создание: Авг 28 2021 г., 05:42
-- Последнее обновление: Авг 28 2021 г., 07:03
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `ID` int(11) NOT NULL,
  `USER_SESSION` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `answers`
--
ALTER TABLE `answers`
  ADD PRIMARY KEY (`ID`);

--
-- Индексы таблицы `authority`
--
ALTER TABLE `authority`
  ADD PRIMARY KEY (`ID`);

--
-- Индексы таблицы `history`
--
ALTER TABLE `history`
  ADD PRIMARY KEY (`ID`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `answers`
--
ALTER TABLE `answers`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90;

--
-- AUTO_INCREMENT для таблицы `authority`
--
ALTER TABLE `authority`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT для таблицы `history`
--
ALTER TABLE `history`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=443;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
