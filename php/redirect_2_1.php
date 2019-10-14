<?php

$redirect = array(
	
    // Ссылка на сайт накоторый нужно перейти залогиненному пользователю
    "grayUrl" => "https://google.ru",

    // Ссылка на сайт на который нужно перейти залогиненному пользователю, после N-го перехода на клоаку
    "counterUrl" => "https://google.ru",
    
    // Статический адресс реффера
    "staticReffer" => "http://healthy-tip.icu/landings/DitonicaTH/mar/IRp14/IRp14Pr/",

    // Название папки с серым лендингом
    //"grayFolder" => "innerFolder",
    //"grayFolder" => "grayLand",
    "grayFolder" => "blackLand",

    // Нужно перейти на адрес прописанный выше
    "isRedirect" => true,

    // Нужно перейти в каталог innerFolder
    // "isRedirect" => true,

    // Нужно ли сохранять в лог данные пользователей
    "isSaveLog" => false,

    // Нужно ли сохранять в лог данные пользователей если он отправит заявку
    "isSaveSuccesLog" => false,
    
    // Количество открытий клоаки, до перехода на акционную страницу
    "openCounter" => 3,
	
);