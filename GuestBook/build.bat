@ECHO OFF
REM Automated build script for Windows Azure Projects
REM To use this script you must be in the root of the WAZ project directory
 
REM Builds Windows Azure projects based on the structure
REM \ProjDir
REM \ProjDir\Web
REM \ProjDir\Worker
REM \ProjDir\Worker\startup.php
 
set PROJNAME="Guestbook"
set OUTPUT_DIR="%CD%\deploy"
set WACMDDIR="C:\WindowsAzureCmdLineTools4PHP"
set PHPRUNTIMEDIR=C:\Program Files (x86)\PHP\v5.3
set PHPRUNTIME="%PHPRUNTIMEDIR%\php.exe"
set DEFAULTWEBDOC=guestbook.php
 
set PROJ=%CD%

REM echo %PROJ%
REM echo %PROJNAME%
REM echo %OUTPUT_DIR%
REM echo %WACMDDIR%
REM echo %PHPRUNTIMEDIR%
REM echo %PHPRUNTIME%
 
REM Make sure the files that are required exists

cd "%WACMDDIR%"
%PHPRUNTIME% package.php  --source="%PROJ%\Web" --project="%PROJNAME%" --runDevFabric -f --target="%OUTPUT_DIR%" --worker-role-startup-script="startup.php" --worker-role="%PROJ%\Worker" --phpRuntime="%PHPRUNTIMEDIR%" --defaultDoc=%DEFAULTWEBDOC%
cd "%PROJ%"
