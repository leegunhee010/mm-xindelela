<?php
/**
 * 로컬 SQLite(writable/mm.db)의 실제 데이터 → 운영 서버(MySQL)용 초기 데이터 SQL
 *   php scripts/export-initial-data.php > docs/sql/015-initial-data.sql
 *
 * - 데모(DEMO-* 상품, '(데모)' 공급사, 데모 회원·주문)는 넣지 않는다
 * - 처음 배포 때 한 번만 들어간다: settings.initial_data_v1 표시가 있으면 모든 INSERT 를 건너뜀
 *   (운영 중에 관리자가 지운 상품이 배포 때마다 되살아나지 않게)
 * - 공급사·카테고리는 이름으로 찾아 연결 (id 가 서버와 달라도 됨)
 */
$db = new SQLite3(__DIR__ . '/../writable/mm.db');

function q($v): string
{
    if ($v === null) {
        return 'NULL';
    }
    if (is_int($v) || is_float($v)) {
        return (string) $v;
    }
    return "'" . str_replace(['\\', "'", "\0"], ['\\\\', "\\'", '\\0'], (string) $v) . "'";
}

function rows(SQLite3 $db, string $sql): array
{
    $out = [];
    $r   = $db->query($sql);
    while ($x = $r->fetchArray(SQLITE3_ASSOC)) {
        $out[] = $x;
    }
    return $out;
}

const GUARD = "NOT EXISTS (SELECT 1 FROM `settings` WHERE `skey`='initial_data_v1')";

function ins(string $table, array $vals, string $exists): string
{
    $cols = '`' . implode('`,`', array_keys($vals)) . '`';
    return "INSERT INTO `{$table}` ({$cols}) SELECT " . implode(',', $vals) . " FROM DUAL WHERE " . GUARD . " AND NOT EXISTS ({$exists});";
}

$o   = [];
$o[] = '-- ============================================================';
$o[] = '-- 실제 판매 데이터 초기 입력 (카테고리·공급사·상품·배너·공지). 데모 없음.';
$o[] = '-- scripts/export-initial-data.php 로 로컬 DB 에서 만든 파일. 손으로 고치지 말 것.';
$o[] = '-- 처음 배포 때 한 번만 들어가고, 이후엔 settings.initial_data_v1 표시 때문에 건너뜀.';
$o[] = '-- ============================================================';
$o[] = '';

foreach (rows($db, "SELECT * FROM categories ORDER BY sort, id") as $c) {
    $o[] = ins('categories', ['name' => q($c['name']), 'name_my' => q($c['name_my']), 'slug' => q($c['slug']), 'sort' => (int) $c['sort'], 'is_active' => (int) $c['is_active'], 'created_at' => 'NOW()', 'updated_at' => 'NOW()'],
        "SELECT 1 FROM `categories` WHERE `name`=" . q($c['name']));
}
$o[] = '';

foreach (rows($db, "SELECT * FROM suppliers WHERE company_name NOT LIKE '%데모%' ORDER BY id") as $s) {
    $v = ['user_id' => 0];
    foreach (['company_name', 'name_my', 'brand', 'intro', 'logo', 'contact_name', 'phone', 'address', 'biz_no', 'website', 'status'] as $k) {
        $v[$k] = q($s[$k] ?? null);
    }
    $v['created_at'] = 'NOW()';
    $v['updated_at'] = 'NOW()';
    $o[] = ins('suppliers', $v, "SELECT 1 FROM `suppliers` WHERE `company_name`=" . q($s['company_name']));
}
$o[] = '';

$skip = ['id', 'supplier_id', 'category_id', 'views', 'order_count', 'created_at', 'updated_at', 'deleted_at'];
foreach (rows($db, "SELECT p.*, s.company_name AS _sup, c.name AS _cat FROM products p LEFT JOIN suppliers s ON s.id=p.supplier_id LEFT JOIN categories c ON c.id=p.category_id WHERE p.code NOT LIKE 'DEMO-%' ORDER BY p.id") as $p) {
    $v = [
        'supplier_id' => '(SELECT `id` FROM `suppliers` WHERE `company_name`=' . q($p['_sup']) . ' LIMIT 1)',
        'category_id' => '(SELECT `id` FROM `categories` WHERE `name`=' . q($p['_cat']) . ' LIMIT 1)',
    ];
    foreach ($p as $k => $val) {
        if (in_array($k, $skip, true) || $k[0] === '_') {
            continue;
        }
        $v[$k] = is_int($val) ? $val : q($val);
    }
    $v['created_at'] = 'NOW()';
    $v['updated_at'] = 'NOW()';
    $o[] = ins('products', $v, "SELECT 1 FROM `products` WHERE `code`=" . q($p['code']));
}
$o[] = '';

foreach (rows($db, "SELECT * FROM banners ORDER BY sort, id") as $b) {
    $v = [];
    foreach (['title', 'title_my', 'subtitle', 'subtitle_my', 'image', 'link', 'bg'] as $k) {
        $v[$k] = q($b[$k]);
    }
    $v['sort'] = (int) $b['sort'];
    $v['is_active'] = (int) $b['is_active'];
    $v['created_at'] = 'NOW()';
    $v['updated_at'] = 'NOW()';
    $o[] = ins('banners', $v, "SELECT 1 FROM `banners` WHERE `image`=" . q($b['image']));
}
$o[] = '';

foreach (rows($db, "SELECT * FROM notices ORDER BY id") as $n) {
    $o[] = ins('notices', ['title' => q($n['title']), 'title_my' => q($n['title_my']), 'body' => q($n['body']), 'body_my' => q($n['body_my']), 'is_pinned' => (int) $n['is_pinned'], 'created_at' => 'NOW()', 'updated_at' => 'NOW()'],
        "SELECT 1 FROM `notices` WHERE `title`=" . q($n['title']));
}
$o[] = '';
$o[] = "-- 한 번 넣었다는 표시";
$o[] = "INSERT INTO `settings` (`skey`,`svalue`,`updated_at`) SELECT 'initial_data_v1', NOW(), NOW() FROM DUAL WHERE " . GUARD . ";";
$o[] = '';
$o[] = "SELECT (SELECT COUNT(*) FROM `products`) AS products, (SELECT COUNT(*) FROM `suppliers`) AS suppliers, (SELECT COUNT(*) FROM `categories`) AS categories, (SELECT COUNT(*) FROM `banners`) AS banners;";
echo implode("\n", $o) . "\n";
