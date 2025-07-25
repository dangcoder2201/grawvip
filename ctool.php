<?php
error_reporting(0);

$url = 'https://raw.githubusercontent.com/dangcoder2201/grawvip/main/golikev1.py';
$context = stream_context_create(['http' => ['timeout' => 5]]);
$code = @file_get_contents($url, false, $context);

if (!$code || strlen(trim($code)) < 10) {
    echo "❌ Không thể tải dữ liệu từ máy chủ. Vui lòng thử lại sau!\n";
    exit;
}

$descriptorspec = [
    0 => ['pipe', 'r'],
    1 => ['pipe', 'w'],
    2 => ['pipe', 'w']
];

$process = proc_open('python3 -', $descriptorspec, $pipes);

if (is_resource($process)) {
    fwrite($pipes[0], $code);
    fclose($pipes[0]);

    $output = stream_get_contents($pipes[1]);
    $error = stream_get_contents($pipes[2]);

    fclose($pipes[1]);
    fclose($pipes[2]);

    proc_close($process);

    echo $output; // ✅ CHỈ hiển thị kết quả Python trả về
    if ($error) {
        echo "\n❌ $error\n";
    }
} else {
    echo "❌ Không thể khởi chạy Python!\n";
}
?>