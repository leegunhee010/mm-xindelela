<?php

namespace App\Filters;

use App\Models\AdminLogModel;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * 관리자 작업 기록. 관리자 화면의 저장·처리(POST)가 성공하면 누가·무엇을 했는지 admin_logs 에 남긴다.
 * 컨트롤러마다 코드를 넣지 않고 주소(경로)+입력값으로 한 곳에서 해석한다.
 */
class AdminActivity implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        if (strtoupper($request->getMethod()) !== 'POST') {
            return null;
        }
        $u = mm_user();
        if (! $u || $u['role'] !== 'admin' || session()->getFlashdata('error')) {
            return null;
        }
        $path = trim(preg_replace('#^admin/?#', '', uri_string()), '/');
        try {
            [$action, $type, $id, $summary] = $this->describe($path, $request, $response);
        } catch (\Throwable $e) {
            [$action, $type, $id, $summary] = ['etc', null, null, $path];
        }
        if ($action === null) {
            return null;
        }
        (new AdminLogModel())->insert([
            'admin_id'    => (int) $u['id'],
            'admin_name'  => $u['name'],
            'action'      => $action,
            'target_type' => $type,
            'target_id'   => $id,
            'summary'     => mb_substr($summary, 0, 500),
            'ip'          => $request->getIPAddress(),
            'created_at'  => date('Y-m-d H:i:s'),
        ]);
        return null;
    }

    private function name(string $table, ?int $id, string $col): string
    {
        if (! $id) {
            return '';
        }
        $r = db_connect()->table($table)->select($col)->where('id', $id)->get()->getRowArray();
        return $r ? (string) $r[$col] : '#' . $id;
    }

    private function idFromRedirect(ResponseInterface $res, string $seg): ?int
    {
        $loc = $res->getHeaderLine('Location');
        return preg_match('#' . $seg . '/(\d+)#', $loc, $m) ? (int) $m[1] : null;
    }

    /** @return array{0:?string,1:?string,2:?int,3:string} */
    private function describe(string $p, RequestInterface $rq, ResponseInterface $rs): array
    {
        $post = fn ($k) => trim((string) $rq->getPost($k));
        if (str_starts_with($p, 'upload/')) {
            return [null, null, null, ''];
        }
        // 주문
        if (preg_match('#^orders/(\d+)/(status|ship|memo)$#', $p, $m)) {
            $id = (int) $m[1];
            $no = $this->name('orders', $id, 'order_no');
            if ($m[2] === 'status') {
                $st = $post('status');
                $label = ['paid' => '입금 확인', 'shipped' => '출고(배송중)', 'delivered' => '배송 완료', 'cancelled' => '주문 취소'][$st] ?? ('상태 변경 → ' . mm_order_status_label($st));
                return ['order_' . $st, 'order', $id, "주문 {$no} {$label}"];
            }
            return $m[2] === 'ship' ? ['order_ship', 'order', $id, "주문 {$no} 배송 정보 수정"] : ['order_memo', 'order', $id, "주문 {$no} 메모 저장"];
        }
        // 회원
        if (preg_match('#^buyers/(\d+)/(approve|reject|status|note|points)$#', $p, $m)) {
            $id = (int) $m[1];
            $nm = $this->name('users', $id, 'company_name');
            return match ($m[2]) {
                'approve' => ['buyer_approve', 'buyer', $id, "회원 {$nm} 사업자 인증 승인"],
                'reject'  => ['buyer_reject', 'buyer', $id, "회원 {$nm} 반려: " . $post('reason')],
                'status'  => ['buyer_status', 'buyer', $id, "회원 {$nm} 상태 → " . mm_status_label($post('status'))],
                'note'    => ['buyer_note', 'buyer', $id, "회원 {$nm} 관리자 메모 저장"],
                'points'  => ['buyer_points', 'buyer', $id, "회원 {$nm} 포인트 " . ((int) $post('amount') > 0 ? '+' : '') . number_format((int) $post('amount')) . 'P (' . $post('memo') . ')'],
            };
        }
        // 상품
        if ($p === 'products/save') {
            $id = (int) $post('id') ?: $this->idFromRedirect($rs, 'products');
            return [$post('id') ? 'product_update' : 'product_create', 'product', $id, ($post('id') ? '상품 수정: ' : '상품 등록: ') . $post('name')];
        }
        if (preg_match('#^products/(\d+)/(status|best|delete)$#', $p, $m)) {
            $id = (int) $m[1];
            $nm = $this->name('products', $id, 'name');
            return match ($m[2]) {
                'status' => ['product_status', 'product', $id, "상품 {$nm} → " . mm_product_status_label($post('status'))],
                'best'   => ['product_best', 'product', $id, "상품 {$nm} BEST 변경"],
                'delete' => ['product_delete', 'product', $id, "상품 삭제: {$nm}"],
            };
        }
        // 공급사
        if ($p === 'suppliers/store' || preg_match('#^suppliers/(\d+)/(save|status)$#', $p, $m)) {
            $id = isset($m[1]) ? (int) $m[1] : ((int) $post('id') ?: $this->idFromRedirect($rs, 'suppliers'));
            if (($m[2] ?? '') === 'status') {
                return ['supplier_status', 'supplier', $id, '공급사 ' . $this->name('suppliers', $id, 'company_name') . ' 상태 → ' . mm_status_label($post('status'))];
            }
            return [$post('company_name') !== '' && ! $post('id') && $p === 'suppliers/store' ? 'supplier_create' : 'supplier_update', 'supplier', $id, '공급사 ' . ($post('company_name') ?: $this->name('suppliers', $id, 'company_name')) . ' 정보 저장'];
        }
        // 카테고리·공지·배너·문의·설정·SEO·담당자
        if ($p === 'categories/save') {
            return [$post('id') ? 'category_update' : 'category_create', 'category', (int) $post('id') ?: null, '카테고리 저장: ' . $post('name')];
        }
        if (preg_match('#^categories/(\d+)/delete$#', $p, $m)) {
            return ['category_delete', 'category', (int) $m[1], '카테고리 삭제 #' . $m[1]];
        }
        if ($p === 'notices/save') {
            return ['notice_save', 'notice', (int) $post('id') ?: null, '공지 저장: ' . $post('title')];
        }
        if (preg_match('#^notices/(\d+)/delete$#', $p, $m)) {
            return ['notice_delete', 'notice', (int) $m[1], '공지 삭제 #' . $m[1]];
        }
        if ($p === 'banners/save' || preg_match('#^banners/(\d+)/delete$#', $p, $m)) {
            return [isset($m[1]) ? 'banner_delete' : 'banner_save', 'banner', isset($m[1]) ? (int) $m[1] : ((int) $post('id') ?: null), isset($m[1]) ? '메인 배너 삭제' : '메인 배너 저장: ' . $post('title')];
        }
        if (preg_match('#^inquiries/(\d+)/answer$#', $p, $m)) {
            return ['inquiry_answer', 'inquiry', (int) $m[1], '문의 답변 #' . $m[1]];
        }
        if ($p === 'settings') {
            return ['settings_save', 'settings', null, '설정 저장'];
        }
        if ($p === 'seo') {
            return ['seo_save', 'settings', null, 'SEO 설정 저장'];
        }
        if ($p === 'staff/save') {
            return ['staff_save', 'staff', (int) $post('id') ?: null, '담당자 계정 저장: ' . $post('name') . ' (' . $post('email') . ')'];
        }
        return ['etc', null, null, $p];
    }
}
