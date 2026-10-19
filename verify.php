<?php
$bot_token = 'YOUR-BOT-TOKEN'; // توکن ربات خود را اینجا بگذارید

// اطلاعات دریافتی از طریق GET
$data = $_GET;

// اگر هش ارسال نشده بود، درخواست نامعتبر است
if (!isset($data['hash'])) {
    die("Unauthorized");
}

$received_hash = $data['hash'];
unset($data['hash']); // هش را از آرایه حذف می‌کنیم تا در محاسبه شرکت نکند

// ۱. مرتب‌سازی کلیدها به ترتیب الفبا
ksort($data);

// ۲. ساخت رشته داده‌ها با فرمت key=value
$data_check_arr = [];
foreach ($data as $key => $value) {
    $data_check_arr[] = $key . '=' . $value;
}
$data_check_string = implode("\n", $data_check_arr);

// ۳. محاسبه کلید مخفی با استفاده از توکن ربات (SHA256 از توکن)

$secret_key = hash('sha256', $bot_token, true);

// ۴. محاسبه HMAC-SHA256
$calculated_hash = hash_hmac('sha256', $data_check_string, $secret_key);

// ۵. مقایسه هش‌ها
if (strcmp($calculated_hash, $received_hash) !== 0) {
    die("Data is NOT from Telegram!");
}

// ۶. بررسی زمان (جلوگیری از حملات تکرار - مثلاً اعتبار تا 24 ساعت آینده)
if ((time() - $data['auth_date']) > 86400) {
    die("Authentication data is outdated!");
}

// احراز هویت موفقیت‌آمیز است!
echo "سلام " . htmlspecialchars($data['first_name']) . "! ورود با موفقیت انجام شد.";
// اینجا می‌توانید کاربر را لاگین کنید یا در دیتابیس ثبت/آپدیت کنید.