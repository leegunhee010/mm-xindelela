<section class="corner-section pc:mx-auto pc:w-[544px] mx-auto pc:pt-[44px] text-center">
  <picture><img alt="" class="mx-auto mb-[24px] size-[100px]" src="/images/member/unified_member.svg"></picture>
  <h3 class="title3 font-bold">주문 신청이 접수되었습니다</h3>
  <p class="body3 text-gray3 mt-[8px]">주문번호 <b class="text-black sub-head1"><?= esc($o['order_no']) ?></b></p>
  <ol class="text-left mt-[28px] rounded-[12px] bg-gray9 p-[20px] body3 leading-[2] list-decimal pl-[36px]">
    <li>담당자가 재고와 배송비를 확인한 뒤 <b>최종 금액</b>을 마이페이지와 Viber로 안내합니다.</li>
    <li>안내받은 금액을 <b>KBZPay</b>로 송금해 주세요.</li>
    <li>마이페이지 › 주문 상세에서 <b>송금 캡처</b>를 올려 주세요.</li>
    <li>입금 확인 즉시 한국 공급사에 발주하고, 배송이 시작되면 알려 드립니다.</li>
  </ol>
  <div class="mt-[16px] rounded-[8px] border border-gray6 p-[14px] sub-head2 font-bold"><?= esc(mm_setting('kbzpay_account', '')) ?></div>
  <div class="btn-area shrink-0 flex btn-grid02 sub-head2 mt-[24px] gap-[8px]">
    <a href="<?= site_url('products') ?>" class="text-black bg-white border border-gray6 min-h-[44px] px-[16px] py-[12px] body3 !leading-none w-full rounded-full font-bold text-center">쇼핑 계속하기</a>
    <a href="<?= site_url('mypage/orders/' . $o['id']) ?>" class="text-white bg-primary min-h-[44px] px-[16px] py-[12px] body3 !leading-none w-full rounded-full font-bold text-center">주문 상세 보기</a>
  </div>
</section>
