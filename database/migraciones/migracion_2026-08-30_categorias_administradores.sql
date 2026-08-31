-- =====================================================================
-- Hardware Solutions — Migración sesión #3 (parte 2)
-- Resuelve los dos pendientes de diseño detectados en la revisión de
-- normalización: la relación administradores <-> armados_recomendados,
-- y categorias <-> las 8 tablas de componentes.
-- =====================================================================

USE `proyecto`;

-- ---------------------------------------------------------------------
-- 1) administradores <-> armados_recomendados
--    Cada armado curado queda registrado con el admin que lo creó.
--    armados_recomendados está vacía (0 filas), así que no hace falta
--    backfill ni valor por defecto.
-- ---------------------------------------------------------------------
ALTER TABLE armados_recomendados
  ADD COLUMN creado_por INT NOT NULL AFTER id;

ALTER TABLE armados_recomendados
  ADD CONSTRAINT fk_armados_creado_por
  FOREIGN KEY (creado_por) REFERENCES administradores(id)
  ON DELETE RESTRICT ON UPDATE CASCADE;

-- ---------------------------------------------------------------------
-- 2) categorias <-> las 8 tablas de componentes (opción #2)
--    2a) categorias necesita saber a qué tabla/archivo admin/*.php
--        corresponde cada categoría, para poder armar el menú dinámico.
-- ---------------------------------------------------------------------
ALTER TABLE categorias
  ADD COLUMN tabla VARCHAR(50) NOT NULL DEFAULT '' AFTER nombre;

UPDATE categorias SET tabla = 'cpu'            WHERE nombre = 'Procesador';
UPDATE categorias SET tabla = 'placa_madre'    WHERE nombre = 'Placa Madre';
UPDATE categorias SET tabla = 'ram'            WHERE nombre = 'Memoria RAM';
UPDATE categorias SET tabla = 'gpu'            WHERE nombre = 'Tarjeta Gráfica';
UPDATE categorias SET tabla = 'fuente_poder'   WHERE nombre = 'Fuente de Poder';
UPDATE categorias SET tabla = 'almacenamiento' WHERE nombre = 'Almacenamiento';
UPDATE categorias SET tabla = 'gabinete'       WHERE nombre = 'Gabinete';
UPDATE categorias SET tabla = 'cooler'         WHERE nombre = 'Refrigeración';

ALTER TABLE categorias
  ALTER COLUMN tabla DROP DEFAULT;

ALTER TABLE categorias
  ADD CONSTRAINT uq_categorias_tabla UNIQUE (tabla);

-- 2b) cada una de las 8 tablas de componentes queda vinculada a su
--     categoria_id real (relación explícita, ya no solo "implícita" por
--     tener una tabla separada). DEFAULT se deja puesto a propósito:
--     categoria_id es una constante por tabla (todo cpu es categoria 1),
--     así que sirve de red de seguridad si algún INSERT futuro lo omite.
ALTER TABLE cpu            ADD COLUMN categoria_id INT NOT NULL DEFAULT 1 AFTER id;
ALTER TABLE placa_madre    ADD COLUMN categoria_id INT NOT NULL DEFAULT 2 AFTER id;
ALTER TABLE ram            ADD COLUMN categoria_id INT NOT NULL DEFAULT 3 AFTER id;
ALTER TABLE gpu            ADD COLUMN categoria_id INT NOT NULL DEFAULT 4 AFTER id;
ALTER TABLE fuente_poder   ADD COLUMN categoria_id INT NOT NULL DEFAULT 5 AFTER id;
ALTER TABLE almacenamiento ADD COLUMN categoria_id INT NOT NULL DEFAULT 6 AFTER id;
ALTER TABLE gabinete       ADD COLUMN categoria_id INT NOT NULL DEFAULT 7 AFTER id;
ALTER TABLE cooler         ADD COLUMN categoria_id INT NOT NULL DEFAULT 8 AFTER id;

ALTER TABLE cpu            ADD CONSTRAINT fk_cpu_categoria            FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE placa_madre    ADD CONSTRAINT fk_placa_madre_categoria    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE ram            ADD CONSTRAINT fk_ram_categoria            FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE gpu            ADD CONSTRAINT fk_gpu_categoria            FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE fuente_poder   ADD CONSTRAINT fk_fuente_poder_categoria   FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE almacenamiento ADD CONSTRAINT fk_almacenamiento_categoria FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE gabinete       ADD CONSTRAINT fk_gabinete_categoria       FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE cooler         ADD CONSTRAINT fk_cooler_categoria         FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE RESTRICT ON UPDATE CASCADE;
