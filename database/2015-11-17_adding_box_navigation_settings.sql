ALTER TABLE `box`
CHANGE `show_navigation` `show_filters` int(1) NOT NULL DEFAULT '1' AFTER `status`;

ALTER TABLE `box`
ADD `show_navigation` int(1) NOT NULL DEFAULT '1';