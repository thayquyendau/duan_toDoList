-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th4 18, 2025 lúc 05:18 PM
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
(15, 'Ngày mai');

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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `tasks`
--

INSERT INTO `tasks` (`task_id`, `title`, `description`, `status`, `start_time`, `end_time`, `user_id`, `category_id`, `created_at`, `updated_at`, `is_deleted`) VALUES
(57, 'Đi ngủ', 'Ngủ ở nhà', 'Pending', '2025-04-18 22:13:00', '2025-04-19 12:00:00', 4, 15, '2025-04-18 15:14:46', '2025-04-18 15:14:46', 0);

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
(4, '1', 'lehieuphuoc35205@gmail.com', '$2y$10$PKmyfXZEcJjjtPFQQuvvnOu6TCgwGT4pdoHNUYcxP4iLBJs25lvoy', '2025-04-18 13:24:33'),
(5, '2', 'anhanh2345@g.com', '$2y$10$8z/yJyf3D9bC/WV2dzLaveaeo2HzmtfVG4w0rtIHxt6wy6SSNhQ0O', '2025-04-18 13:36:06');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `user_actions`
--

CREATE TABLE `user_actions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `task_id` int(11) DEFAULT NULL,
  `timestamp` datetime DEFAULT current_timestamp(),
  `title` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `user_actions`
--

INSERT INTO `user_actions` (`id`, `user_id`, `action`, `task_id`, `timestamp`, `title`) VALUES
(77, 1, 'Đã xóa nhiệm vụ', 48, '2025-04-13 14:53:27', 'Test'),
(78, 1, 'Đã xóa nhiệm vụ', 47, '2025-04-13 15:09:09', 'Debug Knight2'),
(79, 1, 'Đã sửa nhiệm vụ', 45, '2025-04-13 15:10:40', 'Proteins'),
(80, 1, 'Đã khôi phục nhiệm vụ', 47, '2025-04-13 15:10:50', 'Debug Knight2'),
(81, 1, 'Đã khôi phục nhiệm vụ', 48, '2025-04-13 15:11:03', 'Test'),
(82, 1, 'Đã xóa nhiệm vụ', 47, '2025-04-13 15:11:09', 'Debug Knight2'),
(83, 1, 'Đã xóa nhiệm vụ', 48, '2025-04-13 15:11:26', 'Test'),
(84, 1, 'Đã sửa nhiệm vụ', 49, '2025-04-13 15:12:00', 'LOL'),
(85, 1, 'Đã xóa nhiệm vụ', 43, '2025-04-13 15:12:06', 'Buy groceri'),
(86, 1, 'Đã khôi phục nhiệm vụ', 48, '2025-04-13 15:12:21', 'Test'),
(87, 1, 'Đã xóa nhiệm vụ', 48, '2025-04-13 15:12:28', 'Test'),
(88, 1, 'Đã khôi phục nhiệm vụ', 47, '2025-04-13 15:20:42', 'Debug Knight2'),
(89, 1, 'Đã xóa nhiệm vụ', 47, '2025-04-13 15:20:45', 'Debug Knight2'),
(90, 1, 'Đã khôi phục nhiệm vụ', 43, '2025-04-13 15:20:49', 'Buy groceri'),
(91, 1, 'Đã khôi phục nhiệm vụ', 47, '2025-04-13 15:20:54', 'Debug Knight2'),
(92, 1, 'Đã khôi phục nhiệm vụ', 48, '2025-04-13 15:20:58', 'Test'),
(93, 1, 'Đã sửa nhiệm vụ', 47, '2025-04-13 15:24:50', 'Debug Knight2'),
(94, 1, 'Đã sửa nhiệm vụ', 47, '2025-04-13 15:25:08', 'Debug Knight2'),
(95, 1, 'Đã sửa nhiệm vụ', 47, '2025-04-13 15:26:54', 'Debug Knight2'),
(96, 1, 'Đã sửa nhiệm vụ', 47, '2025-04-13 15:27:00', 'Debug Knight2'),
(97, 1, 'Đã sửa nhiệm vụ', 47, '2025-04-13 15:27:11', 'Debug Knight2'),
(98, 1, 'Đã sửa nhiệm vụ', 47, '2025-04-13 15:28:43', 'Debug Knight2'),
(99, 1, 'Đã sửa nhiệm vụ', 47, '2025-04-13 15:28:52', 'Debug Knight2'),
(100, 1, 'Đã sửa nhiệm vụ', 47, '2025-04-13 15:30:11', 'Debug Knight2'),
(101, 4, 'Đã thêm nhiệm vụ', 50, '2025-04-18 21:10:07', 'Ngày hôm nay'),
(102, 4, 'Đã xóa nhiệm vụ', 50, '2025-04-18 21:10:55', 'Ngày hôm nay'),
(103, 4, 'Đã thêm nhiệm vụ', 51, '2025-04-18 21:16:48', 'ds'),
(104, 4, 'Đã thêm nhiệm vụ', 52, '2025-04-18 21:26:14', 'ds'),
(105, 4, 'Đã thêm nhiệm vụ', 53, '2025-04-18 21:26:36', 'duc copffff'),
(106, 4, 'Đã sửa nhiệm vụ', 53, '2025-04-18 21:26:57', 'duc'),
(107, 4, 'Đã sửa nhiệm vụ', 53, '2025-04-18 21:27:12', 'duc'),
(108, 4, 'Đã xóa nhiệm vụ', 53, '2025-04-18 21:27:20', 'duc'),
(109, 4, 'Đã xóa nhiệm vụ', 52, '2025-04-18 21:35:12', 'ds'),
(110, 4, 'Đã thêm nhiệm vụ', 54, '2025-04-18 21:43:15', 'dsâ'),
(111, 4, 'Đã sửa nhiệm vụ', 54, '2025-04-18 21:43:53', 'qqqqqqqqqqqqqqqqqq'),
(112, 4, 'Đã thêm nhiệm vụ', 55, '2025-04-18 21:44:55', 'ds'),
(113, 4, 'Đã sửa nhiệm vụ', 55, '2025-04-18 21:45:11', 'dsss'),
(114, 4, 'Đã thêm nhiệm vụ', 56, '2025-04-18 22:10:06', 'duc copffff'),
(115, 4, 'Đã sửa nhiệm vụ', 56, '2025-04-18 22:10:38', '88999jjjj'),
(116, 4, 'Đã xóa nhiệm vụ', 56, '2025-04-18 22:10:43', '88999jjjj'),
(117, 4, 'Đã sửa nhiệm vụ', 55, '2025-04-18 22:13:30', 'mmm'),
(118, 4, 'Đã thêm nhiệm vụ', 57, '2025-04-18 22:14:46', 'Đi ngủ');

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
-- Chỉ mục cho bảng `user_actions`
--
ALTER TABLE `user_actions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT cho bảng `tasks`
--
ALTER TABLE `tasks`
  MODIFY `task_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `user_actions`
--
ALTER TABLE `user_actions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=119;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `tasks_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Các ràng buộc cho bảng `user_actions`
--
ALTER TABLE `user_actions`
  ADD CONSTRAINT `user_actions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
