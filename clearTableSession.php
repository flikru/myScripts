https://blog.budagov.ru/b-user-session-kak-pochistit/#:~:text=%D0%95%D1%81%D1%82%D1%8C%20%D0%B2%D0%BE%D0%B7%D0%BC%D0%BE%D0%B6%D0%BD%D0%BE%D1%81%D1%82%D1%8C%20%D0%B2%D1%80%D1%83%D1%87%D0%BD%D1%83%D1%8E%20%D0%BF%D0%BE%D1%87%D0%B8%D1%81%D1%82%D0%B8%D1%82%D1%8C%20%D1%82%D0%B0%D0%B1%D0%BB%D0%B8%D1%86%D1%83,%D1%83%D0%B4%D0%B0%D0%BB%D1%8F%D1%82%D1%81%D1%8F%20%D0%B2%D1%81%D0%B5%20%D1%81%D0%B5%D1%81%D1%81%D0%B8%D0%B8%20%D1%81%D1%82%D0%B0%D1%80%D1%88%D0%B5%20%D1%81%D1%83%D1%82%D0%BE%D0%BA.
Если в базе данных, таблица b_user_session занимает много места, можно ее очистить выполнив код в командной php строке
Код
\Bitrix\Main\Session\Handlers\Table\UserSessionTable::deleteOlderThan(86400);
Командная php строка находится в админке сайта по пути
АДРЕС_САЙТА/bitrix/admin/php_command_line.php?lang=ru
