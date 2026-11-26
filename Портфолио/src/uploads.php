<?php
declare(strict_types=1);

const MAX_UPLOAD = 10 * 1024 * 1024;

function storeDocument(array $file, string $storage): array
{
    $error = $file['error'] ?? UPLOAD_ERR_NO_FILE;
    if (in_array($error, [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true)) {
        throw new DomainException('Максимальный размер документа — 10 МБ.');
    }
    if ($error !== UPLOAD_ERR_OK) {
        throw new DomainException($error === UPLOAD_ERR_NO_FILE ? 'Выберите подтверждающий документ.' : 'Не удалось загрузить документ.');
    }
    if ($file['size'] < 1 || $file['size'] > MAX_UPLOAD) {
        throw new DomainException('Загрузите непустой документ размером до 10 МБ.');
    }
    $name = basename(str_replace('\\', '/', $file['name']));
    $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    if (!in_array($extension, ['pdf', 'jpg', 'jpeg'], true) || !mb_check_encoding($name) || mb_strlen($name) > 255) {
        throw new DomainException('Выберите PDF или JPG с именем до 255 символов.');
    }
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    $pdf = $extension === 'pdf';
    $valid = $pdf ? $mime === 'application/pdf'
        : $mime === 'image/jpeg' && (getimagesize($file['tmp_name'])[2] ?? null) === IMAGETYPE_JPEG;
    if (!$valid) {
        throw new DomainException('Содержимое документа не соответствует формату PDF или JPG.');
    }
    if (!is_dir($storage) || !is_writable($storage)) {
        throw new RuntimeException('Каталог документов недоступен для записи.');
    }
    $stored = bin2hex(random_bytes(20)) . ($pdf ? '.pdf' : '.jpg');
    if (!move_uploaded_file($file['tmp_name'], $storage . '/' . $stored)) {
        throw new RuntimeException('Не удалось сохранить документ.');
    }
    return [$stored, $name];
}
