<?php
/**
 * Render 시작 때 실행: 환경변수로 관리자 비밀번호와 구글맵 키를 넣는다.
 * - ADMIN_PASSWORD : 데모 관리자(admin@xindelela.com) 비밀번호. 공개 주소라 admin1234 를 그대로 두지 않는다.
 * - GOOGLE_MAPS_KEY: 관리자 설정의 구글맵 키
 */
$db = new SQLite3(__DIR__ . '/../writable/mm.db');

$pw = getenv('ADMIN_PASSWORD');
if ($pw !== false && $pw !== '') {
    $s = $db->prepare("UPDATE users SET password_hash = :h WHERE email = 'admin@xindelela.com'");
    $s->bindValue(':h', password_hash($pw, PASSWORD_DEFAULT));
    $s->execute();
    echo "admin password set\n";
}

$key = getenv('GOOGLE_MAPS_KEY');
if ($key !== false && $key !== '') {
    $id = $db->querySingle("SELECT id FROM settings WHERE skey = 'google_maps_key'");
    $s  = $id
        ? $db->prepare('UPDATE settings SET svalue = :v WHERE id = ' . (int) $id)
        : $db->prepare("INSERT INTO settings (skey, svalue) VALUES ('google_maps_key', :v)");
    $s->bindValue(':v', $key);
    $s->execute();
    echo "maps key set\n";
}
