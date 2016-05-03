if (!window.dict || typeof window.dict != object) window.dict = {};
dict.config = {};
dict.config = {
"unyn": {
"0": "Не указано", "1": "Да", "2": "Нет"}
, "a_edu": {
"0": "Не указано", "1": "Среднее неполное", "2": "Среднее", "3": "Среднее специальное", "4": "Студент", "5": "Незаконченное высшее", "6": "Высшее", "7": "Аспирант", "8": "Кандидат наук", "9": "Доктор наук", "10": "Академиг"}
, "a_period": {
"0": "Не указано", "1": "Меньше года", "2": "1-2 года", "3": "3-4 года", "4": "5 лет и больше"}
, "dw2": {
"1": "пн", "2": "вт", "3": "ср", "4": "чт", "5": "пт", "6": "сб", "7": "вс"}
, "dw4": {
"1": "Понедельник", "2": "Вторник", "3": "Среда", "4": "Четверг", "5": "Пятница", "6": "Суббота", "7": "Воскресенье"}
, "mm2": {
"1": "янв", "2": "фев", "3": "мар", "4": "апр", "5": "май", "6": "июн", "7": "июл", "8": "авг", "9": "сен", "10": "окт", "11": "ноя", "12": "дек"}
, "mm4": {
"1": "Январь", "2": "Февраль", "3": "Март", "4": "Апрель", "5": "Май", "6": "Июнь", "7": "Июль", "8": "Август", "9": "Сентябрь", "10": "Октябрь", "11": "Ноябрь", "12": "Декабрь"}
, "blog": {
"catalog_offset": "4"}
, "cena_nabor": {
"a": "1000", "b": "5000", "c": "10000", "d": "15000", "e": "20000", "f": "24000"}
, "editor": {
"img_descr": ""}
, "ext": {
"articles": ".html", "subjects": ".asp", "public": "", "data": ".asp", "constructor": ".asp"}
, "group_func_sum": {
"papki": "bumaga"}
, "img_fields": {
"files": "id", "articles": "img"}
, "img_size_list": {
"default_big": "---", "photo_big": "600", "main_img_big": "950"}
, "map": {
"h": "104.255429", "v": "52.276675", "level": "16", "title": "Презентомания", "balloon_img": "logo2.jpg"}
, "present_group_id": {
"f": "1", "m": "2", "p": "4", "k": "8", "d": "16"}
, "present_group_value": {
"f": "Женщине", "m": "Мужчине", "p": "Для двоих", "k": "Компании", "d": "Детям"}
, "rating_mail_ru": {
"id": "", "theme": "54", "src": "http://de.cd.be.a1.top.mail.ru/counter?id=2022933"}
, "site": {
"charset": "UTF-8", "doctype": "<!DOCTYPE html>", "metas": "<meta	 http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />", "field_div": ";", "path_div": ":", "cookie_div": "|", "uri_prefix": "/present.ru", "unjs_msg": "\r\n<ul class=\"msg\" id=\"unjs\"><li>Javascript отключён, ряд функций сайта работать не будет, вы не сможете добавлять информацию.</li></ul>\r\n", "show_flag": "ru", "upload_ext": "jpg|jpeg|gif|png|doc|xls|odt|zip|txt|gz", "img_ext": "jpg|jpeg|gif|png", "ajax": "", "date_div": ".", "title": "Презентомания", "root_page": "index", "presents_per_page": "9", "file_attach": "", "catalog_lister": ""}
, "status_oplaty": {
"s1": "-------------", "s2": "Выставлен счёт", "s3": "Счёт оплачен", "s4": "Счёт не нужен"}
, "tabsort1": {
"use_zebra": "", "show_log": "", "arrows_draw": "1", "paginate": "1", "use_inputs": "1", "strip_tags": "1", "patch_similar": "1", "bg_cols": "", "bg_cells": "", "alt_mirror": "", "axis_require": "1", "page_knopka": "", "page_size": "30"}
, "thubm_list": {
"default_thumb": "---", "photo_thumb": "130", "main_img_thumb": "260"}
, "tpls": {
"page": "page", "logo3": "logo3", "blog": "blog", "blog_item": "blog_item", "licens": "licens", "company": "company", "catalog": "catalog", "contacts": "contacts", "messages": "messages", "requests": "requests", "present": "present", "logo2": "logo2", "logo_anonce": "logo_anonce", "logo": "logo", "logo4": "logo4", "present_new": "present_new", "cart": "cart", "present_multi": "present_multi"}
, "vid_oplaty": {
"acc": "счёт", "nal": "нал", "zachet": "зачёт"}
, "subjects0": {
"articles": "articles", "blanks": "blanks", "calendar_rf": "calendar_rf", "calendar_site": "calendar_site", "fields": "fields", "files": "files", "help": "help", "legends": "legends", "messages": "messages", "poll": "poll", "poll_form": "poll_form", "privates": "privates", "requests": "requests", "resume": "resume", "rubr": "rubr", "schema": "schema", "sites": "sites", "spiski": "spiski", "subjects": "subjects", "tips": "tips", "users": "users"}
, "path_div": ":", "uri_prefix": "/present.ru"}
dict.sites = {};
dict.sites = {
"public": {
"name": "public", "path": "/", "js": "/site.js", "css": "/public.css", "title": "В начало", "icon": "i__public.png", "metka": "1"}
, "data": {
"name": "data", "path": "/data/", "js": "/construct.js;/edit.js", "css": "/edit.css", "title": "Данные", "icon": "i__data.png", "metka": "1"}
, "constructor": {
"name": "constructor", "action": "View:build_constructor_menu", "path": "/constructor/", "js": "/construct.js;/edit.js", "css": "/edit.css", "title": "Конструктор", "icon": "i__constructor.png", "metka": "1"}
}
dict.subjects = {};
dict.subjects = {
"subjects": {
"table": "subjects", "type": "Xml", "name": "subjects", "title": "Метаданные", "file": "subjects.xml", "show_flag": "name", "status_w": "2", "status_r": "1", "rows": "1", "flag": "", "add": "", "view": "", "menu": "", "action": "", "js": "", "css": "", "metka": "", "icon": "", "order": ""}
, "privates": {
"table": "privates", "type": "Xml", "name": "privates", "title": "Настройки", "menu": "88", "file": "privates.xml", "status_w": "2", "rows": "1", "metka": "88", "icon": "i__new.png", "show_flag": "ru", "flag": "option", "add": "", "view": "", "action": "", "js": "", "css": "", "status_r": "", "order": ""}
, "write": {
"name": "write", "title": "Запись Данных", "view": "void", "action": "Write:write", "status_w": "1", "status_r": "1", "table": "", "type": "", "flag": "", "add": "", "menu": "", "file": "", "show_flag": "", "js": "", "css": "", "rows": "", "metka": "", "icon": "", "order": ""}
, "fields": {
"table": "fields", "type": "Xml", "name": "fields", "title": "Описания полей", "file": "fields.xml", "show_flag": "name", "status_w": "2", "status_r": "1", "rows": "1", "flag": "table", "add": "", "view": "", "menu": "", "action": "", "js": "", "css": "", "metka": "", "icon": "", "order": ""}
, "js_unload": {
"type": "void", "name": "js_unload", "title": "Выгрузить js-словари", "view": "void", "action": "js_unload", "metka": "55", "status_w": "1", "status_r": "1", "table": "", "flag": "", "add": "", "menu": "", "file": "", "show_flag": "", "js": "", "css": "", "rows": "", "icon": "", "order": ""}
, "help": {
"table": "help", "type": "Xml", "name": "help", "title": "Справка", "menu": "77", "file": "help.xml", "status_w": "2", "rows": "1", "metka": "77", "icon": "i__new.png", "show_flag": "ru", "flag": "", "add": "", "view": "", "action": "", "js": "", "css": "", "status_r": "", "order": ""}
, "delete": {
"type": "void", "name": "delete", "title": "Удалить запись", "view": "void", "action": "Write:delete", "status_w": "1", "status_r": "1", "table": "", "flag": "", "add": "", "menu": "", "file": "", "show_flag": "", "js": "", "css": "", "rows": "", "metka": "", "icon": "", "order": ""}
, "sites": {
"table": "sites", "type": "Xml", "name": "sites", "title": "Сайты", "menu": "88", "file": "sites.xml", "show_flag": "name", "status_w": "3", "status_r": "3", "rows": "1", "metka": "88", "icon": "i__new.png", "flag": "", "add": "", "view": "", "action": "", "js": "", "css": "", "order": ""}
, "create_table": {
"type": "void", "name": "create_table", "title": "Создать таблицу", "view": "void", "action": "Write:create_table", "status_w": "2", "status_r": "2", "table": "", "flag": "", "add": "", "menu": "", "file": "", "show_flag": "", "js": "", "css": "", "rows": "", "metka": "", "icon": "", "order": ""}
, "users": {
"table": "users", "type": "Xml", "name": "users", "title": "Пользователи", "menu": "88", "file": "users.xml", "show_flag": "name", "status_w": "3", "status_r": "3", "rows": "1", "metka": "88", "icon": "i__new.png", "flag": "", "add": "", "view": "", "action": "", "js": "", "css": "", "order": ""}
, "auth": {
"type": "void", "name": "auth", "title": "Авторизация", "view": "void", "action": "Command:auth", "status_w": "1", "status_r": "1", "table": "", "flag": "", "add": "", "menu": "", "file": "", "show_flag": "", "js": "", "css": "", "rows": "", "metka": "", "icon": "", "order": ""}
, "articles": {
"table": "articles", "type": "Mysql", "name": "articles", "title": "Страницы (статьи)", "add": "messages", "menu": "77", "file": "articles", "status_w": "1", "rows": "1", "metka": "77", "icon": "i__articles.png", "show_flag": "filename", "flag": "", "view": "", "action": "", "js": "", "css": "", "status_r": "", "order": ""}
, "messages": {
"table": "messages", "type": "Mysql", "name": "messages", "title": "Сообщения", "flag": "topic", "menu": "77", "file": "messages", "rows": "1", "metka": "77", "icon": "i__messages.png", "add": "", "view": "", "action": "", "show_flag": "", "js": "", "css": "", "status_r": "", "status_w": "", "order": ""}
, "files": {
"table": "files", "type": "Mysql", "name": "files", "title": "Файлы", "flag": "topic", "menu": "77", "file": "files", "status_w": "1", "rows": "1", "metka": "77", "icon": "i__files.png", "js": ":addFileField", "show_flag": "id", "add": "", "view": "", "action": "", "css": "", "status_r": "", "order": ""}
, "js_list": {
"type": "void", "name": "js_list", "title": "Список разделов ajax", "view": "void", "action": "js_list", "status_w": "1", "table": "", "flag": "", "add": "", "menu": "", "file": "", "show_flag": "", "js": "", "css": "", "status_r": "", "rows": "", "metka": "", "icon": "", "order": ""}
, "save_sql": {
"name": "save_sql", "title": "Сохранить данные в sql файл", "view": "void", "action": "Command:save_sql", "status_w": "1", "table": "", "type": "", "flag": "", "add": "", "menu": "", "file": "", "show_flag": "", "js": "", "css": "", "status_r": "", "rows": "", "metka": "", "icon": "", "order": ""}
, "add_sql": {
"name": "add_sql", "title": "Загрузить данные из sql файла", "view": "void", "action": "Write:add_sql", "status_w": "2", "table": "", "type": "", "flag": "", "add": "", "menu": "", "file": "", "show_flag": "", "js": "", "css": "", "status_r": "", "rows": "", "metka": "", "icon": "", "order": ""}
, "legends": {
"table": "legends", "type": "Xml", "name": "legends", "title": "Легенды", "menu": "88", "file": "legends.xml", "show_flag": "name", "status_w": "2", "rows": "1", "metka": "88", "icon": "i__new.png", "flag": "table", "add": "", "view": "", "action": "", "js": "", "css": "", "status_r": "", "order": ""}
, "rubr": {
"table": "rubr", "type": "Mysql", "name": "rubr", "title": "Рубрики каталога", "menu": "88", "file": "rubr", "status_w": "1", "rows": "1", "metka": "88", "icon": "i__new.png", "add": "rubr_links", "flag": "", "view": "", "action": "", "show_flag": "", "js": "", "css": "", "status_r": "", "order": ""}
, "rubr_reindex": {
"type": "void", "name": "rubr_reindex", "title": "Переиндексировать рубрики каталога", "view": "void", "action": "Write:rubr_reindex", "metka": "55", "status_w": "2", "status_r": "2", "table": "", "flag": "", "add": "", "menu": "", "file": "", "show_flag": "", "js": "", "css": "", "rows": "", "icon": "", "order": ""}
, "void": {
"name": "void", "status_w": "2", "title": "Пустота", "view": "void", "table": "", "type": "", "flag": "", "add": "", "menu": "", "action": "", "file": "", "show_flag": "", "js": "", "css": "", "status_r": "", "rows": "", "metka": "", "icon": "", "order": ""}
, "blanks": {
"table": "blanks", "type": "Xml", "name": "blanks", "title": "Шаблоны", "menu": "77", "file": "blanks.xml", "show_flag": "name", "status_w": "2", "status_r": "2", "rows": "1", "metka": "77", "icon": "i__new.png", "flag": "", "add": "", "view": "", "action": "", "js": "", "css": "", "order": ""}
, "submit_order": {
"name": "submit_order", "title": "Отправить заказ", "view": "void", "action": "Write:submit_order", "status_r": "1", "table": "", "type": "", "flag": "", "add": "", "menu": "", "file": "", "show_flag": "", "js": "", "css": "", "status_w": "", "rows": "", "metka": "", "icon": "", "order": ""}
, "poll": {
"table": "poll", "type": "Mysql", "name": "poll", "title": "Голосования (таблица)", "rows": "1", "metka": "1", "file": "poll", "flag": "", "add": "", "view": "", "menu": "", "action": "", "show_flag": "", "js": "", "css": "", "status_r": "", "status_w": "", "icon": "", "order": ""}
, "to_vote": {
"type": "void", "name": "to_vote", "title": "Голосовать", "view": "void", "action": "Write:to_vote", "status_r": "1", "table": "", "flag": "", "add": "", "menu": "", "file": "", "show_flag": "", "js": "", "css": "", "status_w": "", "rows": "", "metka": "", "icon": "", "order": ""}
, "poll_form": {
"table": "poll_form", "type": "Xml", "name": "poll_form", "title": "Голосования", "rows": "1", "metka": "77", "file": "poll_form.xml", "menu": "77", "status_w": "2", "icon": "i__new.png", "flag": "", "add": "", "view": "", "action": "", "show_flag": "", "js": "", "css": "", "status_r": "", "order": ""}
, "js_form": {
"type": "void", "name": "js_form", "title": "ajax-форма", "view": "void", "action": "js_form", "table": "", "flag": "", "add": "", "menu": "", "file": "", "show_flag": "", "js": "", "css": "", "status_r": "", "status_w": "", "rows": "", "metka": "", "icon": "", "order": ""}
, "js_get_files": {
"type": "void", "name": "js_get_files", "title": "Список файлов ajax", "view": "void", "action": "js_get_files", "status_w": "1", "table": "", "flag": "", "add": "", "menu": "", "file": "", "show_flag": "", "js": "", "css": "", "status_r": "", "rows": "", "metka": "", "icon": "", "order": ""}
, "rethumb": {
"type": "void", "name": "rethumb", "title": "Создать миниатюру заново", "view": "void", "action": "rethumb", "status_w": "1", "table": "", "flag": "", "add": "", "menu": "", "file": "", "show_flag": "", "js": "", "css": "", "status_r": "", "rows": "", "metka": "", "icon": "", "order": ""}
, "js_get_fragment": {
"type": "void", "name": "js_get_fragment", "title": "ajax-фрагмент", "view": "void", "action": "js_get_fragment", "table": "", "flag": "", "add": "", "menu": "", "file": "", "show_flag": "", "js": "", "css": "", "status_r": "", "status_w": "", "rows": "", "metka": "", "icon": "", "order": ""}
, "ajax": {
"table": "texts", "type": "View", "name": "ajax", "title": "Ajax (форма?)", "view": "ajax", "action": "ajax", "status_w": "1", "flag": "", "add": "", "menu": "", "file": "", "show_flag": "", "js": "", "css": "", "status_r": "", "rows": "", "metka": "", "icon": "", "order": ""}
, "spiski": {
"table": "spiski", "type": "Xml", "name": "spiski", "title": "Списки", "menu": "88", "file": "spiski.xml", "status_w": "2", "rows": "1", "metka": "88", "icon": "i__new.png", "show_flag": "ru", "flag": "spisok", "add": "", "view": "", "action": "", "js": "", "css": "", "status_r": "", "order": ""}
, "tips": {
"table": "tips", "type": "Xml", "name": "tips", "title": "Напоминания", "menu": "88", "file": "tips.xml", "status_w": "2", "rows": "1", "metka": "88", "icon": "i__new.png", "show_flag": "ru", "flag": "", "add": "", "view": "", "action": "", "js": "", "css": "", "status_r": "", "order": ""}
, "calendar_rf": {
"table": "calendar_rf", "type": "Xml", "name": "calendar_rf", "title": "Календарь РФ", "menu": "88", "file": "calendar_rf.xml", "status_w": "2", "rows": "1", "metka": "88", "icon": "i__new.png", "show_flag": "value", "flag": "", "add": "", "view": "", "action": "", "js": "", "css": "", "status_r": "", "order": ""}
, "separate_model": {
"type": "void", "name": "separate_model", "title": "Отделить модель сайта от библиотечной", "view": "void", "action": "Write:separate_model", "metka": "55", "table": "", "flag": "", "add": "", "menu": "", "file": "", "show_flag": "", "js": "", "css": "", "status_r": "", "status_w": "", "rows": "", "icon": "", "order": ""}
, "calendar_site": {
"table": "calendar_site", "type": "Xml", "name": "calendar_site", "title": "Календарь сайта", "file": "calendar_site.xml", "status_w": "2", "rows": "1", "icon": "i__new.png", "show_flag": "value", "view": "void", "flag": "", "add": "", "menu": "", "action": "", "js": "", "css": "", "status_r": "", "metka": "", "order": ""}
, "profiler": {
"name": "profiler", "title": "Выдать js-profiler", "view": "void", "status_w": "1", "status_r": "3", "action": "js_profiler", "table": "", "type": "", "flag": "", "add": "", "menu": "", "file": "", "show_flag": "", "js": "", "css": "", "rows": "", "metka": "", "icon": "", "order": ""}
, "resume": {
"table": "resume", "type": "Mysql", "name": "resume", "title": "Резюме", "menu": "77", "file": "resume", "rows": "1", "metka": "77", "icon": "i__new.png", "status_r": "1", "flag": "", "add": "", "view": "", "action": "", "show_flag": "", "js": "", "css": "", "status_w": "", "order": ""}
, "drop_tables": {
"type": "void", "name": "drop_tables", "title": "Удвлить sql-таблицы", "view": "void", "action": "Write:drop_tables", "status_r": "3", "status_w": "3", "metka": "55", "table": "", "flag": "", "add": "", "menu": "", "file": "", "show_flag": "", "js": "", "css": "", "rows": "", "icon": "", "order": ""}
, "schema": {
"table": "schema", "type": "Xml", "name": "schema", "title": "schema", "flag": "", "add": "", "view": "", "menu": "", "action": "", "file": "schema.xml", "show_flag": "name", "js": "", "css": "", "status_r": "3", "status_w": "3", "rows": "1", "metka": "77", "icon": "i__new.png", "order": ""}
, "write_bool": {
"table": "", "type": "", "name": "write_bool", "title": "Запись в поле (ajax)", "flag": "", "add": "", "view": "void", "menu": "", "action": "Write:write_bool", "file": "", "show_flag": "", "js": "", "css": "", "status_r": "1", "status_w": "1", "rows": "", "metka": "", "icon": "", "order": ""}
, "SE_word": {
"type": "void", "name": "SE_word", "title": "Поисковый запрос", "view": "void", "action": "SE_word", "status_w": "1", "table": "", "flag": "", "add": "", "menu": "", "file": "", "show_flag": "", "js": "", "css": "", "status_r": "", "rows": "", "metka": "", "icon": "", "order": ""}
, "def2php": {
"name": "def2php", "title": "Выгрузить модель в php", "view": "void", "action": "Command:def2php", "metka": "55", "status_w": "1", "status_r": "3", "table": "", "type": "", "flag": "", "add": "", "menu": "", "file": "", "show_flag": "", "js": "", "css": "", "rows": "", "icon": "", "order": ""}
, "files2bd": {
"name": "files2bd", "title": "Поднять потерянные файлы", "view": "void", "action": "Write:files2bd", "metka": "55", "status_w": "1", "status_r": "1", "table": "", "type": "", "flag": "", "add": "", "menu": "", "file": "", "show_flag": "", "js": "", "css": "", "rows": "", "icon": "", "order": ""}
, "add_fields": {
"name": "add_fields", "title": "Добавить в fields новые из .csv", "view": "void", "action": "Write:add_fields", "metka": "55", "status_w": "3", "status_r": "3", "table": "", "type": "", "flag": "", "add": "", "menu": "", "file": "", "show_flag": "", "js": "", "css": "", "rows": "", "icon": "", "order": ""}
, "add_subjects": {
"name": "add_subjects", "title": "Добавить в модель новые из .xml", "view": "void", "action": "Write:add_subjects", "metka": "55", "status_w": "3", "status_r": "3", "table": "", "type": "", "flag": "", "add": "", "menu": "", "file": "", "show_flag": "", "js": "", "css": "", "rows": "", "icon": "", "order": ""}
, "clear_fields": {
"type": "void", "name": "clear_fields", "title": "Синхронизировать поля с сущностями", "view": "void", "action": "Write:clear_fields", "metka": "55", "status_w": "1", "status_r": "1", "table": "", "flag": "", "add": "", "menu": "", "file": "", "show_flag": "", "js": "", "css": "", "rows": "", "icon": "", "order": ""}
, "corr_data": {
"table": "texts", "name": "corr_data", "title": "Коррекция Данных", "view": "corr_data", "action": "corr_data", "status_w": "1", "type": "", "flag": "", "add": "", "menu": "", "file": "", "show_flag": "", "js": "", "css": "", "status_r": "", "rows": "", "metka": "", "icon": "", "order": ""}
, "requests": {
"table": "requests", "type": "Mysql", "name": "requests", "title": "Заказы (внешние)", "flag": "", "add": "", "view": "", "menu": "77", "action": "", "file": "requests", "show_flag": "", "js": "", "css": "", "status_r": "", "status_w": "", "rows": "1", "metka": "77", "icon": "i__new.png", "order": ""}
, "add_presents": {
"table": "", "type": "void", "name": "add_presents", "title": "add_presents", "flag": "", "add": "", "view": "void", "menu": "", "action": "add_presents", "file": "", "show_flag": "", "js": "", "css": "", "status_r": "", "status_w": "", "rows": "", "metka": "", "icon": "", "order": ""}
}
dict.fields = {};
dict.fields = {
"subjects": {
"table": {
"table": "subjects", "name": "table", "type": "varchar(30)", "list": "1", "ru": "", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "type": {
"table": "subjects", "name": "type", "type": "varchar(30)", "list": "1", "ru": "", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "name": {
"table": "subjects", "name": "name", "type": "varchar(30)", "ru": "Имя", "key": "1", "list": "1", "link": "1", "php": "addext", "mtime": "", "metka": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "title": {
"table": "subjects", "name": "title", "type": "varchar(30)", "list": "1", "ru": "", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "flag": {
"table": "subjects", "name": "flag", "type": "varchar(30)", "list": "1", "ru": "", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "add": {
"table": "subjects", "name": "add", "type": "varchar(255)", "list": "1", "ru": "", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "view": {
"table": "subjects", "name": "view", "type": "varchar(30)", "list": "1", "ru": "", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "menu": {
"table": "subjects", "name": "menu", "type": "tinyint(3)", "list": "1", "ru": "", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "action": {
"table": "subjects", "name": "action", "type": "varchar(30)", "list": "1", "ru": "", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "file": {
"table": "subjects", "name": "file", "type": "varchar(50)", "list": "1", "ru": "", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "show_flag": {
"table": "subjects", "name": "show_flag", "type": "varchar(30)", "list": "1", "ru": "", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "js": {
"table": "subjects", "name": "js", "type": "varchar(50)", "list": "1", "ru": "", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "css": {
"table": "subjects", "name": "css", "type": "varchar(50)", "list": "1", "ru": "", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "status_r": {
"table": "subjects", "name": "status_r", "type": "tinyint(3)", "list": "1", "ru": "", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "status_w": {
"table": "subjects", "name": "status_w", "type": "tinyint(3)", "list": "1", "ru": "", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "rows": {
"table": "subjects", "name": "rows", "type": "tinyint(1)", "list": "1", "ru": "", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "metka": {
"table": "subjects", "name": "metka", "type": "tinyint(3)", "list": "1", "ru": "", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "icon": {
"table": "subjects", "name": "icon", "type": "varchar(50)", "list": "1", "ru": "", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "order": {
"table": "subjects", "name": "order", "type": "varchar(150)", "list": "1", "ru": "", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
}
, "fields": {
"table": {
"table": "fields", "name": "table", "type": "varchar(30)", "key": "1", "list": "1", "link": "table", "mtime": "2012-07-22 13:22:32", "ru": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "name": {
"table": "fields", "name": "name", "type": "varchar(30)", "key": "1", "list": "1", "link": "1", "ru": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "type": {
"table": "fields", "name": "type", "type": "varchar(255)", "list": "1", "ru": "", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "ru": {
"table": "fields", "name": "ru", "type": "varchar(30)", "list": "1", "ru": "", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "key": {
"table": "fields", "name": "key", "type": "tinyint(1)", "list": "1", "ru": "", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "list": {
"table": "fields", "name": "list", "type": "tinyint(1)", "list": "1", "ru": "", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "link": {
"table": "fields", "name": "link", "type": "varchar(30)", "list": "1", "ru": "", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "mtime": {
"table": "fields", "name": "mtime", "type": "timestamp", "list": "1", "default": "NOT NULL default CURRENT_TIMESTAMP", "extra": "on update CURRENT_TIMESTAMP", "ru": "", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "metka": {
"table": "fields", "name": "metka", "type": "tinyint(3)", "list": "1", "ru": "", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "php": {
"table": "fields", "name": "php", "type": "varchar(99)", "list": "1", "ru": "", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "default": {
"table": "fields", "name": "default", "type": "varchar(30)", "list": "1", "default": "not null", "ru": "", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "extra": {
"table": "fields", "name": "extra", "type": "varchar(50)", "list": "1", "ru": "", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "html": {
"table": "fields", "name": "html", "type": "tinyint(1)", "list": "1", "default": "NOT NULL", "ru": "", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "source": {
"table": "fields", "name": "source", "type": "varchar(50)", "list": "1", "js": "buildSourceLink", "ru": "", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "order": "", "section": ""}
, "js": {
"table": "fields", "name": "js", "type": "varchar(255)", "list": "1", "ru": "", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "order": {
"table": "fields", "name": "order", "type": "int(3)", "list": "1", "mtime": "2012-07-22 13:13:19", "ru": "", "key": "", "link": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "section": {
"table": "fields", "name": "section", "type": "varchar(30)", "list": "1", "mtime": "2012-07-22 13:24:01", "ru": "", "key": "", "link": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
}
, "sites": {
"name": {
"table": "sites", "name": "name", "type": "varchar(30)", "key": "1", "link": "1", "list": "1", "ru": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "action": {
"table": "sites", "name": "action", "type": "varchar(50)", "list": "1", "ru": "", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "path": {
"table": "sites", "name": "path", "type": "varchar(50)", "list": "1", "link": "1", "ru": "", "key": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "js": {
"table": "sites", "name": "js", "type": "varchar(50)", "list": "1", "ru": "", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "css": {
"table": "sites", "name": "css", "type": "varchar(50)", "list": "1", "ru": "", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "title": {
"table": "sites", "name": "title", "type": "varchar(50)", "list": "1", "ru": "", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "icon": {
"table": "sites", "name": "icon", "type": "varchar(50)", "list": "1", "ru": "", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
}
, "users": {
"name": {
"table": "users", "name": "name", "type": "varchar(30)", "ru": "Имя", "key": "1", "list": "1", "link": "1", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "fullname": {
"table": "users", "name": "fullname", "type": "varchar(30)", "ru": "", "key": "", "list": "", "link": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "password": {
"table": "users", "name": "password", "type": "varchar(34)", "ru": "Пароль", "list": "1", "php": "md6", "key": "", "link": "", "mtime": "", "metka": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "status": {
"table": "users", "name": "status", "type": "tinyint(3)", "ru": "", "key": "", "list": "", "link": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "mtime": {
"table": "users", "name": "mtime", "type": "timestamp", "default": "CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP", "ru": "", "key": "", "list": "", "link": "", "mtime": "", "metka": "", "php": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "email": {
"table": "users", "name": "email", "type": "varchar(100)", "ru": "", "key": "", "list": "", "link": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
}
, "articles": {
"filename": {
"table": "articles", "name": "filename", "type": " varchar(100)", "ru": "Адрес страницы", "key": "1", "list": "1", "default": " NOT NULL", "source": "js:title:urlTr", "mtime": "2012-09-21 09:03:48", "php": "addext", "order": "10", "link": "1", "metka": "", "extra": "", "html": "", "js": "", "section": ""}
, "title": {
"table": "articles", "name": "title", "type": " varchar(255)", "ru": "Заголовок страницы", "list": "1", "link": "1", "default": " NOT NULL", "order": "20", "key": "", "mtime": "", "metka": "", "php": "", "extra": "", "html": "", "source": "", "js": "", "section": ""}
, "tpl": {
"table": "articles", "name": "tpl", "type": " varchar(30)", "ru": "Шаблон", "list": "1", "default": " NOT NULL DEFAULT \"logo\"", "source": "config:tpls", "order": "30", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "extra": "", "html": "", "js": "", "section": ""}
, "parent": {
"table": "articles", "name": "parent", "type": "varchar(99)", "ru": "Раздел", "list": "1", "default": "NULL", "source": "ajax:addListEvent:articles", "key": "3", "mtime": "2012-11-15 20:03:54", "php": "addext", "order": "40", "link": "", "metka": "", "extra": "", "html": "", "js": "", "section": ""}
, "multi": {
"table": "articles", "name": "multi", "type": " varchar(99)", "ru": "Мультиподарок", "key": "5", "list": "1", "link": "", "mtime": "2012-11-15 20:03:39", "metka": "", "php": "addext", "default": "NULL", "extra": "", "html": "", "source": "ajax:addListEvent:articles", "js": "", "order": "44", "section": ""}
, "docdate": {
"table": "articles", "name": "docdate", "type": " date", "ru": "Дата док.", "default": " NOT NULL", "order": "50", "mtime": "2012-10-03 10:54:31", "php": "date_ru", "key": "", "list": "", "link": "", "metka": "", "extra": "", "html": "", "source": "", "js": "", "section": ""}
, "order": {
"table": "articles", "name": "order", "type": "smallint(4)", "ru": "Место", "default": " NOT NULL DEFAULT \"1\"", "key": "3", "order": "60", "mtime": "2012-10-22 11:14:05", "list": "1", "link": "", "metka": "", "php": "", "extra": "", "html": "", "source": "", "js": "", "section": ""}
, "metka": {
"table": "articles", "name": "metka", "type": " tinyint(3)", "ru": "Меню", "default": " NOT NULL DEFAULT \"1\"", "key": "5", "list": "1", "order": "65", "link": "", "mtime": "", "metka": "", "php": "", "extra": "", "html": "", "source": "", "js": "", "section": ""}
, "group": {
"table": "articles", "name": "group", "type": "int(5)", "ru": "Группа", "default": "NOT NULL DEFAULT '31'", "mtime": "2012-09-23 19:25:04", "order": "70", "js": "addGroupBoxes", "key": "", "list": "", "link": "", "metka": "", "php": "", "extra": "", "html": "", "source": "", "section": ""}
, "img": {
"table": "articles", "name": "img", "type": " varchar(55)", "ru": "Картинка", "list": "1", "default": "NULL", "source": "ajax:addListEvent:files", "mtime": "2012-11-03 00:43:32", "order": "75", "key": "", "link": "", "metka": "", "php": "", "extra": "", "html": "", "js": "", "section": ""}
, "price": {
"table": "articles", "name": "price", "type": "float", "ru": "Цена", "default": " NOT NULL DEFAULT '9999'", "mtime": "2012-09-18 14:44:35", "order": "80", "key": "", "list": "", "link": "", "metka": "", "php": "", "extra": "", "html": "", "source": "", "js": "", "section": ""}
, "color": {
"table": "articles", "name": "color", "type": " varchar(30)", "ru": "Цвет ссылки", "default": " NOT NULL", "order": "85", "list": "", "mtime": "2012-10-18 19:35:18", "key": "", "link": "", "metka": "", "php": "", "extra": "", "html": "", "source": "", "js": "", "section": ""}
, "icon": {
"table": "articles", "name": "icon", "type": " varchar(30)", "ru": "Иконка меню", "default": " NOT NULL", "order": "90", "key": "", "list": "", "link": "", "mtime": "", "metka": "", "php": "", "extra": "", "html": "", "source": "", "js": "", "section": ""}
, "counter": {
"table": "articles", "name": "counter", "type": " int(12)", "ru": "counter", "default": " NOT NULL", "order": "100", "key": "", "list": "", "link": "", "mtime": "", "metka": "", "php": "", "extra": "", "html": "", "source": "", "js": "", "section": ""}
, "css": {
"table": "articles", "name": "css", "type": " varchar(255)", "ru": "Стили .css", "default": " NOT NULL", "order": "110", "key": "", "list": "", "link": "", "mtime": "", "metka": "", "php": "", "extra": "", "html": "", "source": "", "js": "", "section": ""}
, "js": {
"table": "articles", "name": "js", "type": " varchar(255)", "ru": "Скрипты .js", "default": " NOT NULL", "order": "120", "key": "", "list": "", "link": "", "mtime": "", "metka": "", "php": "", "extra": "", "html": "", "source": "", "js": "", "section": ""}
, "mtime": {
"table": "articles", "name": "mtime", "type": " timestamp", "ru": "Дата изменения", "default": "NULL DEFAULT CURRENT_TIMESTAMP", "extra": "on update CURRENT_TIMESTAMP", "key": "4", "order": "130", "list": "", "mtime": "2012-10-03 21:15:55", "link": "", "metka": "", "php": "", "html": "", "source": "", "js": "", "section": ""}
, "seokeywords": {
"table": "articles", "name": "seokeywords", "type": " varchar(255)", "ru": "seokeywords", "default": " NOT NULL", "order": "190", "key": "", "list": "", "link": "", "mtime": "", "metka": "", "php": "", "extra": "", "html": "", "source": "", "js": "", "section": ""}
, "seotitle": {
"table": "articles", "name": "seotitle", "type": " varchar(255)", "ru": "seotitle", "default": " NOT NULL", "order": "200", "mtime": "2012-09-18 14:45:16", "key": "", "list": "", "link": "", "metka": "", "php": "", "extra": "", "html": "", "source": "", "js": "", "section": ""}
, "seodescr": {
"table": "articles", "name": "seodescr", "type": " text", "ru": "Анонс", "default": " NOT NULL", "order": "210", "mtime": "2012-10-22 11:14:37", "list": "", "key": "", "link": "", "metka": "", "php": "", "extra": "", "html": "", "source": "", "js": "", "section": ""}
, "spec": {
"table": "articles", "name": "spec", "type": " text", "ru": "Спецификация", "key": "", "list": "", "link": "", "mtime": "2012-11-07 20:43:21", "metka": "", "php": "", "default": "NOT NULL", "extra": "", "html": "1", "source": "", "js": "", "order": "220", "section": ""}
, "reklama": {
"table": "articles", "name": "reklama", "type": " text", "ru": "Реклама", "key": "", "list": "", "link": "", "mtime": "2012-11-07 20:43:00", "metka": "", "php": "", "default": "NOT NULL", "extra": "", "html": "", "source": "", "js": "", "order": "230", "section": ""}
, "text": {
"table": "articles", "name": "text", "type": " mediumtext", "ru": "Текст страницы", "html": "1", "order": "250", "key": "", "list": "", "link": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "source": "", "js": "", "section": ""}
}
, "help": {
"title": {
"table": "help", "name": "title", "type": " varchar(255)", "ru": "Заголовок", "list": "1", "link": "1", "default": " NOT NULL", "mtime": "2012-10-30 13:37:43", "order": "10", "key": "", "metka": "", "php": "", "extra": "", "html": "", "source": "", "js": "", "section": ""}
, "text": {
"table": "help", "name": "text", "type": " mediumtext", "ru": "Текст", "html": "1", "list": "1", "mtime": "2012-10-30 13:38:01", "order": "20", "key": "", "link": "", "metka": "", "php": "", "default": "", "extra": "", "source": "", "js": "", "section": ""}
, "name": {
"table": "help", "name": "name", "type": " varchar(100)", "ru": "Адрес", "key": "1", "list": "1", "default": " NOT NULL", "source": "js:title:urlTr", "mtime": "2012-10-30 13:38:20", "order": "30", "link": "", "metka": "", "php": "", "extra": "", "html": "", "js": "", "section": ""}
, "parent": {
"table": "help", "name": "parent", "type": " varchar(100)", "ru": "Раздел", "list": "1", "default": " NOT NULL DEFAULT \"index.htm\"", "source": "ajax:addListEvent:help", "key": "3", "mtime": "2012-10-30 13:38:44", "order": "40", "link": "", "metka": "", "php": "", "extra": "", "html": "", "js": "", "section": ""}
, "metka": {
"table": "help", "name": "metka", "type": " tinyint(3)", "ru": "Статус", "default": " NOT NULL DEFAULT \"1\"", "key": "5", "list": "1", "mtime": "2012-10-30 13:38:57", "order": "50", "link": "", "metka": "", "php": "", "extra": "", "html": "", "source": "", "js": "", "section": ""}
, "order": {
"table": "help", "name": "order", "type": "smallint(3)", "ru": "Порядок", "list": "1", "default": " NOT NULL", "mtime": "2012-10-30 13:40:13", "order": "55", "key": "", "link": "", "metka": "", "php": "", "extra": "", "html": "", "source": "", "js": "", "section": ""}
, "mtime": {
"table": "help", "name": "mtime", "type": " timestamp", "ru": "Дата изменения", "default": "NULL DEFAULT CURRENT_TIMESTAMP", "extra": "on update CURRENT_TIMESTAMP", "key": "4", "mtime": "2012-10-30 13:39:15", "order": "60", "list": "", "link": "", "metka": "", "php": "", "html": "", "source": "", "js": "", "section": ""}
}
, "messages": {
"id": {
"table": "messages", "name": "id", "type": "int(6)", "key": "1", "default": "NOT NULL", "extra": "auto_increment", "list": "1", "mtime": "2012-10-03 11:20:07", "link": "1", "ru": "", "metka": "", "php": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "name": {
"table": "messages", "name": "name", "type": "varchar(30)", "ru": "Имя", "list": "1", "default": "NOT NULL", "link": "1", "mtime": "2012-10-03 11:20:14", "key": "", "metka": "", "php": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "email": {
"table": "messages", "name": "email", "type": "varchar(55)", "ru": "Почта", "list": "1", "default": "NOT NULL", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "text": {
"table": "messages", "name": "text", "type": "text", "ru": "Текст сообщения", "list": "1", "default": "NOT NULL", "html": "1", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "extra": "", "source": "", "js": "", "order": "", "section": ""}
, "topic": {
"table": "messages", "name": "topic", "type": "varchar(30)", "list": "1", "link": "1", "default": "NOT NULL", "ru": "", "key": "", "mtime": "", "metka": "", "php": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "metka": {
"table": "messages", "name": "metka", "type": "tinyint(3)", "default": "NOT NULL default \"1\"", "ru": "", "key": "", "list": "", "link": "", "mtime": "", "metka": "", "php": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "mtime": {
"table": "messages", "name": "mtime", "type": "timestamp", "default": "NOT NULL default CURRENT_TIMESTAMP", "extra": "on update CURRENT_TIMESTAMP", "ru": "", "key": "", "list": "", "link": "", "mtime": "", "metka": "", "php": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
}
, "files": {
"id": {
"table": "files", "name": "id", "type": "varchar(20)", "ru": "Файл", "key": "1", "list": "1", "link": "[files]", "default": "NOT NULL", "mtime": "2012-08-09 12:19:52", "order": "10", "metka": "", "php": "", "extra": "", "html": "", "source": "", "js": "", "section": ""}
, "descr": {
"table": "files", "name": "descr", "type": "text", "ru": "Описание", "list": "1", "default": "NOT NULL", "html": "1", "mtime": "2012-08-09 12:20:22", "order": "20", "key": "", "link": "", "metka": "", "php": "", "extra": "", "source": "", "js": "", "section": ""}
, "title": {
"table": "files", "name": "title", "type": "varchar(55)", "ru": "Наименование", "key": "3", "list": "1", "link": "1", "default": "NOT NULL", "mtime": "2012-08-09 12:20:35", "order": "30", "metka": "", "php": "", "extra": "", "html": "", "source": "", "js": "", "section": ""}
, "mtime": {
"table": "files", "name": "mtime", "type": "timestamp", "ru": "Время", "list": "1", "default": "NULL DEFAULT CURRENT_TIMESTAMP", "mtime": "2012-10-03 16:48:15", "order": "40", "extra": "on update CURRENT_TIMESTAMP", "key": "", "link": "", "metka": "", "php": "", "html": "", "source": "", "js": "", "section": ""}
, "topic": {
"table": "files", "name": "topic", "type": "varchar(99)", "ru": "Раздел", "key": "3", "list": "1", "default": "NULL", "source": "ajax:addListEvent:articles", "mtime": "2012-10-10 14:37:05", "php": "addext", "order": "50", "js": "", "link": "topic", "metka": "", "extra": "", "html": "", "section": ""}
, "ext": {
"table": "files", "name": "ext", "type": "varchar(5)", "ru": "Тип", "list": "1", "default": "NOT NULL default 'jpg'", "mtime": "2012-08-09 12:20:05", "order": "60", "key": "", "link": "", "metka": "", "php": "", "extra": "", "html": "", "source": "", "js": "", "section": ""}
, "group2": {
"table": "files", "name": "group2", "type": "varchar(30)", "ru": "Клиент", "default": "NOT NULL", "mtime": "2012-08-09 12:39:06", "order": "70", "key": "", "list": "", "link": "", "metka": "", "php": "", "extra": "", "html": "", "source": "", "js": "", "section": ""}
, "group1": {
"table": "files", "name": "group1", "type": "varchar(30)", "ru": "Рубрика", "list": "", "default": "NOT NULL", "mtime": "2012-08-09 12:38:58", "order": "80", "key": "", "link": "", "metka": "", "php": "", "extra": "", "html": "", "source": "", "js": "", "section": ""}
, "topic_index": {
"table": "files", "name": "topic_index", "type": "bigint(18)", "ru": "Индекс рубрики", "key": "4", "list": "", "default": "NOT NULL", "mtime": "2012-08-09 12:38:48", "order": "90", "link": "", "metka": "", "php": "", "extra": "", "html": "", "source": "", "js": "", "section": ""}
}
, "legends": {
"table": {
"table": "legends", "name": "table", "type": "varchar(30)", "key": "1", "list": "1", "ru": "", "link": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "id": {
"table": "legends", "name": "id", "type": "tinyint(3)", "list": "1", "default": "NOT NULL", "extra": "auto_increment", "ru": "", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "value": {
"table": "legends", "name": "value", "type": "varchar(30)", "list": "1", "link": "1", "ru": "", "key": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "name": {
"table": "legends", "name": "name", "type": "varchar(30)", "key": "1", "list": "1", "link": "1", "ru": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
}
, "rubr": {
"name": {
"table": "rubr", "name": "name", "type": "varchar(50)", "list": "1", "default": "not null", "key": "1", "link": "1", "ru": "", "mtime": "", "metka": "", "php": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "id": {
"table": "rubr", "name": "id", "type": "smallint(3)", "list": "1", "default": "not null", "key": "2", "extra": "auto_increment", "ru": "", "link": "", "mtime": "", "metka": "", "php": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "level": {
"table": "rubr", "name": "level", "type": "tinyint(1)", "list": "1", "default": "not null", "ru": "", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "parent": {
"table": "rubr", "name": "parent", "type": "varchar(50)", "list": "1", "default": "not null", "key": "2", "ru": "", "link": "", "mtime": "", "metka": "", "php": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "index": {
"table": "rubr", "name": "index", "type": "bigint(18)", "list": "1", "default": "not null", "key": "3", "ru": "", "link": "", "mtime": "", "metka": "", "php": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
}
, "blanks": {
"name": {
"table": "blanks", "name": "name", "type": "varchar(30)", "key": "1", "list": "1", "ru": "", "link": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "title": {
"table": "blanks", "name": "title", "type": "varchar(55)", "list": "1", "link": "1", "ru": "", "key": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "value": {
"table": "blanks", "name": "value", "type": "text", "html": "1", "ru": "", "key": "", "list": "", "link": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "source": "", "js": "", "order": "", "section": ""}
}
, "poll": {
"opt": {
"table": "poll", "name": "opt", "type": "tinyint(2)", "key": "3", "list": "1", "default": "NOT NULL", "ru": "", "link": "", "mtime": "", "metka": "", "php": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "topic": {
"table": "poll", "name": "topic", "type": "int(3)", "key": "1", "list": "1", "default": "NOT NULL", "ru": "", "link": "", "mtime": "", "metka": "", "php": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "iduser": {
"table": "poll", "name": "iduser", "type": "int(11)", "key": "1", "list": "1", "default": "NOT NULL", "extra": "auto_increment", "ru": "", "link": "", "mtime": "", "metka": "", "php": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
}
, "poll_form": {
"id": {
"table": "poll_form", "name": "id", "type": "int(3)", "key": "2", "list": "1", "ru": "", "link": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "name": {
"table": "poll_form", "name": "name", "type": "varchar(30)", "key": "1", "list": "1", "source": "js:title:strTr", "ru": "", "link": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "js": "", "order": "", "section": ""}
, "title": {
"table": "poll_form", "name": "title", "type": "varchar(255)", "list": "1", "ru": "Вопрос", "link": "1", "key": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "metka": {
"table": "poll_form", "name": "metka", "type": "tinyint(3)", "list": "1", "default": "NOT NULL defaul '1'", "ru": "Место на сайте", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "v1": {
"table": "poll_form", "name": "v1", "type": "varchar(30)", "list": "1", "ru": "", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "v2": {
"table": "poll_form", "name": "v2", "type": "varchar(30)", "list": "1", "ru": "", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "v3": {
"table": "poll_form", "name": "v3", "type": "varchar(30)", "list": "1", "ru": "", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "v4": {
"table": "poll_form", "name": "v4", "type": "varchar(30)", "list": "1", "ru": "", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "v5": {
"table": "poll_form", "name": "v5", "type": "varchar(30)", "list": "1", "ru": "", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "v6": {
"table": "poll_form", "name": "v6", "type": "varchar(30)", "list": "1", "ru": "", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "v7": {
"table": "poll_form", "name": "v7", "type": "varchar(30)", "list": "1", "ru": "", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "v8": {
"table": "poll_form", "name": "v8", "type": "varchar(30)", "list": "1", "ru": "", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "v9": {
"table": "poll_form", "name": "v9", "type": "varchar(30)", "list": "1", "ru": "", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
}
, "spiski": {
"spisok": {
"table": "spiski", "name": "spisok", "type": "varchar(30)", "key": "1", "list": "1", "link": "spisok", "order": "10", "ru": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "section": ""}
, "id": {
"table": "spiski", "name": "id", "type": "int(3)", "list": "1", "default": "NOT NULL", "extra": "auto_increment", "order": "1", "ru": "", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "html": "", "source": "", "js": "", "section": ""}
, "name": {
"table": "spiski", "name": "name", "type": "varchar(30)", "key": "1", "list": "1", "link": "1", "order": "5", "ru": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "section": ""}
, "value": {
"table": "spiski", "name": "value", "type": "varchar(255)", "list": "1", "link": "", "html": "2", "ru": "Значение", "order": "15", "mtime": "2012-08-17 15:19:46", "key": "", "metka": "", "php": "", "default": "", "extra": "", "source": "", "js": "", "section": ""}
}
, "tips": {
"name": {
"table": "tips", "name": "name", "type": "varchar(30)", "ru": "Код", "key": "1", "list": "1", "link": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "title": {
"table": "tips", "name": "title", "type": "varchar(30)", "ru": "Название", "list": "1", "link": "1", "key": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "subject": {
"table": "tips", "name": "subject", "type": "varchar(30)", "ru": "Предмет деятельности", "list": "1", "mtime": "2012-08-17 11:44:01", "source": "config:subjects0", "key": "", "link": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "js": "", "order": "", "section": ""}
, "date_field": {
"name": "date_field", "type": "varchar(30)", "ru": "Поле с датой", "list": "1", "table": "", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "agent": {
"table": "tips", "name": "agent", "type": "varchar(30)", "ru": "Исполнитель", "list": "1", "source": "ajax:addListEvent:users", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "js": "", "order": "", "section": ""}
, "offset": {
"table": "tips", "name": "offset", "type": "tinyint(3)", "ru": "Дни", "list": "1", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "sent": {
"table": "tips", "name": "sent", "type": "date", "ru": "Отправлено", "list": "1", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
}
, "privates": {
"option": {
"table": "privates", "name": "option", "type": "varchar(30)", "key": "1", "list": "1", "link": "option", "ru": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "id": {
"table": "privates", "name": "id", "type": "int(3)", "list": "1", "default": "NOT NULL", "extra": "auto_increment", "ru": "", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "name": {
"table": "privates", "name": "name", "type": "varchar(30)", "key": "1", "list": "1", "link": "1", "ru": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "value": {
"table": "privates", "name": "value", "type": "varchar(255)", "list": "1", "link": "1", "ru": "", "key": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
}
, "calendar_rf": {
"m": {
"table": "calendar_rf", "name": "m", "type": "int(2)", "ru": "Месяц", "key": "", "list": "1", "link": "", "mtime": "2012-11-16 13:21:41", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "10", "section": ""}
, "c": {
"table": "calendar_rf", "name": "c", "type": "int(2)", "ru": "Число", "key": "", "list": "1", "link": "", "mtime": "2012-11-16 13:21:54", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "20", "section": ""}
, "d": {
"table": "calendar_rf", "name": "d", "type": "varchar(3)", "ru": "День недели", "key": "", "list": "1", "link": "", "mtime": "2012-11-16 13:22:08", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "30", "section": ""}
, "rest": {
"table": "calendar_rf", "name": "rest", "type": "tinyint(1)", "ru": "Выходной", "key": "", "list": "1", "link": "", "mtime": "2012-11-16 13:22:20", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "40", "section": ""}
, "date": {
"table": "calendar_rf", "name": "date", "type": "date", "key": "2", "list": "1", "link": "1", "mtime": "2012-11-16 13:22:32", "order": "50", "ru": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "section": ""}
, "value": {
"table": "calendar_rf", "name": "value", "type": "varchar(255)", "list": "1", "mtime": "2012-11-16 13:22:45", "order": "60", "ru": "", "key": "", "link": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "section": ""}
, "descr": {
"table": "calendar_rf", "name": "descr", "type": "varchar(55)", "ru": "", "key": "", "list": "1", "link": "", "mtime": "2012-11-16 13:22:58", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "70", "section": ""}
, "name": {
"table": "calendar_rf", "name": "name", "type": "varchar(30)", "key": "1", "list": "1", "mtime": "2012-11-16 13:23:09", "order": "80", "ru": "", "link": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "section": ""}
}
, "calendar_site": {
"name": {
"table": "calendar_site", "name": "name", "type": "varchar(4)", "ru": "Неделя", "key": "1", "list": "", "link": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "dw1": {
"table": "calendar_site", "name": "mon", "type": "text", "ru": "Понедельник", "list": "1", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "dw2": {
"table": "calendar_site", "name": "tue", "type": "text", "ru": "Вторник", "list": "1", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "dw3": {
"table": "calendar_site", "name": "wed", "type": "text", "ru": "Среда", "list": "1", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "dw4": {
"table": "calendar_site", "name": "thu", "type": "text", "ru": "Четверг", "list": "1", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "dw5": {
"table": "calendar_site", "name": "fri", "type": "text", "ru": "Пятница", "list": "1", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "dw6": {
"table": "calendar_site", "name": "sat", "type": "text", "ru": "Суббота", "list": "1", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
, "dw7": {
"table": "calendar_site", "name": "sun", "type": "text", "ru": "Воскресенье", "list": "1", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
}
, "resume": {
"vakancie": {
"table": "resume", "name": "vakancie", "type": "varchar(55)", "ru": "Интересующая вас вакансия", "list": "1", "link": "1", "default": "NOT NULL", "html": "4", "order": "1", "section": "resume_person", "mtime": "2012-07-23 01:16:46", "key": "", "metka": "", "php": "", "extra": "", "source": "", "js": ""}
, "name1": {
"table": "resume", "name": "name1", "type": "varchar(30)", "ru": "Фамилия", "list": "1", "link": "1", "default": "NOT NULL", "html": "4", "order": "2", "section": "resume_person", "key": "", "mtime": "", "metka": "", "php": "", "extra": "", "source": "", "js": ""}
, "name2": {
"table": "resume", "name": "name2", "type": "varchar(30)", "ru": "Имя", "list": "1", "default": "NOT NULL", "html": "4", "order": "3", "section": "resume_person", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "extra": "", "source": "", "js": ""}
, "name3": {
"table": "resume", "name": "name3", "type": "varchar(30)", "ru": "Отчество", "list": "1", "default": "NOT NULL", "html": "4", "order": "4", "section": "resume_person", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "extra": "", "source": "", "js": ""}
, "date1": {
"table": "resume", "name": "date1", "type": "date", "ru": "Дата рождения", "list": "1", "php": "date_ru", "default": "NOT NULL", "html": "4", "order": "5", "section": "resume_person", "mtime": "2012-07-22 19:09:38", "key": "", "link": "", "metka": "", "extra": "", "source": "", "js": ""}
, "adr_prop": {
"table": "resume", "name": "adr_prop", "type": "varchar(99)", "ru": "Адрес прописки ", "list": "1", "default": "NOT NULL", "order": "6", "section": "resume_person", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "extra": "", "html": "", "source": "", "js": ""}
, "adr_fact": {
"table": "resume", "name": "adr_fact", "type": "varchar(99)", "ru": "Адрес фактического места жительства ", "list": "1", "default": "NOT NULL", "order": "7", "section": "resume_person", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "extra": "", "html": "", "source": "", "js": ""}
, "tel_mob": {
"table": "resume", "name": "tel_mob", "type": "varchar(30)", "ru": "Мобильный телефон", "list": "1", "default": "NOT NULL", "html": "4", "order": "8", "section": "resume_person", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "extra": "", "source": "", "js": ""}
, "tel_dom": {
"table": "resume", "name": "tel_dom", "type": "varchar(30)", "ru": "Домашний телефон ", "list": "1", "default": "NOT NULL", "order": "9", "section": "resume_person", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "extra": "", "html": "", "source": "", "js": ""}
, "tel_rab": {
"table": "resume", "name": "tel_rab", "type": "varchar(30)", "ru": "Рабочий телефон ", "list": "1", "default": "NOT NULL", "order": "10", "section": "resume_person", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "extra": "", "html": "", "source": "", "js": ""}
, "email": {
"table": "resume", "name": "email", "type": "varchar(30)", "ru": "E-mail ", "list": "1", "default": "NOT NULL", "order": "11", "section": "resume_person", "mtime": "2012-07-23 00:18:39", "key": "", "link": "", "metka": "", "php": "", "extra": "", "html": "", "source": "", "js": ""}
, "edu": {
"table": "resume", "name": "edu", "type": "tinyint(3)", "ru": "Образование", "list": "1", "default": "NOT NULL", "html": "4", "source": "config:a_edu", "order": "19", "section": "resume_person", "mtime": "2012-07-23 00:21:38", "key": "", "link": "", "metka": "", "php": "", "extra": "", "js": ""}
, "dohod2": {
"table": "resume", "name": "dohod2", "type": "varchar(30)", "ru": "Ожидаемый уровень месячного дохода", "list": "1", "default": "NOT NULL", "order": "20", "section": "resume_person", "mtime": "2012-07-23 00:24:16", "key": "", "link": "", "metka": "", "php": "", "extra": "", "html": "", "source": "", "js": ""}
, "passport": {
"table": "resume", "name": "passport", "type": "tinyint(1)", "ru": "Действующий паспорт РФ", "list": "1", "default": "NOT NULL", "html": "4", "source": "config:unyn", "order": "120", "section": "resume_docu", "mtime": "2012-07-22 19:47:11", "key": "", "link": "", "metka": "", "php": "", "extra": "", "js": ""}
, "voen": {
"table": "resume", "name": "voen", "type": "tinyint(1)", "ru": "Военный билет (приписное св-во)", "list": "1", "default": "NOT NULL", "html": "4", "source": "config:unyn", "order": "130", "section": "resume_docu", "mtime": "2012-07-22 19:47:23", "key": "", "link": "", "metka": "", "php": "", "extra": "", "js": ""}
, "company": {
"table": "resume", "name": "company", "type": "varchar(99)", "ru": "Название компании ", "list": "1", "default": "NOT NULL", "order": "140", "section": "resume_previous", "mtime": "2012-07-22 19:47:41", "key": "", "link": "", "metka": "", "php": "", "extra": "", "html": "", "source": "", "js": ""}
, "job": {
"table": "resume", "name": "job", "type": "varchar(55)", "ru": "Должность ", "list": "1", "default": "NOT NULL", "order": "150", "section": "resume_previous", "mtime": "2012-07-22 19:47:59", "key": "", "link": "", "metka": "", "php": "", "extra": "", "html": "", "source": "", "js": ""}
, "period": {
"table": "resume", "name": "period", "type": "tinyint(3)", "ru": "Период работы ", "list": "1", "default": "NOT NULL", "order": "160", "section": "resume_previous", "mtime": "2012-07-22 19:48:12", "source": "config:a_period", "key": "", "link": "", "metka": "", "php": "", "extra": "", "html": "", "js": ""}
, "people": {
"table": "resume", "name": "people", "type": "int(3)", "ru": "Подчиненные (количество)", "list": "1", "default": "NOT NULL", "order": "170", "section": "resume_previous", "mtime": "2012-07-22 19:48:23", "key": "", "link": "", "metka": "", "php": "", "extra": "", "html": "", "source": "", "js": ""}
, "dohod1": {
"table": "resume", "name": "dohod1", "type": "varchar(30)", "ru": "Уровень дохода (в месяц) ", "list": "1", "default": "NOT NULL", "order": "180", "section": "resume_previous", "mtime": "2012-07-22 19:48:34", "key": "", "link": "", "metka": "", "php": "", "extra": "", "html": "", "source": "", "js": ""}
, "inf": {
"table": "resume", "name": "inf", "type": "text", "ru": "Дополнительная информация ", "list": "1", "default": "NOT NULL", "order": "210", "section": "resume_dop", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "extra": "", "html": "", "source": "", "js": ""}
, "file": {
"table": "resume", "name": "file", "type": "varchar(30)", "ru": "Вы можете добавить файл резюме ", "list": "1", "default": "NOT NULL", "js": "addFileField", "order": "220", "section": "resume_dop", "mtime": "2012-07-23 14:09:47", "link": "[files/resume]", "key": "", "metka": "", "php": "", "extra": "", "html": "", "source": ""}
, "id": {
"table": "resume", "name": "id", "type": "int(5)", "key": "1", "list": "1", "link": "1", "default": "NOT NULL", "extra": "auto_increment", "order": "230", "ru": "", "mtime": "", "metka": "", "php": "", "html": "", "source": "", "js": "", "section": ""}
}
, "schema": {
"title": {
"table": "schema", "name": "title", "type": "varchar(55)", "ru": "", "key": "", "list": "1", "link": "1", "mtime": "2012-08-31 08:17:14", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "1", "section": ""}
, "name": {
"table": "schema", "name": "name", "type": "varchar(30)", "ru": "", "key": "1", "list": "1", "link": "1", "mtime": "2012-08-31 08:17:22", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "5", "section": ""}
, "value": {
"table": "schema", "name": "value", "type": "bool", "ru": "", "key": "", "list": "1", "link": "", "mtime": "2012-08-31 08:17:31", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "9", "section": ""}
}
, "price": {
"group": {
"table": "price", "name": "group", "type": "varchar(30)", "ru": "Категория", "key": "1", "list": "1", "link": "group", "php": "dict_uslugi_group", "source": "config:dict_uslugi_group", "mtime": "", "metka": "", "default": "", "extra": "", "html": "", "js": "", "order": "", "section": ""}
, "name": {
"table": "price", "name": "name", "type": "varchar(30)", "ru": "Наименование", "key": "1", "list": "1", "link": "1", "php": "dict_uslugi", "source": "config:dict_uslugi", "mtime": "", "metka": "", "default": "", "extra": "", "html": "", "js": "", "order": "", "section": ""}
, "price": {
"table": "price", "name": "price", "type": "float", "ru": "Цена", "list": "1", "key": "", "link": "", "mtime": "", "metka": "", "php": "", "default": "", "extra": "", "html": "", "source": "", "js": "", "order": "", "section": ""}
}
, "css": {
"id": {
"table": "css", "name": "id", "ru": "Код", "type": "int(7)", "default": "not null", "extra": "auto_increment", "key": "1", "list": "1", "link": "1", "order": "10", "mtime": "2012-08-21 15:17:48", "section": "1", "metka": "", "php": "", "html": "", "source": "", "js": ""}
, "selector": {
"table": "css", "name": "selector", "ru": "Селектор", "type": "varchar(25)", "default": "not null", "extra": "", "key": "", "list": "1", "link": "1", "order": "20", "mtime": "2012-08-21 15:18:00", "section": "1", "metka": "", "php": "", "html": "", "source": "", "js": ""}
, "title": {
"table": "css", "name": "title", "ru": "Название", "type": "varchar(55)", "default": "not null", "extra": "", "key": "", "list": "1", "link": "1", "order": "30", "mtime": "2012-08-21 15:18:15", "section": "1", "metka": "", "php": "", "html": "", "source": "", "js": ""}
, "sample": {
"table": "css", "name": "sample", "ru": "Образец", "type": "text", "default": "not null", "extra": "", "key": "", "list": "1", "link": "", "order": "40", "mtime": "2012-08-21 15:18:27", "js": "addSampleDiv", "section": "3", "metka": "", "php": "", "html": "", "source": ""}
, "background_color": {
"table": "css", "name": "background_color", "ru": "Фон", "type": "varchar(55)", "default": "not null", "extra": "", "key": "", "list": "1", "link": "", "order": "50", "mtime": "", "metka": "", "php": "", "html": "", "source": "", "js": "", "section": ""}
, "font_family": {
"table": "css", "name": "font_family", "ru": "Шрифт", "type": "varchar(55)", "default": "not null", "extra": "", "key": "", "list": "1", "link": "", "order": "60", "mtime": "", "metka": "", "php": "", "html": "", "source": "", "js": "", "section": ""}
, "font_size": {
"table": "css", "name": "font_size", "ru": "Кегль", "type": "varchar(55)", "default": "not null", "extra": "", "key": "", "list": "1", "link": "", "order": "70", "mtime": "", "metka": "", "php": "", "html": "", "source": "", "js": "", "section": ""}
, "color": {
"table": "css", "name": "color", "ru": "Цвет", "type": "varchar(55)", "default": "not null", "extra": "", "key": "", "list": "1", "link": "", "order": "80", "mtime": "", "metka": "", "php": "", "html": "", "source": "", "js": "", "section": ""}
, "text_decoration": {
"table": "css", "name": "text_decoration", "ru": "Подчёркивание", "type": "varchar(55)", "default": "not null", "extra": "", "key": "", "list": "1", "link": "", "order": "90", "mtime": "2012-08-22 14:03:26", "source": "config:css_decoration", "metka": "", "php": "", "html": "", "js": "", "section": ""}
, "font_weight": {
"table": "css", "name": "font_weight", "ru": "Жирность", "type": "varchar(55)", "default": "not null", "extra": "", "key": "", "list": "1", "link": "", "order": "100", "mtime": "2012-08-22 14:04:57", "source": "config:css_bold", "metka": "", "php": "", "html": "", "js": "", "section": ""}
, "font_style": {
"table": "css", "name": "font_style", "ru": "Курсив", "type": "varchar(55)", "default": "not null", "extra": "", "key": "", "list": "1", "link": "", "order": "110", "mtime": "2012-08-22 14:05:15", "source": "config:css_italic", "metka": "", "php": "", "html": "", "js": "", "section": ""}
, "text_align": {
"table": "css", "name": "text_align", "ru": "Выравнивание", "type": "varchar(55)", "default": "not null", "extra": "", "key": "", "list": "1", "link": "", "order": "120", "mtime": "2012-08-22 14:05:35", "source": "config:css_align", "metka": "", "php": "", "html": "", "js": "", "section": ""}
, "line_height": {
"table": "css", "name": "line_height", "ru": "Межстрочный интервал", "type": "varchar(55)", "default": "not null", "extra": "", "key": "", "list": "1", "link": "", "order": "130", "mtime": "", "metka": "", "php": "", "html": "", "source": "", "js": "", "section": ""}
, "text_indent": {
"table": "css", "name": "text_indent", "ru": "Абзацный отступ", "type": "varchar(55)", "default": "not null", "extra": "", "key": "", "list": "1", "link": "", "order": "140", "mtime": "", "metka": "", "php": "", "html": "", "source": "", "js": "", "section": ""}
, "border": {
"table": "css", "name": "border", "ru": "Рамки", "type": "varchar(55)", "default": "not null", "extra": "", "key": "", "list": "1", "link": "", "order": "150", "mtime": "", "metka": "", "php": "", "html": "", "source": "", "js": "", "section": ""}
, "margin": {
"table": "css", "name": "margin", "ru": "Поля", "type": "varchar(55)", "default": "not null", "extra": "", "key": "", "list": "1", "link": "", "order": "160", "mtime": "", "metka": "", "php": "", "html": "", "source": "", "js": "", "section": ""}
, "margin_top": {
"table": "css", "name": "margin_top", "ru": "Поле сверху", "type": "varchar(55)", "default": "not null", "extra": "", "key": "", "list": "1", "link": "", "order": "170", "mtime": "", "metka": "", "php": "", "html": "", "source": "", "js": "", "section": ""}
, "margin_right": {
"table": "css", "name": "margin_right", "ru": "Поле справа", "type": "varchar(55)", "default": "not null", "extra": "", "key": "", "list": "1", "link": "", "order": "180", "mtime": "", "metka": "", "php": "", "html": "", "source": "", "js": "", "section": ""}
, "margin_bottom": {
"table": "css", "name": "margin_bottom", "ru": "Поле снизу", "type": "varchar(55)", "default": "not null", "extra": "", "key": "", "list": "1", "link": "", "order": "190", "mtime": "", "metka": "", "php": "", "html": "", "source": "", "js": "", "section": ""}
, "margin_left": {
"table": "css", "name": "margin_left", "ru": "Поле слева", "type": "varchar(55)", "default": "not null", "extra": "", "key": "", "list": "1", "link": "", "order": "200", "mtime": "", "metka": "", "php": "", "html": "", "source": "", "js": "", "section": ""}
, "border_top": {
"table": "css", "name": "border_top", "ru": "Рамка сверху", "type": "varchar(55)", "default": "not null", "extra": "", "key": "", "list": "1", "link": "", "order": "210", "mtime": "", "metka": "", "php": "", "html": "", "source": "", "js": "", "section": ""}
, "border_right": {
"table": "css", "name": "border_right", "ru": "Рамка справа", "type": "varchar(55)", "default": "not null", "extra": "", "key": "", "list": "1", "link": "", "order": "220", "mtime": "", "metka": "", "php": "", "html": "", "source": "", "js": "", "section": ""}
, "border_bottom": {
"table": "css", "name": "border_bottom", "ru": "Рамка снизу", "type": "varchar(55)", "default": "not null", "extra": "", "key": "", "list": "1", "link": "", "order": "230", "mtime": "", "metka": "", "php": "", "html": "", "source": "", "js": "", "section": ""}
, "border_left": {
"table": "css", "name": "border_left", "ru": "Рамка слева", "type": "varchar(55)", "default": "not null", "extra": "", "key": "", "list": "1", "link": "", "order": "240", "mtime": "", "metka": "", "php": "", "html": "", "source": "", "js": "", "section": ""}
, "padding": {
"table": "css", "name": "padding", "type": "varchar(55)", "ru": "Внутренние отступы", "key": "", "list": "1", "link": "", "mtime": "2012-08-21 15:54:16", "metka": "", "php": "", "default": "not null", "extra": "", "html": "", "source": "", "js": "", "order": "250", "section": ""}
}
, "requests": {
"name": {
"table": "requests", "name": "name", "type": "varchar(30)", "list": "1", "mtime": "2012-09-21 10:21:22", "default": "NOT NULL", "ru": "Ваше имя", "order": "10", "link": "1", "html": "4", "section": "1", "key": "", "metka": "", "php": "", "extra": "", "source": "", "js": ""}
, "tel": {
"table": "requests", "name": "tel", "type": "varchar(15)", "list": "1", "mtime": "2012-09-21 10:21:42", "default": "NOT NULL", "ru": "Ваш телефон", "html": "4", "order": "20", "section": "1", "key": "", "link": "", "metka": "", "php": "", "extra": "", "source": "", "js": ""}
, "email": {
"table": "requests", "name": "email", "type": "varchar(55)", "list": "1", "mtime": "2012-09-21 10:21:54", "default": "NOT NULL", "ru": "E-mail", "order": "30", "section": "1", "key": "", "link": "", "metka": "", "php": "", "extra": "", "html": "", "source": "", "js": ""}
, "text": {
"table": "requests", "name": "text", "type": "text", "list": "1", "mtime": "2012-09-21 12:31:34", "default": "NOT NULL", "ru": "Дополнительные сведения", "order": "40", "section": "", "key": "", "link": "", "metka": "", "php": "", "extra": "", "html": "", "source": "", "js": ""}
, "mtime": {
"table": "requests", "name": "mtime", "type": "timestamp", "list": "1", "default": "NOT NULL default CURRENT_TIMESTAMP", "order": "180", "mtime": "2012-09-21 10:12:46", "html": "-1", "ru": "", "key": "", "link": "", "metka": "", "php": "", "extra": "", "source": "", "js": "", "section": ""}
, "present": {
"table": "requests", "name": "present", "type": "varchar(55)", "list": "1", "mtime": "2012-09-21 15:01:41", "default": "NOT NULL", "ru": "Подарок", "source": "", "order": "190", "html": "", "section": "2", "php": "addext", "key": "", "link": "", "metka": "", "extra": "", "js": ""}
, "id": {
"table": "requests", "name": "id", "type": "int(5)", "ru": "", "key": "1", "list": "1", "link": "1", "order": "200", "mtime": "2012-09-21 13:11:31", "section": "2", "default": "not null", "extra": "auto_increment", "metka": "", "php": "", "html": "", "source": "", "js": ""}
}
}
dict.legends = {};
dict.legends = {
"order": {
"new": {
"table": "order", "id": "1", "name": "new", "value": "Новый"}
, "accept_des": {
"table": "order", "id": "2", "value": "В работе", "name": "accept_des"}
, "ready": {
"table": "order", "id": "3", "value": "Макет готов", "name": "ready"}
, "pravka": {
"table": "order", "id": "4", "value": "Правка", "name": "pravka"}
, "accept_kli": {
"table": "order", "id": "5", "value": "Макет утверждён", "name": "accept_kli"}
, "toprint": {
"table": "order", "id": "6", "value": "Сдано в печать", "name": "toprint"}
, "closed": {
"table": "order", "id": "7", "value": "Сдано клиенту", "name": "closed"}
, "to_klient": {
"table": "order", "id": "8", "value": "Отправлен на утверждение", "name": "to_klient"}
, "rush": {
"table": "order", "id": "9", "value": "Срочно!", "name": "rush"}
, "fail": {
"table": "order", "id": "10", "value": "Не продано", "name": "fail"}
}
}
dict.dm = {};
dict.dm = {
}
dict.calendar = {};
dict.calendar = {
}
dict.calendar.table = {};
dict.calendar.table = {
"2013-01-01": "Новый Год", "2013-01-02": "Новогодние каникулы", "2013-01-03": "Новогодние каникулы", "2013-01-04": "Новогодние каникулы", "2013-01-05": "Новогодние каникулы", "2013-01-07": "Рождество Христово", "2013-01-12": "День работника прокуратуры Российской Федерации", "2013-01-13": "Старый Новый Год", "2013-01-21": "День инженерных войск", "2013-01-25": "Татьянин день", "2013-01-27": "День снятия блокады города Ленинграда", "2013-02-02": "День разгрома советскими войсками немецко-фашистских войск в Сталинградской битве (1943 год)", "2013-02-08": "Международный день стоматолога", "2013-02-10": "День дипломатического работника", "2013-02-14": "День Святого Валентина / День всех влюбленных", "2013-02-15": "День памяти воинов-интернационалистов", "2013-02-23": "День защитника Отечества", "2013-03-08": "Международный женский день", "2013-03-10": "День работников геодезии и картографии", "2013-03-11": "День работника органов наркоконтроля", "2013-03-17": "День работников торговли, бытового обслуживания населения и жилищно-коммунального хозяйства", "2013-03-19": "День моряка-подводника", "2013-03-23": "День работников гидрометеорологической службы России", "2013-03-25": "День работника культуры России", "2013-03-27": "День внутренних войск МВД России", "2013-03-29": "День специалиста юридической службы", "2013-04-01": "День смеха / День дурака", "2013-04-02": "День единения народов", "2013-04-08": "День сотрудников военных комиссариатов", "2013-04-07": "День геолога", "2013-04-12": "День космонавтики", "2013-04-14": "День войск противовоздушной обороны (Россия)", "2013-04-15": "День специалиста по радиоэлектронной борьбе", "2013-04-18": "День победы русских воинов князя Александра Невского над немецкими рыцарями на Чудском озере (Ледовое побоище, 1242 год)", "2013-04-21": "День местного самоуправления", "2013-04-26": "День памяти погибших в радиационных авариях и катастрофах", "2013-04-27": "День российского парламентаризма", "2013-04-28": "Международный день охраны труда", "2013-04-30": "День пожарной охраны", "2013-05-01": "Праздник Весны и Труда", "2013-05-07": "День Президентского полка", "2013-05-09": "День Победы", "2013-05-26": "День российского предпринимательства", "2013-05-24": "День кадрового работника", "2013-05-25": "День филолога", "2013-05-27": "Общероссийский день библиотек", "2013-05-28": "День пограничника", "2013-05-29": "День химика", "2013-05-31": "День российской адвокатуры", "2013-06-01": "Международный день защиты детей", "2013-06-02": "День спутникового мониторинга и навигации", "2013-06-05": "День эколога", "2013-06-06": "Пушкинский день России", "2013-06-08": "День социального работника", "2013-06-12": "День России", "2013-06-09": "День работников текстильной и лёгкой промышленности", "2013-06-14": "День работников миграционной службы", "2013-06-16": "День медицинского работника", "2013-06-29": "День партизан и подпольщиков", "2013-06-22": "День памяти и скорби — день начала Великой Отечественной войны (1941 год)", "2013-06-27": "День молодёжи (Россия)", "2013-06-30": "День экономиста", "2013-07-03": "День ГАИ (ГИБДД МВД РФ)", "2013-07-07": "День работников морского и речного флота", "2013-07-08": "День семьи, любви и верности", "2013-07-10": "День победы русской армии под командованием Петра Первого над шведами в Полтавском сражении (1709 год)", "2013-07-28": "День Военно-Морского Флота", "2013-07-14": "День российской почты", "2013-07-21": "День металлурга", "2013-07-26": "День системного администратора", "2013-08-01": "День Тыла Вооруженных Сил Российской Федерации", "2013-08-02": "День Воздушно-десантных войск", "2013-08-04": "День железнодорожника", "2013-08-06": "День железнодорожных войск", "2013-08-09": "День первой в российской истории морской победы русского флота под командованием Петра Первого над шведами у мыса Гангут (1714 год)", "2013-08-12": "День Военно-воздушных сил", "2013-08-10": "День физкультурника", "2013-08-11": "День строителя", "2013-08-18": "День Воздушного Флота России", "2013-08-15": "День археолога", "2013-08-22": "День Государственного флага Российской Федерации", "2013-08-23": "День разгрома советскими войсками немецко-фашистских войск в Курской битве (1943 год)", "2013-08-25": "День шахтёра", "2013-08-27": "День российского кино", "2013-09-01": "День работников нефтяной и газовой промышленности", "2013-09-02": "День окончания Второй мировой войны", "2013-09-03": "День солидарности в борьбе с терроризмом", "2013-09-04": "День специалиста по ядерному обеспечению", "2013-09-08": "День танкиста", "2013-09-11": "День победы русской эскадры под командованием Ф. Ф. Ушакова над турецкой эскадрой у мыса Тендра (1790 год)", "2013-09-13": "День программиста", "2013-09-21": "День победы русских полков во главе с великим князем Дмитрием Донским над монголо-татарскими войсками в Куликовской битве (1380 год)", "2012-09-16": "День работников леса и лесоперерабатывающей промышленности", "2012-09-24": "День системного аналитика", "2012-09-27": "Всемирный день туризма", "2012-09-28": "День работника атомной промышленности", "2012-09-30": "День машиностроителя", "2012-10-01": "День Сухопутных войск (Россия)", "2012-10-04": "День космических войск", "2012-10-05": "День учителя", "2012-10-06": "День российского страховщика", "2012-10-20": "День военного связиста", "2012-10-23": "День работников рекламы", "2012-10-24": "День подразделений специального назначения", "2012-10-25": "День таможенника Российской Федерации", "2012-10-14": "День работника сельского хозяйства и перерабатывающей промышленности", "2012-10-21": "День работников дорожного хозяйства", "2012-10-28": "День автомобилиста", "2012-10-29": "День вневедомственной охраны", "2012-10-30": "День инженера-механика", "2012-10-31": "День работников СИЗО и тюрем", "2012-11-01": "День Федеральной службы судебных приставов РФ", "2012-11-04": "День народного единства", "2012-11-05": "День военного разведчика", "2012-11-07": "День согласия и примирения (отмечается с 1996 года)", "2012-11-10": "День сотрудника органов внутренних дел Российской Федерации", "2012-11-13": "День войск радиационной, химической и биологической защиты", "2012-11-19": "День ракетных войск и артиллерии (Россия)", "2012-11-21": "День работника налоговых органов Российской Федерации", "2012-11-27": "День оценщика", "2012-11-25": "День матери", "2012-11-30": "Международный день защиты информации", "2012-12-01": "День победы русской эскадры под командованием П. С. Нахимова над турецкой эскадрой у мыса Синоп (1853 год)", "2012-12-03": "День юриста", "2012-12-05": "День начала контрнаступления советских войск против немецко-фашистских войск в битве под Москвой (1941 год)", "2012-12-09": "День Героев Отечества", "2012-12-12": "День Конституции Российской Федерации", "2012-12-17": "День Ракетных войск стратегического назначения", "2012-12-19": "День работника военной контрразведки Российской Федерации", "2012-12-20": "День работника органов безопасности Российской Федерации", "2012-12-22": "День энергетика", "2012-12-24": "День взятия турецкой крепости Измаил русскими войсками под командованием А. В. Суворова (1790 год)", "2012-12-27": "День спасателя Российской Федерации"}
