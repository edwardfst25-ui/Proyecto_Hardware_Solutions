-- =====================================================================
-- Hardware Solutions - Segunda migración del 16 de noviembre de 2026
--
-- 1) Dimensiones físicas para verificar que la tarjeta gráfica y el
--    disipador quepan en el gabinete (4 columnas, todas opcionales).
-- 2) Correo electrónico de los administradores (base para el futuro
--    restablecimiento de contraseña por correo).
--
-- CÓMO EJECUTARLA (una sola vez, después de la primera migración):
--   phpMyAdmin -> base de datos "proyecto" -> Importar -> este archivo.
-- Después, ejecutar database\exportar_bdd.bat.
-- =====================================================================

-- Largo de la tarjeta gráfica, en milímetros.
ALTER TABLE `gpu`
  ADD COLUMN `largo_mm` INT NULL DEFAULT NULL AFTER `consumo_w`;

-- Altura del disipador, en milímetros (solo refrigeración por aire).
ALTER TABLE `cooler`
  ADD COLUMN `alto_mm` INT NULL DEFAULT NULL AFTER `consumo_w`;

-- Espacio del gabinete: largo máximo de tarjeta gráfica y altura máxima de disipador.
ALTER TABLE `gabinete`
  ADD COLUMN `gpu_largo_max_mm` INT NULL DEFAULT NULL AFTER `factor_forma_soportado`,
  ADD COLUMN `cooler_alto_max_mm` INT NULL DEFAULT NULL AFTER `gpu_largo_max_mm`;

-- Las cuatro columnas admiten NULL: mientras no se registre la medida, el motor
-- de compatibilidad indica "sin dato" en lugar de aprobar o rechazar.

-- Correo del administrador: opcional para los que ya existen, único cuando se registra.
ALTER TABLE `administradores`
  ADD COLUMN `correo` VARCHAR(120) NULL DEFAULT NULL AFTER `nombre_completo`,
  ADD UNIQUE KEY `uq_administradores_correo` (`correo`);
