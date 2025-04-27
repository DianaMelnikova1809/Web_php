-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Хост: mysql
-- Время создания: Апр 27 2025 г., 18:36
-- Версия сервера: 8.0.41
-- Версия PHP: 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `Бассейн`
--

-- --------------------------------------------------------

--
-- Структура таблицы `temp_notifications`
--

CREATE TABLE `temp_notifications` (
  `id` int NOT NULL,
  `title` varchar(100) NOT NULL,
  `message` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MEMORY DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `Бассейны`
--

CREATE TABLE `Бассейны` (
  `Код_бассейна` int NOT NULL,
  `Адрес_бассейна` varchar(100) NOT NULL,
  `Телефон_администратора` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `Бассейны`
--

INSERT INTO `Бассейны` (`Код_бассейна`, `Адрес_бассейна`, `Телефон_администратора`) VALUES
(1, 'ул. Водная, 15', '+7 (495) 111-22-33'),
(2, 'пр. Плавательный, 42', '+7 (495) 222-33-44'),
(3, 'ул. Береговая, 7', '+7 (495) 333-44-55');

-- --------------------------------------------------------

--
-- Структура таблицы `Группы`
--

CREATE TABLE `Группы` (
  `Код_группы` int NOT NULL,
  `Количество_человек` int DEFAULT NULL,
  `Код_бассейна` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `Группы`
--

INSERT INTO `Группы` (`Код_группы`, `Количество_человек`, `Код_бассейна`) VALUES
(1, 10, 1),
(2, 8, 1),
(3, 12, 1),
(4, 15, 1),
(5, 10, 2),
(6, 13, 2),
(7, 9, 2),
(8, 18, 2),
(9, 11, 3),
(10, 20, 3),
(11, 11, 3),
(12, 14, 3);

-- --------------------------------------------------------

--
-- Структура таблицы `Представители`
--

CREATE TABLE `Представители` (
  `Код_представителя` int NOT NULL,
  `Фамилия` varchar(50) NOT NULL,
  `Имя` varchar(50) NOT NULL,
  `Отчество` varchar(50) DEFAULT NULL,
  `Телефон` varchar(20) DEFAULT NULL,
  `Адрес_проживания` varchar(100) DEFAULT NULL,
  `Электронная_почта` varchar(255) NOT NULL,
  `Логин` varchar(50) NOT NULL,
  `Пароль` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `Представители`
--

INSERT INTO `Представители` (`Код_представителя`, `Фамилия`, `Имя`, `Отчество`, `Телефон`, `Адрес_проживания`, `Электронная_почта`, `Логин`, `Пароль`) VALUES
(1, 'Петрова', 'Елена', 'Викторовна', '+7 (900) 444-44-44', 'ул. Юных пловцов, 3', 'petrova@example.com', 'petrova', 'petrova'),
(2, 'Сидоров', 'Алексей', 'Петрович', '+7 (900) 555-55-55', 'ул. Водников, 12', 'sidorov@example.com', 'sidorov', 'sidorov'),
(3, 'Соколова', 'Наталья', 'Ивановна', '+7 (901) 111-11-11', 'ул. Лесная, 5', 'sokolova@example.com', 'sokolova', 'sokolova'),
(4, 'Морозова', 'Анна', 'Анатольевна', '+7 (902) 111-11-11', 'ул. Солнечная, 14', 'morozova@example.com', 'morozova', 'morozova'),
(5, 'Белова', 'Надежда', 'Николаевна', '+7 (903) 111-11-11', 'ул. Полевая, 21', 'belova@example.com', 'belova', 'belova'),
(6, 'Серова', 'Мария', 'Андреевна', '+7(999)8887766', 'пр. Говорова 56', 'io@mail.ru', 'CAM', 'cam');

-- --------------------------------------------------------

--
-- Структура таблицы `Спортсмены`
--

CREATE TABLE `Спортсмены` (
  `Код_спортсмена` int NOT NULL,
  `Фамилия` varchar(50) NOT NULL,
  `Имя` varchar(50) NOT NULL,
  `Отчество` varchar(50) DEFAULT NULL,
  `Дата_рождения` date NOT NULL,
  `Адрес_проживания` varchar(100) DEFAULT NULL,
  `Начало_действия` date DEFAULT NULL,
  `Конец_действия` date DEFAULT NULL,
  `Справка_зб` varchar(100) DEFAULT NULL,
  `Справка_от_педиатра` varchar(100) DEFAULT NULL,
  `Код_группы` int DEFAULT NULL,
  `Код_представителя` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `Спортсмены`
--

INSERT INTO `Спортсмены` (`Код_спортсмена`, `Фамилия`, `Имя`, `Отчество`, `Дата_рождения`, `Адрес_проживания`, `Начало_действия`, `Конец_действия`, `Справка_зб`, `Справка_от_педиатра`, `Код_группы`, `Код_представителя`) VALUES
(1, 'Петров', 'Алексей', 'Игоревич', '2010-05-15', 'ул. Юных пловцов, 3', '2025-09-01', '2026-08-31', 'Справка №123', 'Справка №456', 8, 1),
(2, 'Петрова', 'Мария', 'Игоревна', '2011-07-22', 'ул. Юных пловцов, 3', '2025-10-01', '2026-09-30', 'Справка №124', 'Справка №457', 1, 1),
(3, 'Сидорова', 'Ольга', 'Алексеевна', '2012-03-10', 'ул. Водников, 12', '2025-11-01', '2026-10-31', 'Справка №125', 'Справка №458', 1, 2),
(4, 'Соколов', 'Иван', 'Петрович', '2011-04-12', 'ул. Лесная, 5', '2025-09-15', '2026-09-14', 'Справка №129', 'Справка №462', 2, 3),
(5, 'Соколова', 'Екатерина', 'Петровна', '2010-09-25', 'ул. Лесная, 5', '2025-10-15', '2026-10-14', 'Справка №130', 'Справка №463', 2, 3),
(6, 'Морозов', 'Кирилл', 'Анатольевич', '2011-03-28', 'ул. Солнечная, 14', '2025-09-20', '2026-09-19', 'Справка №134', 'Справка №467', 3, 4),
(7, 'Белов', 'Андрей', 'Николаевич', '2010-08-17', 'ул. Полевая, 21', '2025-09-25', '2026-09-24', 'Справка №139', 'Справка №472', 4, 5),
(10, 'Петрова', 'Анна', NULL, '2013-09-02', NULL, NULL, NULL, NULL, NULL, 6, 1),
(11, 'Петрова', 'Мия', NULL, '2017-05-06', NULL, NULL, NULL, NULL, NULL, 11, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `Спортсмены_Тренировки`
--

CREATE TABLE `Спортсмены_Тренировки` (
  `Код_спортсмена` int NOT NULL,
  `Код_тренировки` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `Спортсмены_Тренировки`
--

INSERT INTO `Спортсмены_Тренировки` (`Код_спортсмена`, `Код_тренировки`) VALUES
(1, 1),
(2, 1),
(3, 1),
(1, 2),
(4, 3),
(5, 3),
(4, 4),
(1, 5),
(6, 5),
(10, 5),
(7, 7),
(1, 8),
(10, 8);

-- --------------------------------------------------------

--
-- Структура таблицы `Тренеры`
--

CREATE TABLE `Тренеры` (
  `Код_тренера` int NOT NULL,
  `Фамилия` varchar(50) NOT NULL,
  `Имя` varchar(50) NOT NULL,
  `Отчество` varchar(50) DEFAULT NULL,
  `Телефон` varchar(20) DEFAULT NULL,
  `Адрес_проживания` varchar(100) DEFAULT NULL,
  `Разряд` varchar(50) DEFAULT NULL,
  `Электронная_почта` varchar(255) NOT NULL,
  `Логин` varchar(50) NOT NULL,
  `Пароль` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `Тренеры`
--

INSERT INTO `Тренеры` (`Код_тренера`, `Фамилия`, `Имя`, `Отчество`, `Телефон`, `Адрес_проживания`, `Разряд`, `Электронная_почта`, `Логин`, `Пароль`) VALUES
(1, 'Иванов', 'Петр', 'Сергеевич', '+7 (900) 111-11-11', 'ул. Тренерская, 1', 'Мастер спорта', 'ivanov@example.com', 'ivanov', '$2y$10$ivanovhashedpassword'),
(2, 'Смирнова', 'Ольга', 'Ивановна', '+7 (900) 222-22-22', 'ул. Спортивная, 5', 'Кандидат в мастера спорта', 'smirnova@example.com', 'smirnova', '$2y$10$smirnovahashedpassword'),
(3, 'Кузнецов', 'Алексей', 'Дмитриевич', '+7 (900) 333-33-33', 'пр. Победы, 10', 'Мастер спорта международного класса', 'kuznetsov@example.com', 'kuznetsov', '$2y$10$kuznetsovhashedpassword');

-- --------------------------------------------------------

--
-- Структура таблицы `Тренировки`
--

CREATE TABLE `Тренировки` (
  `Код_тренировки` int NOT NULL,
  `Дата` date NOT NULL,
  `Время` time NOT NULL,
  `Код_группы` int DEFAULT NULL,
  `Код_тренера` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `Тренировки`
--

INSERT INTO `Тренировки` (`Код_тренировки`, `Дата`, `Время`, `Код_группы`, `Код_тренера`) VALUES
(1, '2025-11-15', '16:00:00', 1, 1),
(2, '2025-11-17', '16:00:00', 1, 1),
(3, '2025-11-16', '17:00:00', 2, 2),
(4, '2025-11-18', '17:00:00', 2, 2),
(5, '2025-11-17', '18:00:00', 3, 3),
(6, '2025-11-19', '18:00:00', 3, 3),
(7, '2025-11-18', '10:00:00', 4, 1),
(8, '2025-11-20', '10:00:00', 4, 1);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `temp_notifications`
--
ALTER TABLE `temp_notifications`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `Бассейны`
--
ALTER TABLE `Бассейны`
  ADD PRIMARY KEY (`Код_бассейна`);

--
-- Индексы таблицы `Группы`
--
ALTER TABLE `Группы`
  ADD PRIMARY KEY (`Код_группы`),
  ADD KEY `Код_бассейна` (`Код_бассейна`);

--
-- Индексы таблицы `Представители`
--
ALTER TABLE `Представители`
  ADD PRIMARY KEY (`Код_представителя`);

--
-- Индексы таблицы `Спортсмены`
--
ALTER TABLE `Спортсмены`
  ADD PRIMARY KEY (`Код_спортсмена`),
  ADD KEY `Код_группы` (`Код_группы`),
  ADD KEY `Код_представителя` (`Код_представителя`);

--
-- Индексы таблицы `Спортсмены_Тренировки`
--
ALTER TABLE `Спортсмены_Тренировки`
  ADD PRIMARY KEY (`Код_спортсмена`,`Код_тренировки`),
  ADD KEY `Спортсмены_Тренировки_ibfk_2` (`Код_тренировки`);

--
-- Индексы таблицы `Тренеры`
--
ALTER TABLE `Тренеры`
  ADD PRIMARY KEY (`Код_тренера`);

--
-- Индексы таблицы `Тренировки`
--
ALTER TABLE `Тренировки`
  ADD PRIMARY KEY (`Код_тренировки`),
  ADD KEY `Код_группы` (`Код_группы`),
  ADD KEY `Код_тренера` (`Код_тренера`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `temp_notifications`
--
ALTER TABLE `temp_notifications`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `Бассейны`
--
ALTER TABLE `Бассейны`
  MODIFY `Код_бассейна` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `Группы`
--
ALTER TABLE `Группы`
  MODIFY `Код_группы` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT для таблицы `Представители`
--
ALTER TABLE `Представители`
  MODIFY `Код_представителя` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблицы `Спортсмены`
--
ALTER TABLE `Спортсмены`
  MODIFY `Код_спортсмена` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT для таблицы `Тренеры`
--
ALTER TABLE `Тренеры`
  MODIFY `Код_тренера` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `Тренировки`
--
ALTER TABLE `Тренировки`
  MODIFY `Код_тренировки` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `Группы`
--
ALTER TABLE `Группы`
  ADD CONSTRAINT `Группы_ibfk_1` FOREIGN KEY (`Код_бассейна`) REFERENCES `Бассейны` (`Код_бассейна`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `Спортсмены`
--
ALTER TABLE `Спортсмены`
  ADD CONSTRAINT `Спортсмены_ibfk_1` FOREIGN KEY (`Код_группы`) REFERENCES `Группы` (`Код_группы`) ON DELETE SET NULL,
  ADD CONSTRAINT `Спортсмены_ibfk_2` FOREIGN KEY (`Код_представителя`) REFERENCES `Представители` (`Код_представителя`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `Спортсмены_Тренировки`
--
ALTER TABLE `Спортсмены_Тренировки`
  ADD CONSTRAINT `Спортсмены_Тренировки_ibfk_1` FOREIGN KEY (`Код_спортсмена`) REFERENCES `Спортсмены` (`Код_спортсмена`) ON DELETE CASCADE,
  ADD CONSTRAINT `Спортсмены_Тренировки_ibfk_2` FOREIGN KEY (`Код_тренировки`) REFERENCES `Тренировки` (`Код_тренировки`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `Тренировки`
--
ALTER TABLE `Тренировки`
  ADD CONSTRAINT `Тренировки_ibfk_1` FOREIGN KEY (`Код_группы`) REFERENCES `Группы` (`Код_группы`) ON DELETE SET NULL,
  ADD CONSTRAINT `Тренировки_ibfk_2` FOREIGN KEY (`Код_тренера`) REFERENCES `Тренеры` (`Код_тренера`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
