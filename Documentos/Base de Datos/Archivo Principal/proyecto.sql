-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 31, 2026 at 01:03 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `proyecto`
--

-- --------------------------------------------------------

--
-- Table structure for table `administradores`
--

CREATE TABLE `administradores` (
  `id` int NOT NULL,
  `usuario` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre_completo` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `administradores`
--

INSERT INTO `administradores` (`id`, `usuario`, `password_hash`, `nombre_completo`, `fecha_creacion`) VALUES
(1, '1094049952', '$2y$10$.a5foVZQaJ.NjddgtvkX9eBVBJNYOC.zNzyC8GFY/Fqmms3kpX7r6', 'Edward Peñaranda', '2026-08-16 21:42:10');

-- --------------------------------------------------------

--
-- Table structure for table `almacenamiento`
--

CREATE TABLE `almacenamiento` (
  `id` int NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `marca` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` enum('HDD','SSD SATA','SSD NVMe') COLLATE utf8mb4_unicode_ci NOT NULL,
  `capacidad_gb` int NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `asin` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `imagen` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `fecha_actualizacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `almacenamiento`
--

INSERT INTO `almacenamiento` (`id`, `nombre`, `marca`, `tipo`, `capacidad_gb`, `precio`, `asin`, `imagen`, `descripcion`, `fecha_actualizacion`) VALUES
(1, '990 Pro 1TB NVMe', 'Samsung', 'SSD NVMe', 1000, 380000.00, NULL, NULL, 'PCIe 4.0, hasta 7450 MB/s lectura. Excelente para sistema operativo.', '2026-08-20 01:47:09'),
(2, 'Black SN850X 2TB NVMe', 'Western Digital', 'SSD NVMe', 2000, 650000.00, NULL, NULL, 'PCIe 4.0, orientado a gaming, alta durabilidad.', '2026-08-20 01:47:09'),
(3, '870 EVO 4TB SATA', 'Samsung', 'SSD SATA', 4000, 1300000.00, NULL, NULL, 'Gran capacidad para almacenamiento masivo de archivos y juegos.', '2026-08-20 01:47:09'),
(4, '990 Pro 1TB NVMe', 'Samsung', 'SSD NVMe', 1000, 977569.00, 'B0BHJF2VRN', NULL, 'PCIe 4.0, hasta 7450 MB/s lectura.', '2026-08-30 22:23:54'),
(5, '990 EVO Plus 1TB NVMe', 'Samsung', 'SSD NVMe', 1000, 885919.00, 'B0DHLFWBQ1', NULL, 'PCIe Gen 4x4/5x2, hasta 7150 MB/s lectura.', '2026-08-30 22:23:54'),
(6, 'P3 Plus 2TB NVMe', 'Crucial', 'SSD NVMe', 2000, 1053944.00, 'B0B25ML2FH', NULL, 'PCIe Gen4, hasta 5000 MB/s lectura.', '2026-08-30 22:23:54'),
(7, 'P310 1TB NVMe', 'Crucial', 'SSD NVMe', 1000, 580144.00, 'B0DC8VPSHV', NULL, 'PCIe Gen4, hasta 7100 MB/s lectura.', '2026-08-30 22:23:54'),
(8, 'NV3 2TB NVMe', 'Kingston', 'SSD NVMe', 2000, 855369.00, 'B0DBR6TRZQ', NULL, 'PCIe Gen4x4, hasta 6000 MB/s, bajo consumo.', '2026-08-30 22:23:54'),
(9, 'LEGEND 900 2TB NVMe', 'ADATA', 'SSD NVMe', 2000, 1411379.00, 'B0C92C9WYY', NULL, 'PCIe Gen4x4, hasta 7000 MB/s, compatible PS5.', '2026-08-30 22:23:54'),
(10, 'XLR8 CS3140 1TB NVMe', 'PNY', 'SSD NVMe', 1000, 610694.00, 'B08TL9CBB9', NULL, 'PCIe Gen4x4, hasta 7500 MB/s lectura.', '2026-08-30 22:23:54'),
(11, 'MP44 2TB NVMe', 'TEAMGROUP', 'SSD NVMe', 2000, 1008119.00, 'B0C3VCD5Z8', NULL, 'PCIe Gen4x4, hasta 7000 MB/s lectura.', '2026-08-30 22:23:54'),
(12, 'NM790 2TB NVMe', 'Lexar', 'SSD NVMe', 2000, 1374719.00, 'B0C91X5DZL', NULL, 'PCIe Gen4x4, hasta 7400 MB/s, compatible PS5.', '2026-08-30 22:23:54'),
(13, 'Platinum P41 2TB NVMe', 'SK hynix', 'SSD NVMe', 2000, 1606777.00, 'B09QVD9V7R', NULL, 'PCIe Gen4, NAND 176 capas, hasta 7000 MB/s.', '2026-08-30 22:23:54');

-- --------------------------------------------------------

--
-- Table structure for table `armados_recomendados`
--

CREATE TABLE `armados_recomendados` (
  `id` int NOT NULL,
  `creado_por` int NOT NULL,
  `perfil_id` int NOT NULL,
  `rango_presupuesto` enum('Económico','Intermedio','Alto') COLLATE utf8mb4_unicode_ci NOT NULL,
  `cpu_id` int NOT NULL,
  `placa_id` int NOT NULL,
  `ram_id` int NOT NULL,
  `gpu_id` int DEFAULT NULL,
  `fuente_id` int NOT NULL,
  `almacenamiento_id` int NOT NULL,
  `gabinete_id` int NOT NULL,
  `cooler_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categorias`
--

CREATE TABLE `categorias` (
  `id` int NOT NULL,
  `nombre` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tabla` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`, `tabla`) VALUES
(1, 'Procesador', 'cpu'),
(2, 'Placa Madre', 'placa_madre'),
(3, 'Memoria RAM', 'ram'),
(4, 'Tarjeta Gráfica', 'gpu'),
(5, 'Fuente de Poder', 'fuente_poder'),
(6, 'Almacenamiento', 'almacenamiento'),
(7, 'Gabinete', 'gabinete'),
(8, 'Refrigeración', 'cooler');

-- --------------------------------------------------------

--
-- Table structure for table `cooler`
--

CREATE TABLE `cooler` (
  `id` int NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `marca` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` enum('Aire','Líquido (AIO)') COLLATE utf8mb4_unicode_ci NOT NULL,
  `consumo_w` int DEFAULT '5',
  `precio` decimal(10,2) NOT NULL,
  `asin` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `imagen` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `fecha_actualizacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cooler`
--

INSERT INTO `cooler` (`id`, `nombre`, `marca`, `tipo`, `consumo_w`, `precio`, `asin`, `imagen`, `descripcion`, `fecha_actualizacion`) VALUES
(1, 'Aqua Elite 240 V6 ARGB', 'Thermalright', 'Líquido (AIO)', 6, 280000.00, NULL, NULL, 'AIO de 240mm, muy buena relación precio-rendimiento, compatible con casi todo socket moderno.', '2026-08-20 01:47:09'),
(2, 'Hyper 212 Black Edition', 'Cooler Master', 'Aire', 3, 150000.00, NULL, NULL, 'El cooler de aire más popular del mercado, confiable y económico.', '2026-08-20 01:47:09'),
(3, 'NH-D15', 'Noctua', 'Aire', 3, 480000.00, NULL, NULL, 'Cooler de aire de doble torre, rendimiento casi de nivel AIO, muy silencioso.', '2026-08-20 01:47:09'),
(4, 'Aqua Elite 240 V6 ARGB', 'Thermalright', 'Líquido (AIO)', 6, 280000.00, NULL, NULL, 'AIO de 240mm, muy buena relación precio-rendimiento.', '2026-08-30 22:23:54'),
(5, 'ARCTIC Liquid Freezer III 360', 'ARCTIC', 'Líquido (AIO)', 12, 216303.00, 'B0DLWGG85P', NULL, 'Rendimiento térmico extremo con un radiador grueso de 38mm y un ventilador dedicado para refrigerar los VRM.', '2026-08-30 22:23:54'),
(6, 'Thermalright Peerless Assassin 120 SE ', 'Thermalright', 'Aire', 4, 96066.00, 'B09YTL3JT5', NULL, 'El rey indiscutible de la calidad-precio en aire; ofrece rendimiento térmico de gama alta a un costo muy bajo.', '2026-08-30 22:23:54'),
(7, 'darkFlash E400', 'darkFlash', 'Aire', 4, 68057.00, 'B0FS7DLW1X', NULL, 'Enfriador de torre única con ventilador ARGB y una pantalla digital superior tipo matriz.', '2026-08-30 22:23:54'),
(8, 'ALSEYE W90', 'ALSEYE', 'Aire', 2, 120670.00, 'B0C16VBVPV', NULL, 'Disipador de aire compacto con ventilador ARGB superior y una mini pantalla digital que monitoriza la temperatura.', '2026-08-30 22:23:54'),
(9, 'Gamma A40 ARGB', 'Ocypus', 'Aire', 2, 58772.00, 'B0DS2265RM', NULL, 'Enfriador de torre única compacto de 133mm con ventilador ARGB de alta presión y tapa de matriz.', '2026-08-30 22:23:54'),
(10, 'KF620-DGT', 'Jungle Leopard', 'Aire', 3, 126396.00, 'B0FJ27YMV5', NULL, 'Enfriador de doble torre con 6 tubos de calor, dos ventiladores ARGB y una cubierta con pantalla digital reflectante.', '2026-08-30 22:23:54'),
(11, 'MasterLiquid 360 Atmos', 'Cooler Master', 'Líquido (AIO)', 6, 386832.00, 'B0CK57N22Q', NULL, 'Refrigeración líquida de nueva generación con bomba de doble cámara optimizada y una cubierta superior.', '2026-08-30 22:23:54'),
(12, 'Wraith Prism', 'AMD', 'Aire', 3, 133421.00, 'B09JZM682W', NULL, 'Enfriador de CPU oficial por aire con iluminación RGB controlada por anillo y aspas, diseñado con tubos de cobre.', '2026-08-30 22:23:54'),
(13, 'M120D Plus', 'ALSEYE', 'Aire', 5, 210422.00, 'B09PTGF51P', NULL, 'Disipador de doble torre con ventiladores ARGB duales, 6 tubos de calor y una pantalla digital superior.', '2026-08-30 22:23:54'),
(14, 'Nautilus 360 RS', 'Corsair', 'Líquido (AIO)', 6, 278510.00, 'B0DF7B7324', NULL, 'Enfriador líquido todo en uno de 360mm de alto rendimiento, diseñado para una conexión simplificada y directa.', '2026-08-30 22:23:54'),
(15, 'Rainbow Vision 360 Turbo', 'Thermalright', 'Líquido (AIO)', 12, 686760.00, 'B0FQCFZ8DX', NULL, 'Refrigeración líquida premium de 360mm caracterizada por una espectacular pantalla OLED curva magnética.', '2026-08-30 22:23:54'),
(16, 'FROZN A620 PRO XE', 'ID-COOLING', 'Aire', 5, 237224.00, 'B0H5VN1874', NULL, 'Enfriador de CPU de doble torre con 6 tubos de calor, soporte de ventilador dual y acabado estético premium', '2026-08-30 22:23:54'),
(17, 'Phantom Spirit 120 SE', 'Thermalright', 'Aire', 2, 123486.00, 'B0D93R3YY9', NULL, 'Enfriador de CPU completamente negro de doble torre con 7 tubos de calor de cobre y ventiladores duales', '2026-08-30 22:23:54'),
(18, 'iCUE Link Titan 360 RX', 'Corsair', 'Aire', 5, 615887.00, 'B0D6BFBLTK', NULL, 'Enfriador líquido premium de 360mm con motor FlowDrive, 3 ventiladores RX120 RGB y ecosistema inteligente.', '2026-08-30 22:23:54'),
(19, 'Assassin X 120R SE V2', 'Thermalright', 'Aire', 15, 51344.00, 'B0C53K3CCN', NULL, 'Enfriador de CPU de torre única con ventilador silencioso RGB PWM de 120 mm y rodamiento de alta durabilidad.', '2026-08-30 22:23:54'),
(20, 'TOUGHLIQUID 360 EX Pro ARGB Sync', 'Thermaltake', 'Líquido (AIO)', 11, 433256.00, 'B0D4WWSKGQ', NULL, 'Enfriador líquido todo en uno de 360 mm con sistema de conexión magnética para ventiladores en cadena.', '2026-08-30 22:23:54'),
(21, 'Hyper 212 Halo Black', 'Cooler Master', 'Aire', 4, 105103.00, 'B0BRBWL38D', NULL, 'Enfriador de CPU por aire con diseño clásico compacto de 4 tubos de cobre y ventilador de 120 mm con iluminación.', '2026-08-30 22:23:54'),
(22, 'Liquid Freezer III Pro 240', 'ARCTIC', 'Líquido (AIO)', 4, 181949.00, 'B0DLWFBHSL', NULL, 'Refrigeración líquida de 240 mm con radiador grueso de 38 mm, bomba PWM, ventilador VRM integrado y marco de contacto.', '2026-08-30 22:23:54'),
(23, 'Kraken Plus 360 RGB White', 'NZXT', 'Líquido (AIO)', 14, 464205.00, 'B0F5SCG6QC', NULL, 'Refrigeración líquida blanca con pantalla LCD cuadrada personalizable de 1.54\", ventilador de marco único  y bomba Turbine.', '2026-08-30 22:23:54'),
(24, 'LightFlow ARGB 240 Black', 'Montech', 'Líquido (AIO)', 4, 197764.00, 'B0FRX2174K', NULL, 'Refrigeración líquida de 240 mm con diseño de espejo infinito en el bloque, bomba de 3100 RPM y 2 ventiladores ARGB PWM.', '2026-08-30 22:23:54');

-- --------------------------------------------------------

--
-- Table structure for table `cooler_socket`
--

CREATE TABLE `cooler_socket` (
  `cooler_id` int NOT NULL,
  `socket` enum('LGA1200','LGA1700','LGA1851','AM4','AM5') COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cooler_socket`
--

INSERT INTO `cooler_socket` (`cooler_id`, `socket`) VALUES
(1, 'LGA1200'),
(1, 'LGA1700'),
(1, 'LGA1851'),
(1, 'AM4'),
(1, 'AM5'),
(2, 'LGA1200'),
(2, 'LGA1700'),
(2, 'AM4'),
(2, 'AM5'),
(3, 'LGA1200'),
(3, 'LGA1700'),
(3, 'AM4'),
(3, 'AM5'),
(5, 'LGA1700'),
(5, 'LGA1851'),
(5, 'AM4'),
(5, 'AM5'),
(6, 'LGA1200'),
(6, 'LGA1700'),
(6, 'LGA1851'),
(6, 'AM4'),
(6, 'AM5'),
(7, 'LGA1200'),
(7, 'LGA1700'),
(7, 'LGA1851'),
(7, 'AM4'),
(7, 'AM5'),
(8, 'LGA1200'),
(8, 'LGA1700'),
(8, 'AM4'),
(8, 'AM5'),
(9, 'LGA1200'),
(9, 'LGA1700'),
(9, 'LGA1851'),
(9, 'AM4'),
(9, 'AM5'),
(10, 'LGA1200'),
(10, 'LGA1700'),
(10, 'LGA1851'),
(10, 'AM4'),
(10, 'AM5'),
(11, 'LGA1200'),
(11, 'LGA1700'),
(11, 'LGA1851'),
(11, 'AM4'),
(11, 'AM5'),
(12, 'AM4'),
(12, 'AM5'),
(13, 'LGA1200'),
(13, 'LGA1700'),
(13, 'AM4'),
(13, 'AM5'),
(14, 'LGA1700'),
(14, 'LGA1851'),
(14, 'AM4'),
(14, 'AM5'),
(15, 'LGA1700'),
(15, 'LGA1851'),
(15, 'AM4'),
(15, 'AM5'),
(16, 'LGA1200'),
(16, 'LGA1700'),
(16, 'LGA1851'),
(16, 'AM4'),
(16, 'AM5'),
(17, 'LGA1700'),
(17, 'LGA1851'),
(17, 'AM4'),
(17, 'AM5'),
(18, 'LGA1700'),
(18, 'LGA1851'),
(18, 'AM4'),
(18, 'AM5'),
(19, 'LGA1200'),
(19, 'LGA1700'),
(19, 'LGA1851'),
(19, 'AM4'),
(19, 'AM5'),
(20, 'LGA1700'),
(20, 'LGA1851'),
(20, 'AM4'),
(20, 'AM5'),
(21, 'LGA1200'),
(21, 'LGA1700'),
(21, 'AM4'),
(21, 'AM5'),
(22, 'LGA1700'),
(22, 'LGA1851'),
(22, 'AM4'),
(22, 'AM5'),
(23, 'LGA1700'),
(23, 'LGA1851'),
(23, 'AM4'),
(23, 'AM5'),
(24, 'LGA1700'),
(24, 'LGA1851'),
(24, 'AM4'),
(24, 'AM5');

-- --------------------------------------------------------

--
-- Table structure for table `cpu`
--

CREATE TABLE `cpu` (
  `id` int NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `marca` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `socket` enum('LGA1200','LGA1700','LGA1851','AM4','AM5') COLLATE utf8mb4_unicode_ci NOT NULL,
  `nucleos` int DEFAULT NULL,
  `frecuencia_ghz` decimal(3,1) DEFAULT NULL,
  `consumo_w` int NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `asin` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `imagen` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `fecha_actualizacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cpu`
--

INSERT INTO `cpu` (`id`, `nombre`, `marca`, `socket`, `nucleos`, `frecuencia_ghz`, `consumo_w`, `precio`, `asin`, `imagen`, `descripcion`, `fecha_actualizacion`) VALUES
(1, 'Core i5-12600KF', 'Intel', 'LGA1700', 10, 3.7, 125, 800000.00, NULL, NULL, '10 núcleos (6P+4E), 16 hilos, hasta 4.9 GHz boost. Ideal gama media-alta.', '2026-08-20 01:47:09'),
(2, 'Ryzen 7 9800X3D', 'AMD', 'AM5', 8, 4.7, 120, 1900000.00, NULL, NULL, '8 núcleos, 16 hilos, 5.2 GHz boost. El mejor CPU gaming de su generación (3D V-Cache).', '2026-08-20 01:47:09'),
(3, 'Ryzen 7 7800X3D', 'AMD', 'AM5', 8, 4.2, 120, 1400000.00, NULL, NULL, '8 núcleos, 16 hilos. Excelente relación precio/rendimiento en gaming.', '2026-08-20 01:47:09'),
(6, 'Core i5-14400F', 'Intel', 'LGA1700', 10, 2.5, 65, 720000.00, 'B0CQ1Y7KHV', NULL, '10 núcleos y 16 hilos con velocidades de hasta 4.7 GHz.', '2026-08-30 22:23:53'),
(7, 'Core i5-14600KF', 'Intel', 'LGA1700', 14, 3.5, 125, 1200000.00, 'B0CGJ4MLC8', NULL, '14 núcleos y 20 hilos con una velocidad turbo máxima de hasta 5.3 GHz.', '2026-08-30 22:23:53'),
(8, 'Core i5-13600K', 'Intel', 'LGA1700', 14, 3.5, 125, 1050000.00, 'B0BCDR9M33', NULL, '14 núcleos (6P+8E), 20 hilos y hasta 5,1 GHz.', '2026-08-30 22:23:53'),
(9, 'Core i7-12700K', 'Intel', 'LGA1700', 12, 3.6, 125, 1150000.00, 'B09FXNVDBJ', NULL, '12 núcleos (8P+4E), 20 hilos y hasta 5,0 GHz.', '2026-08-30 22:23:53'),
(10, 'Core i7-12700KF', 'Intel', 'LGA1700', 12, 3.6, 125, 1050000.00, 'B09FXKHN7M', NULL, 'Variante sin gráficos integrados, con 12 núcleos y 20 hilos.', '2026-08-30 22:23:53'),
(11, 'Core Ultra 5 245K', 'Intel', 'LGA1851', 14, 4.2, 125, 1250000.00, 'B0DFK2P311', NULL, '14 núcleos y 14 hilos con hasta 5.2 GHz de velocidad máxima.', '2026-08-30 22:23:53'),
(12, 'Core i9-12900K', 'Intel', 'LGA1700', 16, 3.2, 125, 1600000.00, 'B09FXDLX95', NULL, '16 núcleos (8P+8E), 24 hilos y hasta 5,2 GHz.', '2026-08-30 22:23:53'),
(13, 'Core i9-12900KF', 'Intel', 'LGA1700', 16, 3.2, 125, 1500000.00, 'B09FWYK5M9', NULL, '16 núcleos y 24 hilos, sin gráficos integrados.', '2026-08-30 22:23:53'),
(14, 'Core i9-13900K', 'Intel', 'LGA1700', 24, 3.0, 125, 1800000.00, 'B0BCF54SR1', NULL, '24 núcleos (8P+16E), 32 hilos y hasta 5,8 GHz.', '2026-08-30 22:23:53'),
(15, 'Core Ultra 9 285K', 'Intel', 'LGA1851', 24, 3.7, 125, 2400000.00, 'B0DFKC99VL', NULL, '24 núcleos y 24 hilos independientes (sin Hyper-Threading).', '2026-08-30 22:23:53'),
(16, 'Ryzen 5 5500', 'AMD', 'AM4', 6, 3.6, 65, 320000.00, 'B09VCJ171S', NULL, '6 núcleos, 12 hilos y hasta 4,2 GHz. Incluye refrigerador Wraith Stealth', '2026-08-30 22:23:53'),
(17, 'Ryzen 5 5600X', 'AMD', 'AM4', 6, 3.7, 65, 450000.00, 'B08166SLDF', NULL, '6 núcleos, 12 hilos y hasta 4,6 GHz.', '2026-08-30 22:23:53'),
(18, 'Ryzen 7 5700X', 'AMD', 'AM4', 8, 3.4, 65, 600000.00, 'B09VCHQHZ6', NULL, '8 núcleos, 16 hilos y hasta 4,6 GHz.', '2026-08-30 22:23:53'),
(19, 'Ryzen 5 7600', 'AMD', 'AM5', 6, 3.8, 65, 750000.00, 'B0BMQJWBDM', NULL, '6 núcleos, 12 hilos y hasta 5,1 GHz. Compatible con DDR5.', '2026-08-30 22:23:53'),
(20, 'Ryzen 5 7600X', 'AMD', 'AM5', 6, 4.7, 105, 650000.00, 'B0BBJDS62N', NULL, '6 núcleos, 12 hilos y hasta 5,3 GHz.', '2026-08-30 22:23:53'),
(21, 'Ryzen 7 7700X', 'AMD', 'AM5', 8, 4.5, 105, 900000.00, 'B0BBHHT8LY', NULL, '8 núcleos, 16 hilos y hasta 5,4 GHz.', '2026-08-30 22:23:53'),
(22, 'Ryzen 5 9600X', 'AMD', 'AM5', 6, 3.9, 65, 1050000.00, 'B0D6NN6TM7', NULL, '6 núcleos, 12 hilos y frecuencias de hasta 5.4 GHz boost.', '2026-08-30 22:23:53'),
(23, 'Ryzen 9 7900X', 'AMD', 'AM5', 12, 4.7, 170, 1300000.00, 'B0BBJ59WJ4', NULL, '12 núcleos, 24 hilos y hasta 5,6 GHz. Orientado a tareas exigentes.', '2026-08-30 22:23:53'),
(24, 'Ryzen 7 9700X', 'AMD', 'AM5', 8, 3.8, 65, 1350000.00, 'B0D6NMDNNX', NULL, '8 núcleos, 16 hilos basados en la arquitectura Zen 5.', '2026-08-30 22:23:53'),
(25, 'Ryzen 9 9900X', 'AMD', 'AM5', 12, 4.4, 120, 1750000.00, 'B0D6NN87T8', NULL, '12 núcleos y 24 hilos de procesamiento', '2026-08-30 22:23:53');

-- --------------------------------------------------------

--
-- Table structure for table `fuente_poder`
--

CREATE TABLE `fuente_poder` (
  `id` int NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `marca` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `potencia_w` int NOT NULL,
  `certificacion` enum('Sin certificación','80+ White','80+ Bronze','80+ Silver','80+ Gold','80+ Platinum','80+ Titanium') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `asin` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `imagen` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `fecha_actualizacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fuente_poder`
--

INSERT INTO `fuente_poder` (`id`, `nombre`, `marca`, `potencia_w`, `certificacion`, `precio`, `asin`, `imagen`, `descripcion`, `fecha_actualizacion`) VALUES
(1, 'RM1200x', 'Corsair', 1200, '80+ Gold', 950000.00, NULL, NULL, 'Modular, ideal para configuraciones con GPU de gama alta como RTX 5090.', '2026-08-20 01:47:09'),
(2, 'RM850x', 'Corsair', 850, '80+ Gold', 600000.00, NULL, NULL, 'Modular, cubre bien GPUs de gama media-alta.', '2026-08-20 01:47:09'),
(3, 'RM650x', 'Corsair', 650, '80+ Gold', 450000.00, NULL, NULL, 'Modular, suficiente para configuraciones de gama media.', '2026-08-20 01:47:09'),
(4, 'RM850e (2023)', 'Corsair', 850, '80+ Gold', 520000.00, 'B0BYR1BXC6', NULL, 'Sirve para alimentar computadoras gaming y de alto rendimiento, proporcionando energía estable para el procesador, tarjeta gráfica, discos y demás componentes.', '2026-08-30 22:23:53'),
(5, 'RM850e (2025)', 'Corsair', 850, '80+ Gold', 520000.00, 'B0DPR86ZC8', NULL, 'Sirve para alimentar PCs gaming modernas y estaciones de trabajo, especialmente equipos con tarjetas gráficas nuevas que utilizan conexión 12V-2x6.', '2026-08-30 22:23:54'),
(6, 'RM1000x (2025)', 'Corsair', 1000, '80+ Gold', 650000.00, 'B0FLGMTSRQ', NULL, 'Sirve para alimentar equipos gaming de alto rendimiento, especialmente computadores con procesadores y tarjetas gráficas de alto consumo.', '2026-08-30 22:23:54'),
(7, 'RM1000e (2025)', 'Corsair', 1000, '80+ Gold', 590000.00, 'B0DPR5RZ1T', NULL, 'Sirve para suministrar energía a computadores de alto rendimiento, permitiendo utilizar componentes exigentes como GPUs y CPUs potentes.', '2026-08-30 22:23:54'),
(8, 'RM750e (2023)', 'Corsair', 750, '80+ Gold', 430000.00, 'B0BYR1BXC6', NULL, 'Sirve para alimentar computadores gaming y de uso general, proporcionando potencia suficiente para tarjetas gráficas y procesadores de gama media y alta.', '2026-08-30 22:23:54'),
(9, 'MAG A750GL PCIE5', 'MSI', 750, '80+ Gold', 350000.00, 'B0CC3QBGDL', NULL, 'Sirve para alimentar PCs gaming, incluyendo equipos con tarjetas gráficas modernas PCIe 5.0, ofreciendo hasta 750 W de potencia.', '2026-08-30 22:23:54'),
(10, 'MAG A850GL PCIE5', 'MSI', 850, '80+ Gold', 470000.00, 'B0CB9MSJ5N', NULL, 'Sirve para alimentar computadores gaming de alto rendimiento, especialmente equipos con GPUs modernas y componentes que requieren mayor potencia.', '2026-08-30 22:23:54'),
(11, 'MAG A850GL PCIE5 White', 'MSI', 850, '80+ Gold', 480000.00, 'B0CT41HW5J', NULL, 'Sirve para alimentar PCs gaming de alto rendimiento y además está pensada para configuraciones con estética blanca. Es compatible con tarjetas gráficas modernas PCIe 5.0.', '2026-08-30 22:23:54'),
(12, 'MPG A1000G PCIE5', 'MSI', 1000, '80+ Gold', 650000.00, 'B0B434DB7Y', NULL, 'Sirve para alimentar PCs gaming de gama alta y estaciones de trabajo, soportando tarjetas gráficas de alto consumo y procesadores potentes.', '2026-08-30 22:23:54'),
(13, 'FOCUS V3 GX-850', 'Seasonic', 850, '80+ Gold', 560000.00, 'B0C5B888CM', NULL, 'Sirve para suministrar energía estable a computadores gaming y de alto rendimiento, protegiendo los componentes mediante sus sistemas de protección eléctrica.', '2026-08-30 22:23:54'),
(14, 'Toughpower GF3 850W', 'Thermaltake', 850, '80+ Gold', 560000.00, 'B0BF5SYZX6', NULL, 'Sirve para alimentar PCs gaming modernos, incluyendo equipos con tarjetas gráficas de alto consumo y conexión PCIe 5.0.', '2026-08-30 22:23:54'),
(15, 'Toughpower GF3 1000W', 'Thermaltake', 1000, '80+ Gold', 690000.00, 'B0BF3R83W8', NULL, 'Sirve para alimentar computadores gaming de gama alta, permitiendo instalar procesadores y tarjetas gráficas que requieren bastante energía.', '2026-08-30 22:23:54'),
(16, 'Toughpower GF3 1200W', 'Thermaltake', 1200, '80+ Gold', 850000.00, 'B0BF5Y2NXJ', NULL, 'Sirve para alimentar PCs de gama alta y estaciones de trabajo, especialmente configuraciones con GPUs y CPUs de alto consumo.', '2026-08-30 22:23:54'),
(17, 'Pure Power 12 M 850W', 'be quiet!', 850, '80+ Gold', 560000.00, 'B0BT2Y1LKQ', NULL, 'Sirve para alimentar computadores gaming y estaciones de trabajo, proporcionando energía estable y silenciosa para procesadores y tarjetas gráficas modernas.', '2026-08-30 22:23:54'),
(18, 'Pure Power 12 M 1000W', 'be quiet!', 1000, '80+ Gold', 680000.00, 'B0BVYCXQY4', NULL, 'Sirve para alimentar PCs gaming de alto rendimiento, especialmente configuraciones con tarjetas gráficas potentes y múltiples componentes.', '2026-08-30 22:23:54'),
(19, 'Dark Power Pro 13 1300W', 'be quiet!', 1300, '80+ Titanium', 1400000.00, 'B0C4Z6451J', NULL, 'Sirve para alimentar computadores de máximo rendimiento, estaciones de trabajo y PCs gaming de gama extrema con componentes de muy alto consumo.', '2026-08-30 22:23:54'),
(20, 'ROG Thor 1200P2 Gaming', 'ASUS', 1200, '80+ Platinum', 1100000.00, 'B0BNJ1X5V6', NULL, 'Sirve para alimentar PCs gaming de gama alta, incluyendo equipos con GPUs y CPUs de alto consumo; su pantalla OLED permite visualizar información del consumo eléctrico.', '2026-08-30 22:23:54'),
(21, 'ROG Strix 1000G', 'ASUS', 1000, '80+ Gold', 680000.00, 'B08X7BKLWS', NULL, 'Sirve para alimentar computadores gaming de alto rendimiento, proporcionando energía suficiente para tarjetas gráficas y procesadores potentes.', '2026-08-30 22:23:54'),
(22, 'Focus GX-1000 ATX 3.0', 'Seasonic', 1000, '80+ Gold', 700000.00, 'B0CNXZXCD8', NULL, 'Sirve para alimentar PCs gaming y estaciones de trabajo de alto rendimiento, especialmente equipos modernos con tarjetas gráficas de alto consumo.', '2026-08-30 22:23:54'),
(23, 'RM750x (2021)', 'Corsair', 750, '80+ Gold', 500000.00, 'B0DJ1M18CY', NULL, 'Sirve para alimentar computadores gaming y de alto rendimiento, proporcionando energía estable y silenciosa para CPU, GPU, almacenamiento y demás componentes.', '2026-08-30 22:23:54');

-- --------------------------------------------------------

--
-- Table structure for table `gabinete`
--

CREATE TABLE `gabinete` (
  `id` int NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `marca` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `factor_forma_soportado` enum('ATX','Micro-ATX','Mini-ITX') COLLATE utf8mb4_unicode_ci NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `asin` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `imagen` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `fecha_actualizacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gabinete`
--

INSERT INTO `gabinete` (`id`, `nombre`, `marca`, `factor_forma_soportado`, `precio`, `asin`, `imagen`, `descripcion`, `fecha_actualizacion`) VALUES
(1, '4000D Airflow', 'Corsair', 'ATX', 380000.00, NULL, NULL, 'Excelente flujo de aire, muy popular, buen espacio para cableado.', '2026-08-20 01:47:09'),
(2, 'H510 Flow', 'NZXT', 'ATX', 330000.00, NULL, NULL, 'Diseño minimalista, buena ventilación frontal.', '2026-08-20 01:47:09'),
(3, 'MasterBox Q300L', 'Cooler Master', 'Micro-ATX', 180000.00, NULL, NULL, 'Compacto y económico, ideal para builds más pequeños.', '2026-08-20 01:47:09'),
(4, '4000D Airflow', 'Corsair', 'ATX', 380000.00, NULL, NULL, 'Excelente flujo de aire, buen espacio para cableado.', '2026-08-30 22:23:54'),
(5, 'H6 Flow', 'NZXT', 'ATX', 324934.00, 'B0C89FCDFP', NULL, 'Diseño compacto de doble cámara con excelente flujo de aire y cristales panorámicos.', '2026-08-30 22:23:54'),
(6, 'Y70 Touch Infinite', 'HYTE', 'ATX', 1148180.00, 'B0DKJKG9V5', NULL, 'Gabinete estético de doble cámara que destaca por su pantalla táctil LCD de 2.5K integrada.', '2026-08-30 22:23:54'),
(7, 'O11 Dynamic EVO RGB', 'Lian Li', 'ATX', 495154.00, 'B0CRKVN35N', NULL, 'Icónico chasis panorámico modular con bandas de iluminación RGB duales ajustables.', '2026-08-30 22:23:54'),
(8, 'North', 'Fractal Design', 'ATX', 383737.00, 'B09V878FXQ', NULL, 'Diseño elegante y minimalista que incorpora un panel frontal de madera de nogal auténtica.', '2026-08-30 22:23:54'),
(9, 'Pop Air', 'Fractal Design', 'ATX', 247561.00, 'B08T1GB8D8', NULL, 'Excelente combinación de flujo de aire constante, estética moderna y gran soporte de ventilación.', '2026-08-30 22:23:54'),
(10, 'Lancool 216', 'Lian Li', 'ATX', 318744.00, 'B0BN3SY5XW', NULL, 'Chasis optimizado para refrigeración líquida equipado con dos ventiladores frontales PWM de 160 mm.', '2026-08-30 22:23:54'),
(11, 'H9 Flow', 'NZXT', 'ATX', 371358.00, 'B0DQPPKNSS', NULL, 'Estructura semitorre masiva con paneles de vidrio templado continuos para exhibir el hardware.', '2026-08-30 22:23:54'),
(12, 'Sky Two', 'Montech', 'ATX', 291169.00, 'B0BNTM5W5P', NULL, 'Configuración de doble panel de vidrio con un sistema de ventiladores invertidos preinstalados.', '2026-08-30 22:23:54'),
(13, 'Jonsbo D41 Mesh Screen', 'Jonsbo', 'Micro-ATX', 287795.00, 'B0BQJ2R3RV', NULL, 'Gabinete mesh compacto con pantalla de 8 pulgadas extraíble para usar como segundo monitor.', '2026-08-30 22:23:54'),
(14, 'ProArt PA602', 'Asus', 'ATX', 736557.00, 'B0CPP3DWLX', NULL, 'Chasis premium optimizado para estaciones de trabajo enfocadas en creadores de contenido.', '2026-08-30 22:23:54'),
(15, 'iCUE Link 3500X RGB', 'Corsair', 'ATX', 365199.00, 'B0CZVDHZFD', NULL, 'Diseño envolvente sin pilares compatible con el ecosistema inteligente de cableado oculto.', '2026-08-30 22:23:54'),
(16, 'A3-mATX', 'Lian Li', 'Micro-ATX', 214601.00, 'B0D5GYY364', NULL, 'Chasis ultracompacto y modular de instalación flexible desarrollado en colaboración con DAN Cases.', '2026-08-30 22:23:54'),
(17, 'NV5 MK2', 'Phanteks', 'ATX', 568504.00, 'B086YLBKF8', NULL, 'Torre de flujo de aire optimizado y diseño de vidrio continuo ideal para exhibiciones de GPU vertical.', '2026-08-30 22:23:54'),
(18, 'Corsair FRAME 4000D', 'Corsair', 'ATX', 278510.00, 'B0DFHNV7TK', NULL, 'Diseñado para quienes buscan máxima personalización y ensamble limpio.', '2026-08-30 22:23:54'),
(19, 'View 370 tg ARGB', 'Thermaltake', 'ATX', 309460.00, 'B0GH9PT1FR', NULL, 'Destaca por su avanzado sistema de gestión del ruido y acústica optimizada.', '2026-08-30 22:23:54'),
(20, 'Thermaltake Ver 170 WS ARGB', 'Thermaltake', 'Micro-ATX', 476810.00, 'B0FWVLS9YV', NULL, 'Destaca por su sincronización de iluminación inteligente y flexible.', '2026-08-30 22:23:54'),
(21, 'Ceres 300 TG ARGB', 'Thermaltake', 'Micro-ATX', 227940.00, 'B0C3B23MVP', NULL, 'Diseñado específicamente para usuarios que priorizan un enfriamiento extremo y flujo de aire masivo.', '2026-08-30 22:23:54'),
(22, 'DARKROCK EC2 Negro ATX', 'DARKROCK', 'ATX', 170189.00, 'B0D4QVMBTP', NULL, 'Destaca por su comodidad y facilidad de uso mediante su panel lateral magnético tipo puerta.', '2026-08-30 22:23:54'),
(23, 'Antec FLUX PRO', 'Antec', 'ATX', 464205.00, 'B0DDNS2SY3', NULL, 'Máximo rendimiento térmico con una estética elegante y soporte avanzado para ventiladores de alto flujo.', '2026-08-30 22:23:54'),
(24, 'Jonsbo X400', 'Jonsbo', 'ATX', 429883.00, 'B0G43LL2CL', NULL, 'Destaca de forma extra importante por su gigantesco espacio libre de 427 mm para tarjetas gráficas.', '2026-08-30 22:23:54');

-- --------------------------------------------------------

--
-- Table structure for table `gpu`
--

CREATE TABLE `gpu` (
  `id` int NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `marca` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `memoria_gb` int DEFAULT NULL,
  `consumo_w` int NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `asin` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `imagen` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `fecha_actualizacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gpu`
--

INSERT INTO `gpu` (`id`, `nombre`, `marca`, `memoria_gb`, `consumo_w`, `precio`, `asin`, `imagen`, `descripcion`, `fecha_actualizacion`) VALUES
(5, 'GeForce RTX 5090', 'NVIDIA (asus)', 32, 575, 8000000.00, 'B0DT9YQR11', NULL, 'Gama alta, 32GB GDDR7. Requiere fuente robusta.', '2026-08-30 22:23:53'),
(6, 'GeForce RTX 5080', 'NVIDIA (MSI)', 16, 360, 4800000.00, 'B0DQSLHSP2', NULL, 'Gama alta, 16GB GDDR7. Gran velocidad de reloj.', '2026-08-30 22:23:53'),
(7, 'GeForce RTX 5070', 'NVIDIA (Gigabyte)', 12, 250, 3200000.00, 'B0DTQMLX4F', NULL, 'Gama media-alta, 12GB GDDR7. Diseño compacto SFF.', '2026-08-30 22:23:53'),
(8, 'GeForce RTX 4090', 'NVIDIA (ASUS)', 24, 450, 7500000.00, 'B0BJFRT43X', NULL, 'Gama alta, 24GB GDDR6X. Rendimiento masivo en renderizado.', '2026-08-30 22:23:53'),
(9, 'GeForce RTX 4080 Super', 'NVIDIA (MSI)', 16, 320, 4500000.00, 'B0CQTN1CQH', NULL, 'Gama alta, 16GB GDDR6X. Balance óptimo para resolución 4K.', '2026-08-30 22:23:53'),
(10, 'GeForce RTX 4070 Ti Super', 'NVIDIA (Gigabyte)', 16, 285, 3600000.00, 'B0CQTP53S4', NULL, 'Gama media-alta, 16GB GDDR6X. Bus ampliado de 256 bits.', '2026-08-30 22:23:53'),
(11, 'GeForce RTX 4070 Super', 'NVIDIA (ASUS)', 12, 220, 2800000.00, 'B0CSJV61BN', NULL, 'Gama media-alta, 12GB GDDR6X. Excelente rendimiento en 1440p.', '2026-08-30 22:23:53'),
(12, 'GeForce RTX 4060 Ti', 'NVIDIA (MSI)', 16, 165, 2100000.00, 'B0CVPHDLTD', NULL, 'Gama media, 16GB GDDR6. Mayor VRAM para cargas pesadas.', '2026-08-30 22:23:53'),
(13, 'GeForce RTX 4060', 'NVIDIA (Gigabyte)', 8, 115, 1400000.00, 'B0C7W8GZMJ', NULL, 'Gama media-baja, 8GB GDDR6. Soporta DLSS 3 nativo.', '2026-08-30 22:23:53'),
(14, 'GeForce RTX 3060', 'NVIDIA (EVGA)', 12, 170, 1200000.00, 'B0H4X2M3NY', NULL, 'Gama de entrada, 12GB GDDR6. Ideal para presupuestos ajustados.', '2026-08-30 22:23:53'),
(15, 'Radeon RX 7900 XTX', 'AMD (XFX)', 24, 355, 4300000.00, 'B0BTPK5J68', NULL, 'Gama alta, 24GB GDDR6. Gran potencia en rasterizado puro.', '2026-08-30 22:23:53'),
(16, 'Radeon RX 7900 XT', 'AMD (Sapphire)', 20, 315, 3400000.00, 'B0BNLSDRKB', NULL, 'Gama alta, 20GB GDDR6. Interfaz robusta de 320 bits.', '2026-08-30 22:23:53'),
(17, 'Radeon RX 7800 XT', 'AMD (PowerColor)', 16, 263, 2400000.00, 'B0CGRMJF6C', NULL, 'Gama media-alta, 16GB GDDR6. Muy popular para calidad 1440p.', '2026-08-30 22:23:53'),
(18, 'Radeon RX 7700 XT', 'AMD (Sapphire)', 12, 245, 1950000.00, 'B0CHK2345D', NULL, 'Gama media, 12GB GDDR6. Optimizada para altas tasas de refresco.', '2026-08-30 22:23:53'),
(19, 'Radeon RX 7600 XT', 'AMD (XFX)', 16, 190, 1600000.00, 'B0CSSZCZZP', NULL, 'Gama media-baja, 16GB GDDR6. Excelente almacenamiento de texturas.', '2026-08-30 22:23:53'),
(20, 'Radeon RX 7600', 'AMD (Gigabyte)', 8, 165, 1300000.00, 'B0G6FCDZMK', NULL, 'Gama de entrada, 8GB GDDR6. Ideal para juego competitivo 1080p.', '2026-08-30 22:23:53'),
(21, 'Radeon RX 6700 XT', 'AMD (MSI)', 12, 230, 1500000.00, 'B0GWNKL4YZ', NULL, 'Gama media-baja, 12GB GDDR6. Muy vigente por costo-beneficio.', '2026-08-30 22:23:53'),
(22, 'Radeon RX 6600', 'AMD (XFX)', 8, 132, 1000000.00, 'B09J8VCFWN', NULL, 'Gama de entrada, 8GB GDDR6. Consumo energético muy bajo.', '2026-08-30 22:23:53'),
(23, 'Intel Arc B580', 'Intel (ASRock)', 12, 190, 1150000.00, 'B0DNV4NWF7', NULL, 'Gama media-baja, 12GB GDDR6. Arquitectura moderna Battlemage.', '2026-08-30 22:23:53'),
(24, 'Intel Arc A770', 'Intel (Asrock)', 16, 225, 1500000.00, 'B0DF7PJWH6', NULL, 'Gama media, 16GB GDDR6. Destaca en codificación de video AV1.', '2026-08-30 22:23:53'),
(25, 'Intel Arc A580', 'Intel (Sparkle)', 8, 185, 950000.00, 'B0CJGSP9R7', NULL, 'Gama de entrada, 8GB GDDR6. Opción económica para multitarea básica.', '2026-08-30 22:23:53');

-- --------------------------------------------------------

--
-- Table structure for table `perfiles_uso`
--

CREATE TABLE `perfiles_uso` (
  `id` int NOT NULL,
  `nombre` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `perfiles_uso`
--

INSERT INTO `perfiles_uso` (`id`, `nombre`, `descripcion`) VALUES
(1, 'Gaming', 'Enfocado en rendimiento gráfico para videojuegos'),
(2, 'Edición de video', 'Enfocado en procesamiento multinúcleo y RAM'),
(3, 'Diseño gráfico', 'Enfocado en tarjeta gráfica y precisión de color'),
(4, 'Ofimática / Básico', 'Uso general, navegación y trabajo de oficina');

-- --------------------------------------------------------

--
-- Table structure for table `placa_madre`
--

CREATE TABLE `placa_madre` (
  `id` int NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `marca` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `socket` enum('LGA1200','LGA1700','LGA1851','AM4','AM5') COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo_ram` enum('DDR4','DDR5') COLLATE utf8mb4_unicode_ci NOT NULL,
  `factor_forma` enum('ATX','Micro-ATX','Mini-ITX') COLLATE utf8mb4_unicode_ci NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `asin` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `imagen` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `fecha_actualizacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `placa_madre`
--

INSERT INTO `placa_madre` (`id`, `nombre`, `marca`, `socket`, `tipo_ram`, `factor_forma`, `precio`, `asin`, `imagen`, `descripcion`, `fecha_actualizacion`) VALUES
(1, 'TUF Gaming B760-Plus WiFi', 'ASUS', 'LGA1700', 'DDR5', 'ATX', 750000.00, NULL, NULL, 'Chipset Intel B760, soporta CPUs de 12a/13a/14a gen.', '2026-08-20 01:47:09'),
(2, 'B850 Gaming X WiFi6E', 'Gigabyte', 'AM5', 'DDR5', 'ATX', 850000.00, NULL, NULL, 'Chipset AMD B850, PCIe 5.0, soporta Ryzen serie 7000/9000.', '2026-08-20 01:47:09'),
(3, 'MAG B550 Tomahawk', 'MSI', 'AM4', 'DDR4', 'ATX', 450000.00, NULL, NULL, 'Chipset AMD B550, opción sólida y económica para plataforma AM4.', '2026-08-20 01:47:09'),
(4, 'PRIME B650-PLUS', 'ASUS', 'AM5', 'DDR5', 'ATX', 520600.00, 'B0BHM1L763', NULL, 'es una tarjeta madre de formato ATX diseñada para el socket AMD AM5, óptima para procesadores AMD Ryzen de las series 7000, 8000 y 9000.', '2026-08-30 22:23:53'),
(5, 'PRIME B650-PLUS WIFI', 'ASUS', 'AM5', 'DDR5', 'ATX', 935000.00, 'B0DGWQ128D', NULL, 'es una tarjeta madre de formato ATX diseñada para el socket AMD AM5, que añade conectividad inalámbrica de fábrica a la plataforma B650.', '2026-08-30 22:23:53'),
(6, 'TUF GAMING B650-PLUS WIFI', 'ASUS', 'AM5', 'DDR5', 'ATX', 491400.00, 'B0BHN7GGBQ', NULL, 'es una tarjeta madre de formato ATX robusta para el socket AMD AM5, diseñada con componentes de grado militar y VRM reforzado para largas sesiones de juego y trabajo pesado.', '2026-08-30 22:23:53'),
(7, 'ROG STRIX B650E-F GAMING WIFI', 'ASUS', 'AM5', 'DDR5', 'ATX', 890000.00, 'B0BHMTYZKZ', NULL, 'es una tarjeta madre gama alta de formato ATX diseñada para el socket AMD AM5, destacándose por su soporte para el estándar PCIe 5.0 tanto en la ranura para tarjeta de video principal como en el almacenamiento M.2.', '2026-08-30 22:23:53'),
(8, 'ProArt B650-CREATOR', 'ASUS', 'AM5', 'DDR5', 'ATX', 1100000.00, 'B0BQD5WWRT', NULL, 'es una tarjeta madre de formato ATX diseñada específicamente para profesionales, creadores de contenido y editores de video que utilizan el socket AMD AM5.', '2026-08-30 22:23:53'),
(9, 'PRIME B650M-A AX', 'ASUS', 'AM5', 'DDR5', 'Micro-ATX', 640000.00, 'B0BHMVFS77', NULL, 'una tarjeta madre de formato Micro-ATX diseñada para el socket AMD AM5, que ofrece una plataforma compacta, moderna y equilibrada para procesadores AMD Ryzen de las series 7000, 8000 y 9000.', '2026-08-30 22:23:53'),
(10, 'MAG B650M MORTAR WIFI', 'MSI', 'AM5', 'DDR5', 'Micro-ATX', 304000.00, 'B0BDSBNWMH', NULL, 'es una tarjeta madre de formato Micro-ATX orientada al segmento gaming y de alta durabilidad para el socket AMD AM5, diseñada para exprimir el rendimiento de los procesadores AMD Ryzen de las series 7000, 8000 y 9000.', '2026-08-30 22:23:53'),
(11, 'PRO B650-P WIFI', 'MSI', 'AM5', 'DDR5', 'ATX', 326900.00, 'B0C953W5VM', NULL, 'es una tarjeta madre de formato completo ATX enfocada en el segmento profesional y de trabajo para el socket AMD AM5, orientada a brindar alta estabilidad, conectividad rápida y excelente capacidad de expansión con procesadores AMD Ryzen de las series 7000, 8000 y 9000.', '2026-08-30 22:23:53'),
(12, 'MPG B650 CARBON WIFI', 'MSI', 'AM5', 'DDR5', 'ATX', 762950.00, 'B0BHCJDJGF', NULL, 'es una tarjeta madre de gama alta en formato ATX orientada a gaming entusiasta y tareas de alto rendimiento para el socket AMD AM5, ofreciendo una entrega de potencia excepcional y estética prémium con iluminación RGB.', '2026-08-30 22:23:53'),
(13, 'B650 AORUS ELITE AX V2', 'GIGABYTE', 'AM5', 'DDR5', 'ATX', 720000.00, 'B0CP17GMG3', NULL, 'es una tarjeta madre de gama media-alta en formato ATX enfocada en gaming y tareas exigentes para el socket AMD AM5, reconocida por su excelente entrega de energía, capacidad de disipación y facilidad de ensamblaje (DIY friendly).', '2026-08-30 22:23:53'),
(14, 'B650 EAGLE AX', 'GIGABYTE', 'AM5', 'DDR5', 'ATX', 650000.00, 'B0CSKH1NJF', NULL, 'es una tarjeta madre de formato ATX para el socket AMD AM5, diseñada para ofrecer una plataforma accesible con soporte DDR5, almacenamiento ultra rápido y conectividad inalámbrica de última generación.', '2026-08-30 22:23:53'),
(15, 'B650 GAMING X AX V2', 'GIGABYTE', 'AM5', 'DDR5', 'ATX', 700000.00, 'B0CSKH56WN', NULL, 'es una tarjeta madre equilibrada de formato ATX enfocada en el segmento gamer para el socket AMD AM5, ofreciendo alta durabilidad, conectividad inalámbrica y soporte para almacenamiento ultrarrápido.', '2026-08-30 22:23:53'),
(16, 'B650M PRO RS', 'ASRock', 'AM5', 'DDR5', 'Micro-ATX', 550000.00, 'B0C6HWFVNS', NULL, 'es una tarjeta madre de formato Micro-ATX altamente popular por ofrecer una de las mejores relaciones calidad-precio dentro del socket AMD AM5, combinando el soporte para memorias DDR5 y almacenamiento de nueva generación PCIe 5.0 M.2.', '2026-08-30 22:23:53'),
(17, 'B650M PRO RS WIFI', 'ASRock', 'AM5', 'DDR5', 'Micro-ATX', 620000.00, 'B0C5Y4SRKF', NULL, 'es una tarjeta madre de formato Micro-ATX reconocida por ofrecer una de las mejores relaciones calidad-precio del socket AMD AM5, integrando de fábrica conectividad Wi-Fi 6E, soporte DDR5 y ranura PCIe 5.0 M.2.', '2026-08-30 22:23:53'),
(18, 'B650 PRO RS', 'ASRock', 'AM5', 'DDR5', 'ATX', 650000.00, 'B0BH4KPQP8', NULL, 'es una tarjeta madre de formato completo ATX para el socket AMD AM5, muy valorada por ofrecer una excelente relación costo-beneficio, sólida entrega de energía y soporte para componentes de última generación.', '2026-08-30 22:23:53'),
(19, 'B650E Steel Legend WiFi', 'ASRock', 'AM5', 'DDR5', 'ATX', 850000.00, 'B0BH4MNQXV', NULL, 'es una tarjeta madre gama media-alta de formato ATX construida sobre el chipset AMD B650E (Extreme), destacándose por ofrecer ancho de banda PCIe 5.0 tanto en la ranura principal para tarjeta de video como en la ranura M.2 SSD, además de su característico diseño estético en tonos camuflados plata, blanco y negro.', '2026-08-30 22:23:53'),
(20, 'PRO B660-A DDR4', 'MSI', 'LGA1700', 'DDR4', 'ATX', 420000.00, 'B0CGTBBW5N', NULL, 'es una tarjeta madre de formato ATX orientada al sector profesional y de productividad para el socket Intel LGA 1700, diseñada para ofrecer estabilidad, conectividad rápida y soporte para memoria RAM DDR4 con procesadores Intel Core de 12.ª, 13.ª y 14.ª generación.', '2026-08-30 22:23:53'),
(21, 'PRO B760-P WIFI', 'MSI', 'LGA1700', 'DDR5', 'ATX', 650000.00, 'B0CXWP1CK1', NULL, 'es una tarjeta madre de formato completo ATX enfocada en el entorno profesional y de oficina para el socket Intel LGA 1700, diseñada para ofrecer rendimiento equilibrado y excelente conectividad inalámbrica con procesadores Intel Core de 12.ª, 13.ª y 14.ª generación.', '2026-08-30 22:23:53'),
(22, 'B760 GAMING X AX', 'GIGABYTE', 'LGA1700', 'DDR5', 'ATX', 700000.00, 'B0BSHC2ZD4', NULL, 'es una tarjeta madre de formato ATX enfocada en el segmento gamer entusiasta para el socket Intel LGA 1700, compatible con procesadores Intel Core de 12.ª, 13.ª y 14.ª generación.', '2026-08-30 22:23:53'),
(23, 'Z790 AORUS ELITE AX', 'GIGABYTE', 'LGA1700', 'DDR5', 'ATX', 900000.00, 'B0BH9DXY38', NULL, 'es una tarjeta madre de gama alta en formato ATX con el chipset Intel Z790 para el socket LGA 1700, diseñada para gaming avanzado y overclocking con procesadores Intel Core de 12.ª, 13.ª y 14.ª generación.', '2026-08-30 22:23:53');

-- --------------------------------------------------------

--
-- Table structure for table `ram`
--

CREATE TABLE `ram` (
  `id` int NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `marca` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` enum('DDR4','DDR5') COLLATE utf8mb4_unicode_ci NOT NULL,
  `capacidad_gb` int NOT NULL,
  `velocidad_mhz` int DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `asin` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `imagen` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `fecha_actualizacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ram`
--

INSERT INTO `ram` (`id`, `nombre`, `marca`, `tipo`, `capacidad_gb`, `velocidad_mhz`, `precio`, `asin`, `imagen`, `descripcion`, `fecha_actualizacion`) VALUES
(1, 'Vengeance DDR5 32GB (2x16GB) 6000MHz', 'Corsair', 'DDR5', 32, 6000, 480000.00, NULL, NULL, 'Kit dual channel, CL30, ideal para gaming y edición.', '2026-08-20 01:47:09'),
(2, 'Trident Z5 RGB DDR5 32GB (2x16GB) 6000MHz', 'G.Skill', 'DDR5', 32, 6000, 520000.00, NULL, NULL, 'Kit dual channel con iluminación RGB, alto rendimiento.', '2026-08-20 01:47:09'),
(3, 'Vengeance LPX DDR4 16GB (2x8GB) 3200MHz', 'Corsair', 'DDR4', 16, 3200, 190000.00, NULL, NULL, 'Kit dual channel, opción económica para plataformas AM4/LGA1700 DDR4.', '2026-08-20 01:47:09'),
(4, 'Vengeance DDR5 32GB (2x16GB) 6000MHz', 'Corsair', 'DDR5', 32, 6000, 1799364.00, 'B0CBRJ63RT', NULL, 'Kit dual channel, CL30, ideal para gaming y edición.', '2026-08-30 22:23:54'),
(5, 'Ripjaws V DDR4 16GB (2x8GB) 3600MHz CL18', 'G.Skill', 'DDR4', 16, 3600, 1082233.00, 'B07XJLDHW4', NULL, 'Kit económico y confiable para builds de gaming DDR4 con perfil XMP 2.0.', '2026-08-30 22:23:54'),
(6, 'FURY Beast DDR4 16GB (2x8GB) 3200MHz CL16', 'Kingston', 'DDR4', 16, 3200, 702619.00, 'B097K2WBL3', NULL, 'Memoria Plug N Play de bajo perfil, buena para actualizaciones simples y económicas.', '2026-08-30 22:23:54'),
(7, 'T-Force Delta RGB DDR4 16GB (2x8GB) 3200MHz', 'TEAMGROUP', 'DDR4', 16, 3200, 498239.00, 'B07RWDKZ2D', NULL, 'Iluminación RGB de 120° con software de sincronización, buena estética para gaming.', '2026-08-30 22:23:54'),
(8, 'FURY Renegade DDR5 32GB (2x16GB) 6400MHz CL32', 'Kingston', 'DDR5', 32, 6400, 1746665.00, 'B0B72827G5', NULL, 'Memoria de overclocking premium, ideal para entusiastas y rendimiento extremo.', '2026-08-30 22:23:54'),
(9, 'Vengeance LPX DDR4 32GB (2x16GB) 3600MHz CL18', 'Corsair', 'DDR4', 32, 3600, 824697.00, 'B07ZPLM1R1', NULL, 'Perfil bajo (34mm), compatible con coolers grandes, gran capacidad.', '2026-08-30 22:23:54'),
(10, 'Vengeance RGB PRO DDR4 32GB (2x16GB) 3600MHz', 'Corsair', 'DDR4', 32, 3600, 763597.00, 'B082DGZJ9C', NULL, 'RGB multizona con software iCUE, buen balance entre estética y rendimiento.', '2026-08-30 22:23:54'),
(11, 'T-Force Vulcan Z DDR4 16GB (2x8GB) 3200MHz', 'TEAMGROUP', 'DDR4', 16, 3200, 474991.00, 'B07T637L7T', NULL, 'Disipador de aluminio simple y efectivo, opción económica sin RGB.', '2026-08-30 22:23:54'),
(12, 'Flare X5 DDR5 32GB (2x16GB) 6000MHz CL36', 'G.Skill', 'DDR5', 32, 6000, 1625168.00, 'B0BFGB2D2Z', NULL, 'Diseño minimalista con soporte AMD EXPO, pensado para plataformas Ryzen.', '2026-08-30 22:23:54'),
(13, 'FURY Beast DDR5 16GB (2x8GB) 5200MHz CL40', 'Kingston', 'DDR5', 16, 5200, 999993.00, 'B09T97ZSVB', NULL, 'Entrada económica a DDR5 con ECC en matriz para mayor estabilidad.', '2026-08-30 22:23:54'),
(14, 'Pro DDR5 32GB (2x16GB) 6000MHz CL36', 'Crucial', 'DDR5', 32, 6000, 1649669.00, 'B0CTHXMYL8', NULL, 'Memoria de overclocking con buena relación precio-rendimiento en DDR5.', '2026-08-30 22:23:54'),
(15, 'Ripjaws S5 DDR5 32GB (2x16GB) 6000MHz CL36', 'G.Skill', 'DDR5', 32, 6000, 1528080.00, 'B0BJ181PX7', NULL, 'Diseño minimalista de solo 33mm de altura, ideal para coolers grandes.', '2026-08-30 22:23:54'),
(16, 'Dominator Titanium RGB DDR5 32GB (2x16GB) 6000MHz CL30', 'Corsair', 'DDR5', 32, 6000, 2333561.00, 'B0CHSDCR8V', NULL, 'Gama premium con construcción en aluminio forjado y RGB de 11 zonas vía iCUE.', '2026-08-30 22:23:54'),
(17, 'Vengeance DDR5 64GB (2x32GB) 6000MHz CL30', 'Corsair', 'DDR5', 64, 6000, 4270431.00, 'B0C5M7J967', NULL, 'Alta capacidad para multitarea extrema y estaciones de trabajo; perfil AMD EXPO/Intel XMP de un clic.', '2026-08-30 22:23:54'),
(18, 'Trident Z5 Neo RGB DDR5 32GB (2x16GB) 6000MHz CL30', 'G.Skill', 'DDR5', 32, 6000, 2227919.00, 'B0BF8FVLSL', NULL, 'Disipador negro con RGB y perfil AMD EXPO, pensado para plataformas Ryzen de alto rendimiento.', '2026-08-30 22:23:54'),
(19, 'FURY Beast DDR4 32GB (2x16GB) 3200MHz CL16', 'Kingston', 'DDR4', 32, 3200, 977569.00, 'B097K2WKZW', NULL, 'Memoria Plug N Play de bajo perfil, opción confiable para subir de capacidad en equipos DDR4.', '2026-08-30 22:23:54'),
(20, 'T-Force Delta RGB DDR5 32GB (2x16GB) 6000MHz CL30', 'TEAMGROUP', 'DDR5', 32, 6000, 2016605.00, 'B0B3HHB3Z9', NULL, 'Diseño inspirado en aviones stealth, iluminación RGB de 120° y overclock de un clic.', '2026-08-30 22:23:54'),
(21, 'Vengeance RGB DDR5 16GB (2x8GB) 5200MHz CL40', 'Corsair', 'DDR5', 16, 5200, 1020828.00, 'B0BZHXGYWS', NULL, 'Memoria RGB de 10 zonas con software iCUE, capacidad reducida ideal para builds compactos o de oficina/gaming básico.', '2026-08-30 22:23:54'),
(22, 'Vengeance LPX DDR4 16GB (2x8GB) 3600MHz CL18', 'Corsair', 'DDR4', 16, 3600, 488647.00, 'B07RM39V5F', NULL, 'Memoria de perfil bajo (34mm) muy popular, ideal para builds compactos con buen rendimiento en 3600MHz.', '2026-08-30 22:23:54'),
(23, 'FURY Beast DDR5 32GB (2x16GB) 5200MHz CL40', 'Kingston', 'DDR5', 32, 5200, 1677806.00, 'B09KCLP63B', NULL, 'Kit DDR5 sin RGB, confiable y ampliamente disponible, buena entrada a DDR5 con soporte Intel XMP 3.0.', '2026-08-30 22:23:54');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `administradores`
--
ALTER TABLE `administradores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- Indexes for table `almacenamiento`
--
ALTER TABLE `almacenamiento`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `armados_recomendados`
--
ALTER TABLE `armados_recomendados`
  ADD PRIMARY KEY (`id`),
  ADD KEY `perfil_id` (`perfil_id`),
  ADD KEY `cpu_id` (`cpu_id`),
  ADD KEY `placa_id` (`placa_id`),
  ADD KEY `ram_id` (`ram_id`),
  ADD KEY `gpu_id` (`gpu_id`),
  ADD KEY `fuente_id` (`fuente_id`),
  ADD KEY `almacenamiento_id` (`almacenamiento_id`),
  ADD KEY `gabinete_id` (`gabinete_id`),
  ADD KEY `cooler_id` (`cooler_id`),
  ADD KEY `fk_armados_creado_por` (`creado_por`);

--
-- Indexes for table `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`),
  ADD UNIQUE KEY `uq_categorias_tabla` (`tabla`);

--
-- Indexes for table `cooler`
--
ALTER TABLE `cooler`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cooler_socket`
--
ALTER TABLE `cooler_socket`
  ADD PRIMARY KEY (`cooler_id`,`socket`);

--
-- Indexes for table `cpu`
--
ALTER TABLE `cpu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fuente_poder`
--
ALTER TABLE `fuente_poder`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gabinete`
--
ALTER TABLE `gabinete`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gpu`
--
ALTER TABLE `gpu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `perfiles_uso`
--
ALTER TABLE `perfiles_uso`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indexes for table `placa_madre`
--
ALTER TABLE `placa_madre`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ram`
--
ALTER TABLE `ram`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `administradores`
--
ALTER TABLE `administradores`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `almacenamiento`
--
ALTER TABLE `almacenamiento`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `armados_recomendados`
--
ALTER TABLE `armados_recomendados`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `cooler`
--
ALTER TABLE `cooler`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `cpu`
--
ALTER TABLE `cpu`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `fuente_poder`
--
ALTER TABLE `fuente_poder`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `gabinete`
--
ALTER TABLE `gabinete`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `gpu`
--
ALTER TABLE `gpu`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `perfiles_uso`
--
ALTER TABLE `perfiles_uso`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `placa_madre`
--
ALTER TABLE `placa_madre`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `ram`
--
ALTER TABLE `ram`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `armados_recomendados`
--
ALTER TABLE `armados_recomendados`
  ADD CONSTRAINT `armados_recomendados_ibfk_1` FOREIGN KEY (`perfil_id`) REFERENCES `perfiles_uso` (`id`),
  ADD CONSTRAINT `armados_recomendados_ibfk_2` FOREIGN KEY (`cpu_id`) REFERENCES `cpu` (`id`),
  ADD CONSTRAINT `armados_recomendados_ibfk_3` FOREIGN KEY (`placa_id`) REFERENCES `placa_madre` (`id`),
  ADD CONSTRAINT `armados_recomendados_ibfk_4` FOREIGN KEY (`ram_id`) REFERENCES `ram` (`id`),
  ADD CONSTRAINT `armados_recomendados_ibfk_5` FOREIGN KEY (`gpu_id`) REFERENCES `gpu` (`id`),
  ADD CONSTRAINT `armados_recomendados_ibfk_6` FOREIGN KEY (`fuente_id`) REFERENCES `fuente_poder` (`id`),
  ADD CONSTRAINT `armados_recomendados_ibfk_7` FOREIGN KEY (`almacenamiento_id`) REFERENCES `almacenamiento` (`id`),
  ADD CONSTRAINT `armados_recomendados_ibfk_8` FOREIGN KEY (`gabinete_id`) REFERENCES `gabinete` (`id`),
  ADD CONSTRAINT `armados_recomendados_ibfk_9` FOREIGN KEY (`cooler_id`) REFERENCES `cooler` (`id`),
  ADD CONSTRAINT `fk_armados_creado_por` FOREIGN KEY (`creado_por`) REFERENCES `administradores` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `cooler_socket`
--
ALTER TABLE `cooler_socket`
  ADD CONSTRAINT `cooler_socket_ibfk_1` FOREIGN KEY (`cooler_id`) REFERENCES `cooler` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
