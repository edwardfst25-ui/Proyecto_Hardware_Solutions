@echo off
REM ==============================================================
REM  Hardware Solutions - EXPORTAR la BDD al repositorio
REM  Usar cuando cambies la BDD (tablas o datos), ANTES de
REM  hacer commit y push. Sobrescribe
REM  Documentos\Base de Datos\Archivo Principal\proyecto.sql
REM  Laragon (MySQL) debe estar iniciado.
REM ==============================================================
set DUMP=C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysqldump.exe
set SQL=%~dp0..\Documentos\Base de Datos\Archivo Principal\proyecto.sql

"%DUMP%" -u root --default-character-set=utf8mb4 --routines --skip-dump-date proyecto > "%SQL%" || goto error

echo.
echo BDD exportada a proyecto.sql. Ahora haz commit y push.
pause
exit /b

:error
echo.
echo ERROR: revisa que Laragon este iniciado.
pause
