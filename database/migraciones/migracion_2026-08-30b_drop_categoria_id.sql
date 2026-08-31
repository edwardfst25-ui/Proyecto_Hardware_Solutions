-- =====================================================================
-- Hardware Solutions — Migración sesión #3 (parte 3)
-- Revierte categoria_id (columna + FK) en las 8 tablas de componentes.
-- Motivo: es un valor constante por tabla (ej. cooler.categoria_id
-- siempre = 8), no lo usa ningún archivo del proyecto, y duplica lo que
-- categorias.tabla ya expresa en la otra dirección. categorias se
-- mantiene intacta: admin/dashboard.php sigue leyendo categorias.tabla
-- para armar el menú dinámico, eso no cambia.
-- =====================================================================

USE `proyecto`;

ALTER TABLE cpu            DROP FOREIGN KEY fk_cpu_categoria;
ALTER TABLE placa_madre    DROP FOREIGN KEY fk_placa_madre_categoria;
ALTER TABLE ram            DROP FOREIGN KEY fk_ram_categoria;
ALTER TABLE gpu            DROP FOREIGN KEY fk_gpu_categoria;
ALTER TABLE fuente_poder   DROP FOREIGN KEY fk_fuente_poder_categoria;
ALTER TABLE almacenamiento DROP FOREIGN KEY fk_almacenamiento_categoria;
ALTER TABLE gabinete       DROP FOREIGN KEY fk_gabinete_categoria;
ALTER TABLE cooler         DROP FOREIGN KEY fk_cooler_categoria;

ALTER TABLE cpu            DROP COLUMN categoria_id;
ALTER TABLE placa_madre    DROP COLUMN categoria_id;
ALTER TABLE ram            DROP COLUMN categoria_id;
ALTER TABLE gpu            DROP COLUMN categoria_id;
ALTER TABLE fuente_poder   DROP COLUMN categoria_id;
ALTER TABLE almacenamiento DROP COLUMN categoria_id;
ALTER TABLE gabinete       DROP COLUMN categoria_id;
ALTER TABLE cooler         DROP COLUMN categoria_id;
