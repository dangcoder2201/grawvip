<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
error_reporting(0);
// Bắt sự kiện ^C (Ctrl+C)
declare(ticks=1);
pcntl_signal(SIGINT, function() {
    slowEcho(color("=> Cảm ơn bạn đã sử dụng tool! Hẹn gặp lại.\n", "1;32"));
    exit;
});


// Hàm hiển thị từ từ
if (!function_exists('slowEcho')) {
function slowEcho($text, $delay = 500) {
    foreach (str_split($text) as $char) {
        echo $char;
        usleep($delay);
    }
 }
}

// Hàm màu
if (!function_exists('color')) {
function color($text, $color_code) {
    return "\033[".$color_code."m".$text."\033[0m";
}
}

if (!function_exists('rainbow')) {
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
}
// Logo & Info
if (!function_exists('showLogo')) {
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
}
//Lựa Chọn TOOL
if (!function_exists('showInfo')) {
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
}
// Key



// Bắt đầu
system("clear");
showLogo();
showInfo();


//Hiển thị banner
echo color("╔═════════════════════════════════╗\n", "1;39");
echo color("║     ", "1;39") . color("ĐĂNG NHẬP GOLIKE AUTH", "1;36") . color("      ║\n", "1;39");
echo color("╚═════════════════════════════════╝\n", "1;39");

// Khởi tạo file nếu chưa có
if (!file_exists("Authorization.txt")) file_put_contents("Authorization.txt", "");
if (!file_exists("token.txt")) file_put_contents("token.txt", "");

$author = trim(file_get_contents("Authorization.txt"));
$token = trim(file_get_contents("token.txt"));

// Nhập auth nếu chưa có
if ($author == "") {
    echo color(" 💸 NHẬP AUTHORIZATION GOLIKE : ", "1;32");
    $author = trim(fgets(STDIN));
    echo color(" 💸 NHẬP TOKEN (T CỦA GOLIKE): ", "1;32");
    $token = trim(fgets(STDIN));
    file_put_contents("Authorization.txt", $author);
    file_put_contents("token.txt", $token);
} else {
    echo color("Nhập 1 để vào TOOL Golike\n", "1;32");
    echo color("     HOẶC LÀ\n", "1;36");
    echo color("Nhập AUTHORIZATION ở đây để vào acc golike khác : ", "1;32");
    $chon = trim(fgets(STDIN));
    if ($chon != "1") {
        $author = $chon;
        echo color("🚀 Nhập T : ", "1;32");
        $token = trim(fgets(STDIN));
        file_put_contents("Authorization.txt", $author);
        file_put_contents("token.txt", $token);
    }
}

// Xóa màn hình
system('clear');

// Hàm headers
function getHeaders($auth, $token) {
    return [
        "Accept: application/json, text/plain, */*",
        "Content-Type: application/json;charset=utf-8",
        "Authorization: $auth",
        "t: $token",
        "User-Agent: Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Mobile Safari/537.36",
        "Referer: https://app.golike.net/account/manager/tiktok"
    ];
}

// Hàm lấy thông tin người dùng
function getUserInfo($auth, $token) {
    $url = "https://gateway.golike.net/api/users/me";
    $headers = getHeaders($auth, $token);
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers,
    ]);
    $res = curl_exec($ch);
    curl_close($ch);
    return json_decode($res, true);
}

// Thông tin tài khoản
$userInfo = getUserInfo($author, $token);

if (!isset($userInfo['status']) || $userInfo['status'] != 200) {
    echo color("😠 Authorization hoặc T sai rồi bạn ơi! 😭\n", "1;31");
    exit;
}

// ✅ Đăng nhập thành công
$name = $userInfo['data']['name'];
$username = $userInfo['data']['username'];
$tien = $userInfo['data']['coin'];
echo color("Đăng nhập tài khoản thành công!\n", "1;32");
echo color("Tên tài khoản: ", "1;36") . color($name . "\n", "1;33");
echo color(" Username: ", "1;36") . color($username . "\n", "1;33");
echo color("Tiền Hiện Có: ", "1;36") . color($tien . "\n", "1;33");
sleep(2);

// Lấy danh sách acc TikTok
function getTikTokAccounts($auth, $token) {
    $url = "https://gateway.golike.net/api/tiktok-account";
    $headers = getHeaders($auth, $token);
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers,
    ]);
    $res = curl_exec($ch);
    curl_close($ch);
    return json_decode($res, true);
}

// In danh sách TikTok
echo color("╔════════════════════════════════════════════╗\n", "1;39");
echo color("║   ", "1;39") . color("DANH SÁCH ACC TIKTOK TRONG ACC GOLIKE", "1;36") . color("    ║\n", "1;39");
echo color("╚════════════════════════════════════════════╝\n", "1;39");

$response = getTikTokAccounts($author, $token);

if (!isset($response['status']) || $response['status'] != 200) {
    echo color("😠 Không thể lấy danh sách TikTok. Kiểm tra lại token!\n", "1;31");
    exit;
}

$accs = $response['data'];
foreach ($accs as $i => $acc) {
    echo color("[".($i+1)."] ", "1;36") . color($acc['nickname'], "1;93") . color(" | ", "1;97") . color("✅ Online\n", "1;32");
}
echo color("══════════════════════════════════════════════\n", "1;35");
// ======= CHỌN ACC TIKTOK =======
echo "[\033[1;31m❣\033[0m] \033[1;36m✈\033[0m " . color("Chọn tài khoản TikTok muốn chạy: ", "1;33");
while (true) {
    $chonAcc = intval(trim(fgets(STDIN)));
    if (isset($response['data'][$chonAcc - 1])) {
        $tiktokId = $response['data'][$chonAcc - 1]['id'];
        break;
    }
    echo color("❌ Không hợp lệ, chọn lại: ", "1;31");
}

// ======= NHẬP DELAY =======
while (true) {
    echo color("⏳ Nhập delay giữa các job (giây): ", "1;32");
    $delay = intval(trim(fgets(STDIN)));
    if ($delay > 0) break;
    echo color("❌ Delay phải là số > 0\n", "1;31");
}

// ======= NHẬN TIỀN LẦN 2 =======
echo color("💸 Có nhận tiền lần 2 không? (y/n): ", "1;33");
$lan2 = strtolower(trim(fgets(STDIN)));
$nhanlan2 = ($lan2 == 'y');
echo color("════════════════════════════════════════════════\n", "1;35");

/// ======= BẮT ĐẦU GET JOB =======
$daLam = []; // danh sách job đã làm
$dem = 0;
$tongTien = 0;

// ✅ Bảng tiêu đề cố định
  echo "\033[1;36m│\033[0m \033[1;33mSTT\033[0m  \033[1;36m│\033[0m \033[1;33mThời gian\033[0m \033[1;36m│\033[0m \033[1;32mStatus \033[0m \033[1;36m│\033[0m \033[1;31mType Job\033[0m \033[1;36m│\033[0m \033[1;37mID Acc\033[0m \033[1;36m│\033[0m \033[1;32mXu\033[0m   \033[1;36m│\033[0m \033[1;33mTổng\033[0m \033[1;36m│\033[0m\n";
echo color("════════════════════════════════════════════════\n", "1;35");
     usleep(500);

while (true) {
   $text = "Đang tìm nhiệm vụ";
echo color($text, "1;36");
    $jobData = getJobs($author, $token, $tiktokId);
    // Xóa dòng "Đang tìm nhiệm vụ..."
clearLine($text);
   usleep(300000);
    if (!isset($jobData['data']) || !isset($jobData['data']['id'])) {
    $textvip = "Không có job, đợi 5s";
   echo color($textvip, "1;31");
    sleep(5);
     clearLine($text);
    continue;
}


    $job = $jobData['data'];
    $jobId = $job['id'];
    $link = $job['link'];
    $job_type = strtoupper($job['type']);

    if (in_array($jobId, $daLam)) {
        echo color("⚠️ Bỏ qua job trùng ID $jobId\n", "1;33");
        sleep(2);
        continue;
    }

    if (empty($link) || !filter_var($link, FILTER_VALIDATE_URL)) {
        echo color("❌ Link không hợp lệ. Bỏ qua...\n", "1;31");
        sleep(2);
        continue;
    }

    //echo color("👉 Nhiệm vụ: $job_type\n", "1;36");
    //echo color("🌐 Link: ", "1;36") . color("$link\n", "1;34");
    system("termux-open-url '$link'");

   delay($delay);

    // Gọi lần 1
    $hoanthanh = completeJob($author, $token, $jobId, $tiktokId);
    $tien = $hoanthanh['data']['prices'];

    if ($hoanthanh['status'] == 200 && $tien > 0) {
        $dem++;
        $tongTien += $tien;
    } else {
        // Nếu chọn nhận tiền lần 2
        if ($nhanlan2) {
            echo color("Thử lại hoàn thành job lần 2", "1;33");
            sleep(6);
            clearCurrentLine();
            $hoanthanh2 = completeJob($author, $token, $jobId, $tiktokId);
            $tien2 = $hoanthanh2['data']['prices'];
            $tien = $tien2;
          


            if ($hoanthanh2['status'] == 200 && $tien2 > 0) {
                $dem++;
                $tongTien += $tien2;
            } else {
                // ❌ Sau 2 lần vẫn fail → báo lỗi + skip job
                echo color("Không hoàn thành job $jobId sau 2 lần. Đang gửi báo lỗi", "1;31");
               clearCurrentLine();
         $object_id = $jobData['data']['object_id'];
         $ads_id = $jobData['data']['id'];
                if (!$object_id) {
                    echo color("⚠️ Thiếu object_id, không thể skip job!\n", "1;33");
                } else {
                    $skip = baoloi($author, $token, $ads_id, $object_id, $tiktokId);
                    if (isset($skip['status']) && $skip['status'] == 200) {
                        echo color("Đã Bỏ qua Job Này", "1;31");
                        sleep(2);
                        clearCurrentLine();
                    } else {
                        echo color("❌ Skip job thất bại!\n", "1;31");
                        print_r($skip);
                    }
                }
                $daLam[] = $jobId;
                continue;
            }
        } else {
            // ❌ Không nhận tiền lần 2 → skip luôn
            echo color("❌ Không hoàn thành job $jobId\n", "1;31");
            $object_id = $jobData['data']['object_id'];
            $ads_id = $jobData['data']['id'];
            if (!$object_id) {
                echo color("⚠️ Thiếu object_id, không thể skip job!\n", "1;33");
            } else {
                $skip = baoloi($author, $token, $ads_id, $object_id, $tiktokId);
                if (isset($skip['status']) && $skip['status'] == 200) {
                    echo color("✅ Báo lỗi + Skip job $jobId thành công!\n", "1;32");
                } else {
                    echo color("❌ Skip job thất bại!\n", "1;31");
                    print_r($skip);
                }
            }
            $daLam[] = $jobId;
            continue;
        }
    }

    // Hiển thị dòng kết quả vào bảng
 $time = getdate();
    $h = str_pad($time['hours'], 2, "0", STR_PAD_LEFT);
    $m = str_pad($time['minutes'], 2, "0", STR_PAD_LEFT);
    $s = str_pad($time['seconds'], 2, "0", STR_PAD_LEFT);
$stt++;

printf(
    "\033[1;36m│\033[0m \033[1;31m%-3s\033[0m \033[1;36m│\033[0m \033[1;33m%02d:%02d:%02d \033[0m\033[1;36m│\033[0m \033[1;32m%-7s\033[0m \033[1;36m│\033[0m \033[1;31m%-8s\033[0m \033[1;36m│\033[0m \033[1;37m%-6s\033[0m \033[1;36m│\033[0m \033[1;32m+%-2s\033[0m \033[1;36m│\033[0m \033[1;33m%-4s\033[0m \033[1;36m│\033[0m\n",
    $stt, $h, $m, $s, "success", $job_type, "Ẩn ID", $tien, $tongTien
);
}

// ======= HÀM GET JOB =======
function getJobs($auth, $token, $accountId) {
    $url = "https://gateway.golike.net/api/advertising/publishers/tiktok/jobs";
    $headers = getHeaders($auth, $token);
    
    $params = http_build_query([
        'account_id' => $accountId,
        'data' => 'null'
    ]);

    $ch = curl_init($url . '?' . $params);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers,
    ]);

    $res = curl_exec($ch);
    curl_close($ch);

    return json_decode($res, true);
}

// ======= HÀM COMPLETE JOB =======
function completeJob($auth, $token, $ads_id, $accountId) {
    $url = "https://gateway.golike.net/api/advertising/publishers/tiktok/complete-jobs";

    $json_data = json_encode([
        'ads_id' => $ads_id,
        'account_id' => $accountId,
        'async' => true,
        'data' => null,
    ]);

    $headers = array_merge(getHeaders($auth, $token), [
        "Content-Type: application/json",
        "Content-Length: " . strlen($json_data)
    ]);

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $json_data,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_TIMEOUT => 6
    ]);

    $res = curl_exec($ch);
    curl_close($ch);

    return json_decode($res, true);
}

// ======= HÀM SKIP JOB =======
function baoloi($auth, $token, $ads_id, $object_id, $account_id, $loai = 'tiktok') {
    $headers = array_merge(getHeaders($auth, $token), [
        "Content-Type: application/json"
    ]);

    // Bước 1: Báo lỗi job
    $reportData = json_encode([
        'description' => 'Tôi đã làm Job này rồi',
        'users_advertising_id' => $ads_id,
        'type' => 'ads',
        'provider' => 'tiktok',
        'fb_id' => $account_id,
        'error_type' => 6,
    ]);

    $ch1 = curl_init('https://gateway.golike.net/api/report/send');
    curl_setopt_array($ch1, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $reportData,
        CURLOPT_HTTPHEADER => $headers,
    ]);
    curl_exec($ch1);
    curl_close($ch1);

    // Bước 2: Skip job nâng cao
    $skipData = json_encode([
        'ads_id' => $ads_id,
        'object_id' => $object_id,
        'account_id' => $account_id,
        'type' => $loai,
    ]);

    $ch2 = curl_init('https://gateway.golike.net/api/advertising/publishers/tiktok/skip-jobs');
    curl_setopt_array($ch2, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $skipData,
        CURLOPT_HTTPHEADER => $headers,
    ]);
    $response = curl_exec($ch2);
    curl_close($ch2);

    return json_decode($response, true);
}

//HÀM DELAY
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
//FUNCTION XOÁ DÒNG
function clearLine($text) {
    echo "\r" . str_repeat(" ", mb_strlen($text)) . "\r";
}
//jj
function clearCurrentLine() {
    // Di chuyển về đầu dòng, xoá hết dòng
    echo "\r\033[K";
}
