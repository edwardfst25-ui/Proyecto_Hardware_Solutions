@echo off
REM ==============================================================
REM  Hardware Solutions - IMPORTAR la BDD desde el repositorio
REM  Usar DESPUES de hacer "git pull".
REM  Borra la BDD local "proyecto" y la vuelve a crear con
REM  Documentos\Base de Datos\Archivo Principal\proyecto.sql
REM  Laragon (MySQL) debe estar iniciado.
REM ==============================================================
set MYSQL=C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysql.exe
set SQL=%~dp0..\Documentos\Base de Datos\Archivo Principal\proyecto.sql

echo Esto REEMPLAZA la BDD local "proyecto" por la del repositorio.
echo Los cambios locales que no hayas exportado se perderan.
choice /M "Continuar"
if errorlevel 2 exit /b

"%MYSQL%" -u root -e "DROP DATABASE IF EXISTS proyecto; CREATE DATABASE proyecto CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" || goto error
"%MYSQL%" -u root --default-character-set=utf8mb4 proyecto < "%SQL%" || goto error

echo.
echo BDD importada correctamente.
pause
exit /b

:error
echo.
echo ERROR: revisa que Laragon este iniciado.
pause
