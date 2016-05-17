ALTER TABLE `compose_article_item`
CHANGE `type` `type` enum('text','contact','boxes','fullPageImage','slideshow','form','carousel') COLLATE 'utf8_general_ci' NOT NULL AFTER `id`;