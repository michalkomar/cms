ALTER TABLE `user`
ADD `identity_no` varchar(40) COLLATE 'utf8_czech_ci' NULL AFTER `id`;

ALTER TABLE `user`
DROP `degree`;

ALTER TABLE `user`
DROP `is_allowed`;