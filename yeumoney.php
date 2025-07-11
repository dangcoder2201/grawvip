<?php
// Bắt sự kiện ^C (Ctrl+C)
declare(ticks=1);
pcntl_signal(SIGINT, function() {
    slowEcho(color("=> Cảm ơn bạn đã sử dụng tool! Hẹn gặp lại.\n", "1;32"));
    exit;
});

// Cấu hình
$link4m_api = "68061b7295270a109e5ab1ea"; // Thay bằng API token Link4m của bạn
$your_domain = "https://dichvukey.4pu.com/";
$keyFile = "key_data.json";
$cookie_file = "ttc_cookie.txt";
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Hàm hiển thị từ từ
function slowEcho($text, $delay = 500) {
    foreach (str_split($text) as $char) {
        echo $char;
        usleep($delay);
    }
}

/***[ Delay ]***/
if (strtoupper(substr(PHP_OS, 0, 3)) === 'LIN') {
	$_SESSION['load'] = 2000;
	$_SESSION['delay'] = 150000;
} else {
	$_SESSION['load'] = 0;
	$_SESSION['delay'] = 50000;
}

// === VÀO TOOL GIẢ LẬP



// Hàm màu
function color($text, $color_code) {
    return "\033[".$color_code."m".$text."\033[0m";
}
function rainbow($text) {
    $colors = ['1;31','1;33','1;32','1;36','1;34','1;35'];
    $out = '';
    $i = 0;
    foreach (str_split($text) as $char) {
        $out .= "\033[" . $colors[$i % count($colors)] . "m$char";
        $i++;
    }
    return $out . "\033[0m";
}

// Logo & Info
function showLogo() {
    $logo = <<<LOGO
______  ___   _   _ _____  _____ ___________ ___________  
|  _  \/ _ \ | \ | |  __ \/  __ \  _  |  _  \  ___| ___ \ 
| | | / /_\ \|  \| | |  \/| /  \/ | | | | | | |__ | |_/ / 
| | | |  _  || . ` | | __ | |   | | | | | | |  __||    /  
| |/ /| | | || |\  | |_\ \| \__/\ \_/ / |/ /| |___| |\ \  
|___/ \_| |_/\_| \_/\____/ \____/\___/|___/ \____/\_| \_|
LOGO;
    slowEcho(rainbow($logo) . "\n", 500); // hiển thị logo với tốc độ chậm hơn
}
//Lựa Chọn TOOL
function drawMenuBox() {
    global $choice; // ✅ Biến toàn cục

    $lines = [
        "     CHỌN TOOL MUỐN SỬ DỤNG     ",
        "  1. Tool TDS (traodoisub.com)  ",
        "  2. Tool TTC (tuongtaccheo.com)",
        "  3. Tool Golike Tiktok                   ",
        "  0. Thoát Tool ",
    ];
    $border = "+------------------------------------+";
    slowEcho(color($border . "\n", "1;36"));
    foreach ($lines as $line) {
        $padding = max(0, 36 - strlen(strip_tags($line))); // ✅ Tránh lỗi str_repeat âm
        $space = str_repeat(" ", $padding);
        slowEcho(color("| " . $line . $space . " |\n", "1;33"));
    }
    slowEcho(color($border . "\n", "1;36"));
    slowEcho(color("=> Nhập lựa chọn (1 hoặc 2): ", "1;35"));
    $choice = trim(fgets(STDIN)); // ✅ Gán vào biến toàn cục
}
function showInfo() {
    $lines = [
        color(" Tên tool: ", "1;36") . color("Tool TDS TIKTOK + TTC TIKTOK SIÊU LỎ", "1;33"),
        color(" Tác giả: ", "1;36") . color("DangCoder", "1;35"),
        color(" Website: ", "1;36") . color("https://facebook.com/dangcoder", "1;32"),
        color(" Liên hệ: ", "1;36") . color("https://zalo.me/dangcoder", "1;34"),
    ];
    $border = str_repeat("-", 50);
    slowEcho(color("+$border+\n", "1;33"));
    foreach ($lines as $line) {
        $space = 48 - strlen(strip_tags($line));
        $space = max($space, 0);
        slowEcho(color("| ", "1;33") . color($line . str_repeat(" ", $space), "1;31") . color(" |\n", "1;33"));
    }
    slowEcho(color("+$border+\n", "1;33"));
}

// Key
function generateKey() {
    return 'Dangcoder_' . strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8));
}
function isKeyExpired($time) {
    return (time() - $time) >= 86400;
}
function checkInternet() {
    if (!$sock = @fsockopen('www.google.com', 80)) {
        echo color("❌ Vui lòng bật kết nối mạng.\n", "1;31");
        exit;
    } else {
        fclose($sock);
    }
}

// LINK4M
function shortenLink4m($apiToken, $destinationUrl) {
    $apiUrl = "https://link4m.co/api-shorten/v2?api=" . urlencode($apiToken) . "&url=" . urlencode($destinationUrl);
    $response = @file_get_contents($apiUrl);
    if (!$response) {
        return [false, "Không thể kết nối tới máy chủ Link4m."];
    }
    $json = json_decode($response, true);
    if (isset($json['status']) && $json['status'] === 'success' && isset($json['shortenedUrl'])) {
        return [$json['shortenedUrl'], null];
    } else {
        return [false, $json['message'] ?? 'Lỗi không xác định từ API.'];
    }
}

function Noget() {
    $output = shell_exec('termux-info | grep "Device ID"');
    if (!$output) $output = shell_exec('getprop ro.serialno');
    return trim(md5($output));
}

function getDeviceId() {
    $androidId = shell_exec('settings get secure android_id');
    $serial    = shell_exec('getprop ro.serialno');
    $brand     = shell_exec('getprop ro.product.brand');
    $model     = shell_exec('getprop ro.product.model');
    $combined  = trim($androidId . $serial . $brand . $model);
    return md5($combined);
}

function getIP() {
    return @file_get_contents("https://api.ipify.org");
}

function encrypt($text) {
    for ($i = 0; $i < 5; $i++) {
        $text = md5($text);
    }
    return $text;
}

function checkKeyVIP($key) {
    $device_id = getDeviceId();
    $query = http_build_query([
        "key" => $key,
        "device_id" => $device_id
    ]);
    $url = "https://codewebvip.eu.org/api/checkkey.php?" . $query;
    $response = file_get_contents($url);
    return $response ? json_decode($response, true) : null;
}

function generateKeyFree() {
    return strtoupper(substr(str_shuffle("ABCDEFGHJKLMNPQRSTUVWXYZ23456789"), 0, 8));
}

function timeRemaining($futureTime) {
    $remaining = $futureTime - time();
    if ($remaining <= 0) return false;
    $hours = floor($remaining / 3600);
    $minutes = floor(($remaining % 3600) / 60);
    return "$hours giờ $minutes phút";
}

function timeRemainingVip($futureTime) {
    $remaining = $futureTime - time();
    if ($remaining <= 0) return false;
    $days = floor($remaining / 86400);
    $hours = floor(($remaining % 86400) / 3600);
    $minutes = floor(($remaining % 3600) / 60);
    $result = "";
    if ($days > 0) $result .= "$days ngày ";
    if ($hours > 0) $result .= "$hours giờ ";
    if ($minutes > 0) $result .= "$minutes phút";
    return trim($result);
}

// Bắt đầu
print(">> Loading...");
sleep(3);
system("clear");
// === LOADING TOOL ===
for ($i = 0; $i <= 100; $i++) {
    $bar = str_repeat("█", intval($i / 2));
    echo "\r\033[1;32mĐANG LOAD TOOL + GIT +: [{$i}% {$bar}]\033[0m";
    usleep(30000); // 0.03 giây
}
sleep(2);
system("clear");
echo "\033[1;31m[●] Đang Tiến Hành Vào Tool...... \033[0m\n";
sleep(5);
system("clear");
showLogo();
showInfo();

// === KIỂM TRA KEY ĐÃ LƯU ===
if (file_exists($keyFile)) {
    $device_id = getDeviceId();
    $storedContent = file_get_contents($keyFile);
    $decoded = base64_decode($storedContent);
    $stored = json_decode($decoded, true);

    if (!is_array($stored) || !isset($stored['type'], $stored['expires'])) {
        echo color("⚠️ Dữ liệu key không hợp lệ! Xoá file và tạo lại.\n", "1;31");
        unlink($keyFile);
        exit;
    }

    if ($stored['type'] === 'vip') {
        if (
            isset($stored['raw_key'], $stored['key_md5'], $stored['device_md5']) &&
            encrypt($stored['raw_key']) === $stored['key_md5'] &&
            encrypt($device_id) === $stored['device_md5']
        ) {
            $result = checkKeyVIP($stored['raw_key']);
            if ($result && $result['status'] === 'success') {
                $timeLeft = timeRemainingVip($stored['expires']);
                $formattedDate = date('d/m/Y - H:i:s', $stored['expires']);
                echo color("🔐 Key VIP đã lưu hợp lệ.\n", "1;32");
                echo color("⏳ Còn hạn: $timeLeft (Hết hạn: $formattedDate)\n", "1;36");
                echo color("➡️ Đăng nhập thành công!\n", "1;32");
                drawMenuBox();
                handleTool();
                exit;
            } else {
                echo color("❌ Key đã hết hạn hoặc không hợp lệ! Vui lòng nhập lại.\n", "1;31");
                unlink($keyFile);
            }
        } else {
            echo color("⚠️ Dữ liệu mã hoá không khớp, cần nhập lại key VIP!\n", "1;31");
            unlink($keyFile);
        }
    } elseif ($stored['type'] === 'free') {
        if ($stored['expires'] > time()) {
           $ip = getIP();
            if (isset($stored['ip']) && $stored['ip'] === $ip) {
                $timeLeft = timeRemaining($stored['expires']);
                echo color("\n🔓 Key Free còn hạn: $timeLeft\n", "1;36");
                echo color("🌐 IP của bạn: $ip\n", "1;34");
                echo color("➡️ Đăng nhập thành công!\n", "1;32");
                drawMenuBox();
                handleTool();
                exit;
            } else {
                echo color("\n⚠️ IP đã thay đổi hoặc thiếu dữ liệu. Tạo key mới...\n", "1;31");
                unlink($keyFile);
            }
        } else {
            echo color("\n⛔ Key Free đã hết hạn. Tạo key mới...\n", "1;31");
            unlink($keyFile);
        }
    }
}
//KẾT THÚC XỬ LÝ KEY 

// === MENU CHỌN CHẾ ĐỘ ===
$border = "+------------------------------------------+";
$lines = [
    "        CHỌN CHẾ ĐỘ KEY KÍCH HOẠT         ",
    "        1. Sử dụng KEY FREE            ",
    "        2. Sử dụng KEY VIP  ",
];

slowEcho(color($border . "\n", "1;36"));
foreach ($lines as $line) {
    $padding = max(0, 42 - strlen(strip_tags($line))); // fix lỗi ở đây
    $space = str_repeat(" ", $padding);
    slowEcho(color("| " . $line . $space . " |\n", "1;33"));
}
slowEcho(color($border . "\n", "1;36"));
slowEcho(color("👉 Nhập lựa chọn (1 hoặc 2): ", "1;35"));
$luachon = trim(fgets(STDIN));
slowEcho(color("═══════════════════════════════════════════\n", "1;35"));

// === XỬ LÝ FREE KEY ===
if ($luachon === '1') {
    $key = generateKeyFree();
    $ip = getIP();
    $expires = strtotime("+1 day");
    $landingURL = "https://dichvukey.4pu.com/?ma=$key";
    $result = shortenLink4m($link4m_api, $landingURL);
    $shortLink = $result[0];
    $error = $result[1];
    if ($shortLink) {
        slowEcho(color(" IP hiện tại: ", "1;34") . color($ip, "1;32") . "\n");
        echo color("\n🌐 Link Lấy Key: $shortLink\n", "1;33");
        echo color("👉 Dán key sau khi vượt link: ", "1;36");
        $userKey = trim(fgets(STDIN));

        if ($userKey === $key) {
            $data = [
                "key" => $key,
                "expires" => $expires,
                "type" => "free",
                "ip" => $ip
            ];
            file_put_contents($keyFile, base64_encode(json_encode($data)));
            echo color("✅ Key đúng! Truy cập tool...\n", "1;32");
            drawMenuBox();
            handleTool();
        } else {
            echo color("❌ Key không hợp lệ!\n", "1;31");
            exit;
        }
    } else {
        echo color("❌ Lỗi khi tạo link: $error\n", "1;31");
        exit;
    }

// === XỬ LÝ VIP KEY ===
} elseif ($luachon === '2') {
    $device_id = getDeviceId();
    slowEcho(color("📱 Device ID: ", "1;34") . color($device_id, "1;33") . "\n");
    slowEcho(color("🔑 Nhập KEY VIP: ", "1;35"));
    $key = trim(fgets(STDIN));

    $result = checkKeyVIP($key);
    if (!$result || $result['status'] !== 'success') {
        echo color("❌ " . ($result['message'] ?? 'Key không hợp lệ hoặc thiết bị sai!') . "\n", "1;31");
        exit;
    }

    // ✅ THÔNG TIN KEY ĐÃ XÁC THỰC
    $ngaymua = date("d/m/Y - H:i:s", $result['ngaymua']);
    $ngayhet = date("d/m/Y - H:i:s", $result['ngayhet']);
    $conlai = timeRemainingVip($result['ngayhet']);
    $tennguoi = $result['tennguoi'] ?? 'Không rõ';

    $form = [
        color("| Thông Tin KEY VIP", "1;33"),
        color("|------------------------------", "1;33"),
        color("| 👤 Người Mua : ", "1;36") . color($tennguoi, "1;32"),
        color("| 🛒 Ngày Mua   : ", "1;36") . color($ngaymua, "1;37"),
        color("| 📅 Hết Hạn    : ", "1;36") . color($ngayhet, "1;37"),
        color("| ⏳ Còn Lại    : ", "1;36") . color($conlai ?: "Đã hết hạn", "1;35"),
        color("|------------------------------", "1;33"),
    ];

    slowEcho(color("+------------------------------+\n", "1;33"));
    foreach ($form as $line) {
        $padding = 30 - strlen(strip_tags($line));
        $space = str_repeat(" ", max(0, $padding));
        slowEcho($line . $space . "|\n");
    }
    slowEcho(color("+------------------------------+\n", "1;33"));

    // ✅ Lưu key
    $data = [
        "raw_key" => $key,
        "key_md5" => encrypt($key),
        "device_md5" => encrypt($device_id),
        "expires" => $result['ngayhet'],
        "type" => "vip"
    ];
    file_put_contents("key_data.json", base64_encode(json_encode($data)));

    echo color("🔐 Đã lưu KEY VIP mã hoá!\n", "1;32");

    // ✅ TỰ ĐỘNG GỌI MENU CHỌN TOOL
    drawMenuBox(); // ← ← ← chuyển hướng sang lựa chọn tool
    handleTool();
}

/*############################## [PHẦN TTC] ############################*/
function login_ttc($access_token, $cookie_file) {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => "https://tuongtaccheo.com/logintoken.php",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => "access_token=" . urlencode($access_token),
        CURLOPT_COOKIEJAR => $cookie_file,
        CURLOPT_COOKIEFILE => $cookie_file,
        CURLOPT_HTTPHEADER => [
            "Content-Type: application/x-www-form-urlencoded"
        ]
    ]);
    $result = curl_exec($ch);
    curl_close($ch);
    return json_decode($result, true);
}

function get_tiktok($type, $cookie_file) {
    $url = "https://tuongtaccheo.com/tiktok/kiemtien".$type."/getpost.php";
    $headers = [
        "Host: tuongtaccheo.com",
        "accept: application/json, text/javascript, */*; q=0.01",
        "x-requested-with: XMLHttpRequest",
        "user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)",
        "referer: https://tuongtaccheo.com/tiktok/kiemtien/"
    ];
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPGET => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_COOKIEFILE => $cookie_file,
        CURLOPT_HTTPHEADER => $headers,
    ]);
    $res = curl_exec($ch);
    curl_close($ch);
    return json_decode($res, true);
}
function nhantien($id, $cookie_file) {
    $url  = "https://tuongtaccheo.com/tiktok/kiemtien/subcheo/nhantien.php";
    $data = "id=".$id;
    $headers = [
        "Host: tuongtaccheo.com",
        "content-length: " . strlen($data),
        "accept: application/json, text/javascript, */*; q=0.01",
        "x-requested-with: XMLHttpRequest",
        "user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)",
        "referer: https://tuongtaccheo.com/tiktok/kiemtien/"
    ];
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPGET => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_COOKIEFILE => $cookie_file,
        CURLOPT_HTTPHEADER => $headers,
    ]);
    $result = curl_exec($ch);
    curl_close($ch);
    return json_decode($result, true);
}
function coin($access_token, $cookie_file) {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => 'https://tuongtaccheo.com/home.php',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_COOKIEFILE => $cookie_file,
    ]);
    $access = curl_exec($ch);
    curl_close($ch);

    if (strpos($access, 'soduchinh') !== false) {
        $xu = explode('"soduchinh">', $access);
        $xu = explode('</strong>', $xu[1])[0];
        return (int)trim($xu);
    }
    return 0;
}
function delay($delay) {
    $colors = ['1;31','1;33','1;32','1;36','1;34','1;35']; // Cầu vồng
    $msg = "Vui lòng chờ";


    for ($tt = $delay; $tt >= 1; $tt--) {
        for ($frame = 0; $frame < count($colors); $frame++) {
            $rainbow_text = "";

            // Áp dụng màu từng ký tự, xử lý UTF-8
            for ($i = 0; $i < mb_strlen($msg); $i++) {
                $char = mb_substr($msg, $i, 1);
                $color_code = $colors[($i + $frame) % count($colors)];
                $rainbow_text .= "\033[" . $color_code . "m" . $char;
            }

            // In dòng không nhảy, không chấm tròn
            echo "\r\033[0m(◔‿◔) " . $rainbow_text . "\033[0m ";
            echo "\033[1;93m$tt\033[0m \033[1;91mGiây   "; // thêm khoảng trắng xoá phần dư

            usleep(150000); // 0.15s mỗi frame
        }
    }

    // Hiện lại con trỏ
    // Clear dòng
    echo "\r" . str_repeat(" ", 50) . "\r";
}
/*###################### [ HẾT PHẦN TTC ] ############*/
// === Vào tool ===
// Gọi hàm hiển thị form chọn tool
function handleTool() {
    global $choice, $cookie_file;
if ($choice == "0"){
//Thoát tool
posix_kill(posix_getpid(), SIGINT); // Gửi tín hiệu Ctrl+C giả

} elseif ($choice == "1") {
    slowEcho(color("\n=> Bạn đã chọn TOOL TDS\n", "1;36"));
    slowEcho(color("Nhập access_token TDS: ", "1;33"));
    $tds_token = trim(fgets(STDIN));

    $tds_api = "https://traodoisub.com/api/?fields=profile&access_token=$tds_token";
    $json = file_get_contents($tds_api);
    $data = json_decode($json, true);

    if (isset($data['success']) && $data['success'] == 200) {
        slowEcho(color("=> Đăng nhập thành công!\n", "1;32"));
        slowEcho(color("Tài khoản: ", "1;36") . $data['data']['user'] . "\n");
        slowEcho(color("Số xu: ", "1;33") . $data['data']['xu'] . "\n");
        slowEcho(color("Xu die: ", "1;31") . $data['data']['xudie'] . "\n");
    } else {
        slowEcho(color("=> Token TDS không hợp lệ hoặc lỗi kết nối API!\n", "1;31"));
    }

} elseif ($choice == "2") {
system("clear");
showLogo();
showInfo();
    slowEcho(color("\n=> Bạn đã chọn TOOL TTC\n", "1;36"));
    $ttc_data_file = "ttc_data.json"; // Lưu access_token
    if (file_exists($ttc_data_file) && file_exists($cookie_file)) {
        $menu = [
            "+--------------------------------------+",
            "|     CHỌN PHƯƠNG THỨC ĐĂNG NHẬP TTC   |",
            "|  1. Đăng nhập bằng tài khoản đã lưu  |",
            "|  2. Đăng nhập tài khoản mới          |",
            "+--------------------------------------+",
            "👉 Nhập lựa chọn (1 hoặc 2): "
        ];
        foreach ($menu as $line) slowEcho(color($line . "\n", "1;36"));

        $chon = trim(fgets(STDIN));
        if ($chon === "1") {
            $data = json_decode(file_get_contents($ttc_data_file), true);
            $access_token = $data['access_token'] ?? null;

            if (!$access_token) {
                echo color("❌ Không tìm thấy access_token đã lưu!\n", "1;31");
                unlink($ttc_data_file);
                unlink($cookie_file);
                exit;
            }

            echo color("🔁 Đang đăng nhập với access_token đã lưu...\n", "1;33");

        } elseif ($chon === "2") {
            @unlink($ttc_data_file);
            @unlink($cookie_file);
            slowEcho(color("🆕 Nhập access_token TTC mới: ", "1;35"));
            $access_token = trim(fgets(STDIN));
            file_put_contents($ttc_data_file, json_encode(["access_token" => $access_token]));
        } else {
            echo color("❌ Lựa chọn không hợp lệ!\n", "1;31");
            exit;
        }
    } else {
        slowEcho(color("🆕 Nhập access_token TTC: ", "1;35"));
        $access_token = trim(fgets(STDIN));
        file_put_contents($ttc_data_file, json_encode(["access_token" => $access_token]));
    }

    // Đăng nhập
$login = login_ttc($access_token, $cookie_file);

    if (isset($login['status']) && $login['status'] == 'success') {
    slowEcho(color("=> Đăng nhập thành công!\n", "1;32"));
sleep(3);
system("clear");
showLogo();
showInfo();
     
     slowEcho(color("--------------------------------------------------------------\n", "1;33"));
     
    // === Form Đẹp Hiển Thị Thông Tin Tài Khoản TTC ===
    $tk_ttc = $login['data']['user'] ?? 'Không xác định';
    $xu_ttc = $login['data']['sodu'] ?? '0';

    $lines = [
        "+------------------------------------------+",
        "|           \033[1;36mTHÔNG TIN TÀI KHOẢN TTC\033[0m           |",
        "+------------------------------------------+",
        "| 👤 \033[1;36mTài khoản\033[0m    : \033[1;32m" . str_pad($tk_ttc, 22) . "\033[0m|",
        "| 💰 \033[1;33mSố dư hiện tại\033[0m: \033[1;32m" . str_pad($xu_ttc . " xu", 20) . "\033[0m|",
        "+------------------------------------------+"
    ];

    foreach ($lines as $line) {
        echo $line . "\n";
    }
    
     slowEcho(color("--------------------------------------------------------------\n", "1;33"));

        slowEcho(color("===> CHỌN NHIỆM VỤ MUỐN CHẠY <===\n", "1;31"));
        slowEcho(color("1. Nhiệm vụ Flowers\n", "1;33"));
        slowEcho(color("2. Nhiệm vụ Tim\n", "1;33"));
        slowEcho(color("Nhập lựa chọn (1 hoặc 2): ", "1;35"));
        $nhiemvu = trim(fgets(STDIN));
        slowEcho(color("Nhập Thời Gian Delay: ", "1;35"));
        $delay = trim(fgets(STDIN));
        slowEcho(color("--------------------------------------------------------------\n", "1;33"));
        // Thêm phần xử lý nhiệm vụ ở đây nếu muốn
        if ($nhiemvu == "1") {
    $listsub = get_tiktok("/subcheo", $cookie_file);
    $tongnv = count($listsub);

    if ($tongnv == 0) {
        slowEcho(color("=> Đã Hết Nhiệm Vụ Follow          \r", "1;31"));
        sleep(2);
    }

    $list = '';
    $dem = 0;
    $xu_dau = coin($access_token, $cookie_file); // Xu ban đầu

    // ✅ Hiển thị số nhiệm vụ còn lại
    echo "\n\033[1;33m=> Còn lại: \033[1;32m$tongnv nhiệm vụ FOLLOW\033[0m\n";

    // ✅ Bảng tiêu đề cố định
    echo "\033[1;92m+------+--------------+----------+--------+-------------+\n";
    echo "\033[1;92m| STT  | TikTok ID   |  Time    |  Type  |   Status    |\n";
    echo "\033[1;92m+------+--------------+----------+--------+-------------+\n";
     usleep(500);

    for ($lap = 0; $lap < $tongnv; $lap++) {
        $id = $listsub[$lap]["idpost"];
        $link = $listsub[$lap]["link"];

        // Mở link TikTok
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'LIN') {
            @system('xdg-open https://www.tiktok.com/@' . $link);
        } else {
            @system('cmd /c start https://www.tiktok.com/@' . $link);
        }

        delay($delay); // Delay xem video

        $xu = coin($access_token, $cookie_file);
        $dem++;

        // ✅ Dòng hiệu ứng động sau mỗi nhiệm vụ
        $stt_show = str_pad($dem, 2, " ", STR_PAD_LEFT);
        $id_tiktok = str_pad($link, 12, " ", STR_PAD_BOTH);
        $time_show = date("H:i:s");
        $kl =
            "\033[1;91m|\033[1;91m $stt_show " .
            "\033[1;91m|\033[1;93m @$id_tiktok " .
            "\033[1;91m|\033[1;36m $time_show " .
            "\033[1;91m|\033[1;" . rand(31, 37) . "m FOLLOW " .
            "\033[1;91m|\033[1;" . rand(32, 36) . "m Hoàn Thành\033[0m\n";

        for ($i = 0; $i < strlen($kl); $i++) {
            echo $kl[$i];
            usleep(1500);
        }

        $list .= $id . ",";

        // Nhận xu sau mỗi 7 nhiệm vụ
        if ($dem % 7 == 0) {
           $list = substr($list, 0, (strlen($list) - 1));
            $nhantien = nhantien($list, $cookie_file);

            if ($nhantien["mess"]) {
                $do = "\033[1;91m"; // Màu đỏ cảnh báo
                $xujob = $nhantien["sodu"];
                $xu = $xu + $xujob;

                $kl = "\e[1;35m❯\e[1;36m❯\e[1;31m❯\033[1;36m Nhận Thành Công " . ($xujob / 1000) . " Nhiệm Vụ\033[1;31m ● \033[1;32m+$xujob Xu \033[1;31m●\033[1;32m Hiện Có $xu Xu \n";
                for ($i = 0; $i < strlen($kl); $i++) {
                    echo $kl[$i];
                    usleep(500);
                }
                   slowEcho(color("-------------------------------------------------------------\n", "1;33"));
            } else {
                sleep(1);
                fwrite(fopen("luusub.txt", "a+"), $list . ",");
                echo  $do . $nhantien["error"] . "          \n";
            }

            $list = '';
        }
    }

  } elseif ($nhiemvu == "2") { 
          slowEcho(color("Chức Năng Nhiệm Vụ Tim đang bảo trì! \n", "1;91"));
        exit;
  }
    } else {
        slowEcho(color("=> Token TTC không hợp lệ hoặc lỗi kết nối API!\n", "1;31"));
    }
 // LỰA CHỌN TOOL 3
} elseif ($choice == "3"){
  $url = "https://raw.githubusercontent.com/dangcoder2201/grawvip/main/golikev1.php";
    $code = @file_get_contents($url);
    if ($code) {
        eval("?><?php\n" . $code);
    } else {
        slowEcho(color("=> Chức năng này đang được bảo trì, Hoặc gặp lỗi, Vui Lòng thử lại sau...\n", "1;31"));
        exit;
    }
} else {
    slowEcho(color("=> Lựa chọn không hợp lệ! Thoát tool...\n", "1;31"));
}
}
