@echo off
rem START or STOP Services
rem ----------------------------------
rem Check if argument is STOP or START

if not ""%1"" == ""START"" goto stop

if exist C:\Users\myadw\OneDrive\Desktop\php project\hypersonic\scripts\ctl.bat (start /MIN /B C:\Users\myadw\OneDrive\Desktop\php project\server\hsql-sample-database\scripts\ctl.bat START)
if exist C:\Users\myadw\OneDrive\Desktop\php project\ingres\scripts\ctl.bat (start /MIN /B C:\Users\myadw\OneDrive\Desktop\php project\ingres\scripts\ctl.bat START)
if exist C:\Users\myadw\OneDrive\Desktop\php project\mysql\scripts\ctl.bat (start /MIN /B C:\Users\myadw\OneDrive\Desktop\php project\mysql\scripts\ctl.bat START)
if exist C:\Users\myadw\OneDrive\Desktop\php project\postgresql\scripts\ctl.bat (start /MIN /B C:\Users\myadw\OneDrive\Desktop\php project\postgresql\scripts\ctl.bat START)
if exist C:\Users\myadw\OneDrive\Desktop\php project\apache\scripts\ctl.bat (start /MIN /B C:\Users\myadw\OneDrive\Desktop\php project\apache\scripts\ctl.bat START)
if exist C:\Users\myadw\OneDrive\Desktop\php project\openoffice\scripts\ctl.bat (start /MIN /B C:\Users\myadw\OneDrive\Desktop\php project\openoffice\scripts\ctl.bat START)
if exist C:\Users\myadw\OneDrive\Desktop\php project\apache-tomcat\scripts\ctl.bat (start /MIN /B C:\Users\myadw\OneDrive\Desktop\php project\apache-tomcat\scripts\ctl.bat START)
if exist C:\Users\myadw\OneDrive\Desktop\php project\resin\scripts\ctl.bat (start /MIN /B C:\Users\myadw\OneDrive\Desktop\php project\resin\scripts\ctl.bat START)
if exist C:\Users\myadw\OneDrive\Desktop\php project\jetty\scripts\ctl.bat (start /MIN /B C:\Users\myadw\OneDrive\Desktop\php project\jetty\scripts\ctl.bat START)
if exist C:\Users\myadw\OneDrive\Desktop\php project\subversion\scripts\ctl.bat (start /MIN /B C:\Users\myadw\OneDrive\Desktop\php project\subversion\scripts\ctl.bat START)
rem RUBY_APPLICATION_START
if exist C:\Users\myadw\OneDrive\Desktop\php project\lucene\scripts\ctl.bat (start /MIN /B C:\Users\myadw\OneDrive\Desktop\php project\lucene\scripts\ctl.bat START)
if exist C:\Users\myadw\OneDrive\Desktop\php project\third_application\scripts\ctl.bat (start /MIN /B C:\Users\myadw\OneDrive\Desktop\php project\third_application\scripts\ctl.bat START)
goto end

:stop
echo "Stopping services ..."
if exist C:\Users\myadw\OneDrive\Desktop\php project\third_application\scripts\ctl.bat (start /MIN /B C:\Users\myadw\OneDrive\Desktop\php project\third_application\scripts\ctl.bat STOP)
if exist C:\Users\myadw\OneDrive\Desktop\php project\lucene\scripts\ctl.bat (start /MIN /B C:\Users\myadw\OneDrive\Desktop\php project\lucene\scripts\ctl.bat STOP)
rem RUBY_APPLICATION_STOP
if exist C:\Users\myadw\OneDrive\Desktop\php project\subversion\scripts\ctl.bat (start /MIN /B C:\Users\myadw\OneDrive\Desktop\php project\subversion\scripts\ctl.bat STOP)
if exist C:\Users\myadw\OneDrive\Desktop\php project\jetty\scripts\ctl.bat (start /MIN /B C:\Users\myadw\OneDrive\Desktop\php project\jetty\scripts\ctl.bat STOP)
if exist C:\Users\myadw\OneDrive\Desktop\php project\hypersonic\scripts\ctl.bat (start /MIN /B C:\Users\myadw\OneDrive\Desktop\php project\server\hsql-sample-database\scripts\ctl.bat STOP)
if exist C:\Users\myadw\OneDrive\Desktop\php project\resin\scripts\ctl.bat (start /MIN /B C:\Users\myadw\OneDrive\Desktop\php project\resin\scripts\ctl.bat STOP)
if exist C:\Users\myadw\OneDrive\Desktop\php project\apache-tomcat\scripts\ctl.bat (start /MIN /B /WAIT C:\Users\myadw\OneDrive\Desktop\php project\apache-tomcat\scripts\ctl.bat STOP)
if exist C:\Users\myadw\OneDrive\Desktop\php project\openoffice\scripts\ctl.bat (start /MIN /B C:\Users\myadw\OneDrive\Desktop\php project\openoffice\scripts\ctl.bat STOP)
if exist C:\Users\myadw\OneDrive\Desktop\php project\apache\scripts\ctl.bat (start /MIN /B C:\Users\myadw\OneDrive\Desktop\php project\apache\scripts\ctl.bat STOP)
if exist C:\Users\myadw\OneDrive\Desktop\php project\ingres\scripts\ctl.bat (start /MIN /B C:\Users\myadw\OneDrive\Desktop\php project\ingres\scripts\ctl.bat STOP)
if exist C:\Users\myadw\OneDrive\Desktop\php project\mysql\scripts\ctl.bat (start /MIN /B C:\Users\myadw\OneDrive\Desktop\php project\mysql\scripts\ctl.bat STOP)
if exist C:\Users\myadw\OneDrive\Desktop\php project\postgresql\scripts\ctl.bat (start /MIN /B C:\Users\myadw\OneDrive\Desktop\php project\postgresql\scripts\ctl.bat STOP)

:end

