-- =====================================================================
-- Hardware Solutions - Migración del 16 de noviembre de 2026 (sesión #5)
--
-- 1) Agrega la columna cpu.graficos_integrados y la llena para los 23
--    procesadores del catálogo.
-- 2) Elimina 4 registros duplicados (filas de prueba de agosto).
--
-- CÓMO EJECUTARLA (una sola vez):
--   phpMyAdmin (http://localhost/phpmyadmin6) -> base de datos "proyecto"
--   -> pestaña "Importar" -> elegir este archivo -> Importar.
-- Después, ejecutar database\exportar_bdd.bat para actualizar proyecto.sql.
-- =====================================================================

-- ---------------------------------------------------------------------
-- 1) Nueva columna: ¿el procesador trae gráficos integrados?
--    1 = sí (puede dar imagen sin tarjeta gráfica), 0 = no.
--    Por defecto 0: si no se sabe, se asume que necesita tarjeta gráfica,
--    que es el lado seguro.
-- ---------------------------------------------------------------------
ALTER TABLE `cpu`
  ADD COLUMN `graficos_integrados` TINYINT(1) NOT NULL DEFAULT 0 AFTER `consumo_w`;

-- Procesadores CON gráficos integrados (15 de 23).
-- Fuentes: fichas oficiales de AMD e Intel.
--   AMD AM5 (Ryzen 7000 y 9000 sin sufijo F): todos traen "Radeon Graphics".
--   Intel con sufijo K (sin F): UHD Graphics 770 / Intel Graphics.
UPDATE `cpu` SET `graficos_integrados` = 1 WHERE `nombre` IN (
  'Ryzen 5 7600', 'Ryzen 5 7600X', 'Ryzen 7 7700X', 'Ryzen 7 7800X3D', 'Ryzen 9 7900X',
  'Ryzen 5 9600X', 'Ryzen 7 9700X', 'Ryzen 7 9800X3D', 'Ryzen 9 9900X',
  'Core i5-13600K', 'Core i7-12700K', 'Core i9-12900K', 'Core i9-13900K',
  'Core Ultra 5 245K', 'Core Ultra 9 285K'
);

-- Procesadores SIN gráficos integrados (8 de 23): quedan en 0.
--   AMD AM4: Ryzen 5 5500, Ryzen 5 5600X, Ryzen 7 5700X (en AM4 solo los modelos "G" los traen).
--   Intel con sufijo F o KF: Core i5-12600KF, i5-14400F, i5-14600KF, i7-12700KF, i9-12900KF.

-- ---------------------------------------------------------------------
-- 2) Registros duplicados. En cada pareja se conserva el registro más
--    completo. Cada DELETE lleva id + nombre para no borrar otra cosa
--    por error si los ids no coinciden.
-- ---------------------------------------------------------------------
START TRANSACTION;

-- RAM: se borra la fila de prueba (sin ASIN, $480.000); queda la id 4 (con ASIN, $1.799.364).
DELETE FROM `ram` WHERE `id` = 1 AND `nombre` = 'Vengeance DDR5 32GB (2x16GB) 6000MHz' AND `asin` IS NULL;

-- Almacenamiento: se borra la fila de prueba (sin ASIN, $380.000); queda la id 4 (con ASIN, $977.569).
DELETE FROM `almacenamiento` WHERE `id` = 1 AND `nombre` = '990 Pro 1TB NVMe' AND `asin` IS NULL;

-- Gabinete: las dos filas son iguales (mismo precio, sin ASIN); se borra la más antigua (id 1).
DELETE FROM `gabinete` WHERE `id` = 1 AND `nombre` = '4000D Airflow';

-- Cooler: se borra la id 4, que no tiene sockets registrados; queda la id 1, que sí los tiene.
DELETE FROM `cooler` WHERE `id` = 4 AND `nombre` = 'Aqua Elite 240 V6 ARGB'
  AND NOT EXISTS (SELECT 1 FROM `cooler_socket` WHERE `cooler_id` = 4);

COMMIT;

-- Comprobación: debe mostrar 22 RAM, 12 almacenamiento, 23 gabinetes, 23 coolers y 15 CPU con gráficos.
SELECT
  (SELECT COUNT(*) FROM `ram`)            AS ram,
  (SELECT COUNT(*) FROM `almacenamiento`) AS almacenamiento,
  (SELECT COUNT(*) FROM `gabinete`)       AS gabinete,
  (SELECT COUNT(*) FROM `cooler`)         AS cooler,
  (SELECT SUM(`graficos_integrados`) FROM `cpu`) AS cpu_con_graficos;
