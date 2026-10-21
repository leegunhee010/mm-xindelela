-- 가입 축하 포인트 4,000P (인증 승인 시 지급, 1P = 1Ks). 관리자가 이미 바꾼 값은 건드리지 않음.
UPDATE `settings` SET `svalue`='4000' WHERE `skey`='points_signup_bonus' AND `svalue`='0';
