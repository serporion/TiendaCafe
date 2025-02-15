-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 12-02-2025 a las 18:01:32
-- Versión del servidor: 10.4.25-MariaDB
-- Versión de PHP: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `tienda`
--

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `apellidos`, `email`, `password`, `rol`, `fecha_expiracion`, `confirmado`, `token`) VALUES
(1, 'Oscar', 'Delgado', 'oscardelgadohuertas@hotmail.com', '$2y$10$LR5SnHmime6Jib/GQYfeXe/46u6yY5EqMdPMCebCFnXe9eg9T9bd.', 'admin', '2025-02-03 15:07:47', 1, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3MzgzNjE3NzMsImV4cCI6MTczODM2NTM3MywiZGF0YSI6eyJpZCI6MTksIm1haWwiOiJtaWNvcnJlb0Bjb3JyZW8uZXMifX0.zLxRGYQIQGJetkf8uWzxALeYsB3utxgCvpsyXjK37BU'),
(2, 'Pepe', 'Porcel', 'porcel@proceup.com', '$2y$10$2PQO/ydAn5r01wbWc3yl2.H4kA1C1J3DfjFUtCZVMOKK.ov4ZWxLy', 'user', '2025-02-03 12:07:47', 0, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3MzgzNzIwODksImV4cCI6MTczODM3NTY4OSwiZGF0YSI6eyJlbWFpbCI6Im9zY2FyZGVsZ2Fkb2hAZ21haWwuY29tIiwibm9tYnJlIjoiSm9zZSJ9fQ.W840PP2dWtrs-6RLBCxJFTcyFvea1-GrmuXB94JUmd4'),
(3, 'David', 'David', 'david@david.com', '$2y$10$BTXV9AMYQZ6xCAEPOK1jB.e7uZWbYjtdzhohhnIB6vxs58AFkLtKW', 'user', '2025-02-11 10:52:37', 0, 'dfasdfasdfasdf'),
(7, 'Jose', 'David', 'jose@jose.com', '$2y$10$0vrh74xDsl8sV8mb6Edgt.hnFQts49ThOWCDVLEXMyTScwghpAqbW', 'user', '2025-02-01 02:08:09', 1, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3MzgzNzIwODksImV4cCI6MTczODM3NTY4OSwiZGF0YSI6eyJlbWFpbCI6Im9zY2FyZGVsZ2Fkb2hAZ21haWwuY29tIiwibm9tYnJlIjoiSm9zZSJ9fQ.W840PP2dWtrs-6RLBCxJFTcyFvea1-GrmuXB94JUmd4'),
(14, 'Jose', 'David', 'rimawa6885@bmixr.com', '$2y$10$J4CxeNafOPQ05rQi2vMNEur/LQ/pE942ZGW8FypE8b8ySAopJV.eq', 'user', '2025-02-03 12:46:15', 1, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3Mzg1Nzk1NzUsImV4cCI6MTczODU4Njc3NSwiZGF0YSI6eyJlbWFpbCI6InJpbWF3YTY4ODVAYm1peHIuY29tIiwibm9tYnJlIjoiSm9zZSJ9fQ.6fPo_grXOHh2-6UE5xSrtqlSB9j9ignFcG_EKLQVdWw'),
(23, 'Perico', 'De los Palotes', 'oscardelgadoh@gmail.com', '$2y$10$NcMgqFkcyYZjTUzoWWnl0elQh0LtZUs/gwXtBZiTLyKD158tLXQd.', 'user', NULL, 1, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3MzkyODk3MzQsImRhdGEiOnsiZW1haWwiOiJvc2NhcmRlbGdhZG9oQGdtYWlsLmNvbSIsIm5vbWJyZSI6IlBlcmljbyJ9fQ.dlVEZrGnfmPsDkmfhvAv-Y1RlviByHSirzkg8hRfI8o');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
