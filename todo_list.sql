-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th4 08, 2025 lúc 09:54 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `todo_list`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Learning'),
(2, 'Personal'),
(3, 'Shopping');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tasks`
--

CREATE TABLE `tasks` (
  `task_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `status` enum('Pending','Completed') DEFAULT 'Pending',
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `tasks`
--

INSERT INTO `tasks` (`task_id`, `title`, `description`, `status`, `start_time`, `end_time`, `user_id`, `category_id`, `created_at`, `updated_at`) VALUES
(14, 'Làm Project Nâng cao', 'Lab 1, Lab 2', 'Completed', '2025-04-07 14:04:30', '2025-04-07 16:04:42', 1, 1, '2025-03-30 02:36:14', '2025-04-08 07:35:38'),
(18, 'Đi ngủ', 'ngủ sớm trước 10h', 'Pending', NULL, NULL, 1, 2, '2025-04-01 08:36:02', '2025-04-01 08:36:02'),
(19, 'Mua thức ăn', 'Milk, bread, eggs', 'Pending', NULL, NULL, 1, 3, '2025-04-01 14:14:49', '2025-04-01 14:14:49'),
(21, 'Dự án tốt nghiệp', 'Quản lí trường học', 'Pending', NULL, NULL, 1, 3, '2025-04-01 15:27:27', '2025-04-04 04:13:37'),
(22, 'Buy groceries', '123', 'Pending', NULL, NULL, 1, 2, '2025-04-01 15:51:11', '2025-04-01 15:51:11'),
(23, 'Đi học sớm', 'Đến trường sớm 15p', 'Completed', NULL, NULL, 1, 1, '2025-04-01 16:08:04', '2025-04-08 07:41:55'),
(25, 'Uống thuốc', 'ho', 'Pending', NULL, NULL, 1, 2, '2025-04-01 16:15:11', '2025-04-01 16:15:11'),
(29, 'Ngủ 8h', 'tốt cho sức khỏe', 'Pending', NULL, NULL, 1, 2, '2025-04-04 03:14:21', '2025-04-04 03:14:21'),
(32, 'Nấu ăn', 'Canh chua cá lóc', 'Pending', NULL, NULL, 1, 2, '2025-04-07 08:47:40', '2025-04-07 08:47:40'),
(34, 'Debug', 'on tap debug', 'Pending', '2025-04-07 23:17:00', '2025-04-08 23:15:00', 1, 1, '2025-04-07 16:15:48', '2025-04-07 16:15:48'),
(35, '08/04/2025', 'hoom nayt', 'Pending', '2025-04-08 14:50:00', '2025-04-08 14:55:00', 1, 1, '2025-04-08 07:41:09', '2025-04-08 07:41:09');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `created_at`) VALUES
(1, 'admin', 'admin@example.com', '1', '2025-03-21 14:48:05'),
(2, 'user1', 'user1@example.com', '2', '2025-03-21 14:48:05');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`task_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT cho bảng `tasks`
--
ALTER TABLE `tasks`
  MODIFY `task_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `tasks_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
