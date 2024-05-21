Если в базе данных, таблица b_user_session занимает много места, можно ее очистить выполнив код в командной php строке
Код
\Bitrix\Main\Session\Handlers\Table\UserSessionTable::deleteOlderThan(86400);
Командная php строка находится в админке сайта по пути
АДРЕС_САЙТА/bitrix/admin/php_command_line.php?lang=ru
