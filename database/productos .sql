-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 12-02-2025 a las 17:59:54
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
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `categoria_id`, `nombre`, `descripcion`, `precio`, `stock`, `oferta`, `fecha`, `imagen`) VALUES
(2, 1, 'Amargo 1', 'Cafe amargo tipo 1', 6.00, 83, '0', '2025-01-01', '679a7cc69b14a_Brasil.jpg'),
(3, 2, 'Acido 1', 'Café acido', 7.00, 0, '0', '2025-01-01', '679a7fc7284eb_Colombia.jpg'),
(4, 3, 'Arabiga', 'Denso y negro y rojo y azul', 8.00, 45, '0', '2025-01-01', '679aa326c9626_Mexico.jpg'),
(5, 102, 'Cumbal', 'Mucho torrefacto', 8.00, 5, '0', '2025-01-01', '679aa35079751_España.jpg'),
(6, 4, 'Dulce', 'Muy suave.', 6.00, -3, '0', '2025-02-02', '679f9f0f19fd5_Portugal.jpg'),
(7, 1, 'Dulce', 'Cafe nada amargo', 5.00, 35, '0', '2025-02-01', '679f9fc137bf7_Brasil.jpg'),
(8, 3, 'Dulce', 'cafe nada amargo', 9.00, 3, '0', '2025-01-30', '679fa379ddeb6_Mexico.jpg'),
(9, 7, 'rico', 'bueno', 9.00, 10, '0', '2025-02-06', '67a4998acc52a_Vietnam.jpg');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
