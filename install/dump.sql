CREATE TABLE `users` (
`user_id` int(11) NOT NULL AUTO_INCREMENT,
`email` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
`password` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
`name` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
`billing` text COLLATE utf8mb4_unicode_ci,
`api_key` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`token_code` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
`twofa_secret` varchar(16) DEFAULT NULL,
`one_time_login_code` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`pending_email` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`email_activation_code` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
`lost_password_code` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
`facebook_id` bigint(20) DEFAULT NULL,
`type` int(11) NOT NULL DEFAULT '0',
`active` int(11) NOT NULL DEFAULT '0',
`plan_id` varchar(16) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
`plan_expiration_date` datetime DEFAULT NULL,
`plan_settings` text COLLATE utf8_unicode_ci,
`plan_trial_done` tinyint(4) DEFAULT '0',
`plan_expiry_reminder` tinyint(4) DEFAULT '0',
`payment_subscription_id` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
`referral_key` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`referred_by` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`referred_by_has_converted` tinyint(4) DEFAULT '0',
`language` varchar(32) COLLATE utf8_unicode_ci DEFAULT 'english',
`timezone` varchar(32) DEFAULT 'UTC',
`date` datetime DEFAULT NULL,
`ip` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
`country` varchar(32) DEFAULT NULL,
`last_activity` datetime DEFAULT NULL,
`last_user_agent` text COLLATE utf8_unicode_ci,
`total_logins` int(11) DEFAULT '0',
PRIMARY KEY (`user_id`),
KEY `plan_id` (`plan_id`),
KEY `api_key` (`api_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- SEPARATOR --

INSERT INTO `users` (`user_id`, `email`, `password`, `api_key`, `referral_key`, `name`, `token_code`, `email_activation_code`, `lost_password_code`, `facebook_id`, `type`, `active`, `plan_id`, `plan_expiration_date`, `plan_settings`, `plan_trial_done`, `payment_subscription_id`, `date`, `ip`, `last_activity`, `last_user_agent`, `total_logins`)
VALUES (1,'admin','$2y$10$uFNO0pQKEHSFcus1zSFlveiPCB3EvG9ZlES7XKgJFTAl5JbRGFCWy', md5(rand()), md5(rand()), 'Admin','','','',NULL,1,1,'custom','2030-01-01 12:00:00', '{"additional_global_domains":true,"custom_url":true,"deep_links":true,"no_ads":true,"removable_branding":true,"custom_branding":true,"custom_colored_links":true,"statistics":true,"custom_backgrounds":true,"verified":true,"temporary_url_is_enabled":true,"seo":true,"utm":true,"socials":true,"fonts":true,"password":true,"sensitive_content":true,"leap_link":true,"api_is_enabled":true,"affiliate_is_enabled":true,"projects_limit":-1,"pixels_limit":-1,"biolinks_limit":-1,"links_limit":-1,"domains_limit":-1,"enabled_biolink_blocks":{"link":true,"text":true,"image":true,"mail":true,"soundcloud":true,"spotify":true,"youtube":true,"twitch":true,"vimeo":true,"tiktok":true,"applemusic":true,"tidal":true,"anchor":true,"twitter_tweet":true,"instagram_media":true,"rss_feed":true,"custom_html":true,"vcard":true,"image_grid":true,"divider":true}}', 1,'',NOW(),'',NOW(),'',0);

-- SEPARATOR --

CREATE TABLE `projects` (
`project_id` int(11) NOT NULL AUTO_INCREMENT,
`user_id` int(11) NOT NULL,
`name` varchar(64) NOT NULL DEFAULT '',
`color` varchar(16) DEFAULT '#000',
`last_datetime` datetime DEFAULT NULL,
`datetime` datetime NOT NULL,
PRIMARY KEY (`project_id`),
KEY `user_id` (`user_id`),
CONSTRAINT `projects_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

-- SEPARATOR --

CREATE TABLE `links` (
`link_id` int(11) NOT NULL AUTO_INCREMENT,
`project_id` int(11) DEFAULT NULL,
`user_id` int(11) NOT NULL,
`biolink_id` int(11) DEFAULT NULL,
`domain_id` int(11) DEFAULT '0',
`pixels_ids` text,
`type` varchar(32) NOT NULL DEFAULT '',
`subtype` varchar(32) DEFAULT NULL,
`url` varchar(256) NOT NULL DEFAULT '',
`location_url` varchar(512) DEFAULT NULL,
`clicks` int(11) NOT NULL DEFAULT '0',
`settings` text,
`start_date` datetime DEFAULT NULL,
`end_date` datetime DEFAULT NULL,
`is_enabled` tinyint(4) NOT NULL DEFAULT '1',
`datetime` datetime NOT NULL,
PRIMARY KEY (`link_id`),
KEY `project_id` (`project_id`),
KEY `user_id` (`user_id`),
KEY `url` (`url`),
KEY `links_subtype_index` (`subtype`),
KEY `links_type_index` (`type`),
KEY `links_links_link_id_fk` (`biolink_id`),
CONSTRAINT `links_ibfk_2` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE SET NULL ON UPDATE CASCADE,
CONSTRAINT `links_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT `links_links_link_id_fk` FOREIGN KEY (`biolink_id`) REFERENCES `links` (`link_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ROW_FORMAT=DYNAMIC ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- SEPARATOR --

CREATE TABLE `biolinks_blocks` (
`biolink_block_id` int(11) NOT NULL AUTO_INCREMENT,
`user_id` int(11) NOT NULL,
`link_id` int(11) DEFAULT NULL,
`type` varchar(32) NOT NULL DEFAULT '',
`location_url` varchar(512) DEFAULT NULL,
`clicks` int(11) NOT NULL DEFAULT '0',
`settings` text,
`order` int(11) NOT NULL DEFAULT '0',
`start_date` datetime DEFAULT NULL,
`end_date` datetime DEFAULT NULL,
`is_enabled` tinyint(4) NOT NULL DEFAULT '1',
`datetime` datetime NOT NULL,
PRIMARY KEY (`biolink_block_id`),
KEY `user_id` (`user_id`),
KEY `links_type_index` (`type`),
KEY `links_links_link_id_fk` (`link_id`),
CONSTRAINT `biolinks_blocks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT `biolinks_blocks_ibfk_2` FOREIGN KEY (`link_id`) REFERENCES `links` (`link_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- SEPARATOR --

CREATE TABLE `pixels` (
`pixel_id` int(11) NOT NULL AUTO_INCREMENT,
`user_id` int(11) NOT NULL,
`type` varchar(64) NOT NULL,
`name` varchar(64) NOT NULL,
`pixel` varchar(64) NOT NULL,
`last_datetime` datetime DEFAULT NULL,
`datetime` datetime NOT NULL,
PRIMARY KEY (`pixel_id`),
KEY `user_id` (`user_id`),
CONSTRAINT `pixels_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- SEPARATOR --

CREATE TABLE `plans` (
`plan_id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(256) NOT NULL DEFAULT '',
`description` varchar(256) NOT NULL DEFAULT '',
`monthly_price` float NULL,
`annual_price` float NULL,
`lifetime_price` float NULL,
`trial_days` int unsigned NOT NULL DEFAULT '0',
`settings` text NOT NULL,
`taxes_ids` text,
`color` varchar(16) DEFAULT NULL,
`status` tinyint(4) NOT NULL,
`order` int(10) unsigned DEFAULT '0',
`date` datetime NOT NULL,
PRIMARY KEY (`plan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- SEPARATOR --

CREATE TABLE `pages_categories` (
`pages_category_id` int(11) NOT NULL AUTO_INCREMENT,
`url` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
`title` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
`description` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '',
`icon` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`order` int(11) NOT NULL DEFAULT '0',
PRIMARY KEY (`pages_category_id`),
KEY `url` (`url`)
) ROW_FORMAT=DYNAMIC ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SEPARATOR --

CREATE TABLE `pages` (
`page_id` int(11) NOT NULL AUTO_INCREMENT,
`pages_category_id` int(11) DEFAULT NULL,
`url` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
`title` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
`description` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`content` longtext COLLATE utf8mb4_unicode_ci,
`type` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT '',
`position` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
`order` int(11) DEFAULT '0',
`total_views` int(11) DEFAULT '0',
`date` datetime DEFAULT NULL,
`last_date` datetime DEFAULT NULL,
PRIMARY KEY (`page_id`),
KEY `pages_pages_category_id_index` (`pages_category_id`),
KEY `pages_url_index` (`url`),
CONSTRAINT `pages_pages_categories_pages_category_id_fk` FOREIGN KEY (`pages_category_id`) REFERENCES `pages_categories` (`pages_category_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SEPARATOR --

INSERT INTO `pages` (`pages_category_id`, `url`, `title`, `description`, `content`, `type`, `position`, `order`, `total_views`, `date`, `last_date`) VALUES
(NULL, 'https://altumcode.com/', 'Software by AltumCode', '', '', 'external', 'bottom', 1, 0, NOW(), NOW()),
(NULL, 'https://altumco.de/phpbiolinks-buy', 'Built with phpBiolinks', '', '', 'external', 'bottom', 0, 0, NOW(), NOW());

-- SEPARATOR --

CREATE TABLE `track_links` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`user_id` int(11) NOT NULL,
`link_id` int(11) DEFAULT NULL,
`biolink_block_id` int(11) DEFAULT NULL,
`project_id` int(11) DEFAULT NULL,
`country_code` varchar(8) DEFAULT NULL,
`city_name` varchar(128) DEFAULT NULL,
`os_name` varchar(16) DEFAULT NULL,
`browser_name` varchar(32) DEFAULT NULL,
`referrer_host` varchar(256) DEFAULT NULL,
`referrer_path` varchar(1024) DEFAULT NULL,
`device_type` varchar(16) DEFAULT NULL,
`browser_language` varchar(16) DEFAULT NULL,
`utm_source` varchar(128) DEFAULT NULL,
`utm_medium` varchar(128) DEFAULT NULL,
`utm_campaign` varchar(128) DEFAULT NULL,
`is_unique` tinyint(4) DEFAULT '0',
`datetime` datetime NOT NULL,
PRIMARY KEY (`id`),
KEY `link_id` (`link_id`),
KEY `track_links_date_index` (`datetime`),
KEY `track_links_project_id_index` (`project_id`),
KEY `track_links_users_user_id_fk` (`user_id`),
KEY `track_links_biolink_block_id_index` (`biolink_block_id`),
CONSTRAINT `track_links_ibfk_1` FOREIGN KEY (`link_id`) REFERENCES `links` (`link_id`) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT `track_links_links_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `links` (`project_id`) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT `track_links_projects_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE SET NULL ON UPDATE CASCADE,
CONSTRAINT `track_links_users_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ROW_FORMAT=DYNAMIC ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- SEPARATOR --

CREATE TABLE `settings` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`key` varchar(64) NOT NULL DEFAULT '',
`value` longtext NOT NULL,
PRIMARY KEY (`id`),
UNIQUE KEY `key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- SEPARATOR --
SET @cron_key = MD5(RAND());
-- SEPARATOR --
SET @cron_reset_date = NOW();

-- SEPARATOR --
INSERT INTO `settings` (`key`, `value`)
VALUES
('main', '{"se_indexing":true}'),
('ads', '{"header":"","footer":"","header_biolink":"","footer_biolink":""}'),
('captcha', '{"type":"basic","recaptcha_public_key":"","recaptcha_private_key":"","login_is_enabled":0,"register_is_enabled":0,"lost_password_is_enabled":0,"resend_activation_is_enabled":0}'),
('cron', concat('{\"key\":\"', @cron_key, '\"}')),
('default_language', 'english'),
('default_theme_style', 'light'),
('email_confirmation', '0'),
('register_is_enabled', '1'),
('email_notifications', '{\"emails\":\"\",\"new_user\":\"0\",\"new_payment\":\"0\"}'),
('facebook', '{\"is_enabled\":\"0\",\"app_id\":\"\",\"app_secret\":\"\"}'),
('google', '{\"is_enabled\":\"0\",\"client_id\":\"\",\"client_secret\":\"\"}'),
('twitter', '{\"is_enabled\":\"0\",\"consumer_api_key\":\"\",\"consumer_api_secret\":\"\"}'),
('favicon', ''),
('logo', ''),
('opengraph', ''),
('plan_custom', '{\"plan_id\":\"custom\",\"name\":\"Custom\",\"status\":1}'),
('plan_free', '{"plan_id":"free","name":"Free","days":null,"status":1,"settings":{"additional_global_domains":true,"custom_url":true,"deep_links":true,"no_ads":true,"removable_branding":true,"custom_branding":true,"custom_colored_links":true,"statistics":true,"custom_backgrounds":true,"verified":true,"temporary_url_is_enabled":true,"seo":true,"utm":true,"socials":true,"fonts":true,"password":true,"sensitive_content":true,"leap_link":true,"api_is_enabled":true,"affiliate_is_enabled":true,"projects_limit":10,"pixels_limit":10,"biolinks_limit":15,"links_limit":25,"domains_limit":1,"enabled_biolink_blocks":{"link":true,"text":true,"image":true,"mail":true,"soundcloud":true,"spotify":true,"youtube":true,"twitch":true,"vimeo":true,"tiktok":true,"applemusic":true,"tidal":true,"anchor":true,"twitter_tweet":true,"instagram_media":true,"rss_feed":true,"custom_html":true,"vcard":true,"image_grid":true,"divider":true}}}'),
('payment', '{\"is_enabled\":\"0\",\"type\":\"both\",\"brand_name\":\"phpBiolinks\",\"currency\":\"USD\", \"codes_is_enabled\": false}'),
('paypal', '{\"is_enabled\":\"0\",\"mode\":\"sandbox\",\"client_id\":\"\",\"secret\":\"\"}'),
('stripe', '{\"is_enabled\":\"0\",\"publishable_key\":\"\",\"secret_key\":\"\",\"webhook_secret\":\"\"}'),
('offline_payment', '{\"is_enabled\":\"0\",\"instructions\":\"Your offline payment instructions go here..\"}'),
('coinbase', '{\"is_enabled\":\"0\"}'),
('smtp', '{\"host\":\"\",\"from\":\"\",\"from_name\":\"\",\"encryption\":\"tls\",\"port\":\"587\",\"auth\":\"0\",\"username\":\"\",\"password\":\"\"}'),
('custom', '{\"head_js\":\"\",\"head_css\":\"\"}'),
('socials', '{\"facebook\":\"\",\"instagram\":\"\",\"twitter\":\"\",\"youtube\":\"\"}'),
('default_timezone', 'UTC'),
('title', 'phpBiolinks'),
('privacy_policy_url', ''),
('terms_and_conditions_url', ''),
('index_url', ''),
('announcements', '{"id":"","content":"","show_logged_in":"","show_logged_out":""}'),
('business', '{\"invoice_is_enabled\":\"0\",\"name\":\"\",\"address\":\"\",\"city\":\"\",\"county\":\"\",\"zip\":\"\",\"country\":\"\",\"email\":\"\",\"phone\":\"\",\"tax_type\":\"\",\"tax_id\":\"\",\"custom_key_one\":\"\",\"custom_value_one\":\"\",\"custom_key_two\":\"\",\"custom_value_two\":\"\"}'),
('webhooks', '{"user_new": "", "user_delete": ""}'),
('links', '{"branding":"by AltumCode","shortener_is_enabled":"1","domains_is_enabled":"1","main_domain_is_enabled":"1","blacklisted_domains":"","blacklisted_keywords":"","google_safe_browsing_is_enabled":"","google_safe_browsing_api_key":"","avatar_size_limit":"2","background_size_limit":"2","thumbnail_image_size_limit":"2","image_size_limit":"2"}'),
('license', '{\"license\":\"\",\"type\":\"\"}'),
('product_info', '{\"version\":\"11.0.0\", \"code\":\"1100\"}');

-- SEPARATOR --

CREATE TABLE `users_logs` (
`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`user_id` int(11) DEFAULT NULL,
`type` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`ip` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`device_type` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`os_name` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`country_code` varchar(8) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`datetime` datetime DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `users_logs_user_id` (`user_id`),
CONSTRAINT `users_logs_users_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- SEPARATOR --

CREATE TABLE `domains` (
`domain_id` int(11) NOT NULL AUTO_INCREMENT,
`user_id` int(11) DEFAULT NULL,
`scheme` varchar(8) NOT NULL DEFAULT '',
`host` varchar(256) NOT NULL DEFAULT '',
`custom_index_url` varchar(256) DEFAULT NULL,
`custom_not_found_url` varchar(256) DEFAULT NULL,
`type` tinyint(11) DEFAULT '1',
`is_enabled` tinyint(4) DEFAULT '0',
`datetime` datetime DEFAULT NULL,
`last_datetime` datetime DEFAULT NULL,
PRIMARY KEY (`domain_id`),
KEY `user_id` (`user_id`),
KEY `host` (`host`),
KEY `type` (`type`),
CONSTRAINT `domains_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ROW_FORMAT=DYNAMIC ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
-- SEPARATOR --
update settings set value = '{"license":"babiato.co","type":"Extended License"}' where `key` = 'license';
-- SEPARATOR -- 
CREATE TABLE `payments` ( `id` int(11) unsigned NOT NULL AUTO_INCREMENT, `user_id` int(11) DEFAULT NULL, `plan_id` int(11) DEFAULT NULL, `processor` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL, `type` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL, `frequency` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL, `payment_id` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL, `subscription_id` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL, `payer_id` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL, `email` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL, `name` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL, `billing` text COLLATE utf8mb4_unicode_ci, `taxes_ids` text COLLATE utf8mb4_unicode_ci, `base_amount` float DEFAULT NULL, `total_amount` float DEFAULT NULL, `code` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL, `discount_amount` float DEFAULT NULL, `currency` varchar(4) COLLATE utf8mb4_unicode_ci DEFAULT NULL, `payment_proof` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL, `status` tinyint(4) DEFAULT '1', `date` datetime DEFAULT NULL, PRIMARY KEY (`id`), KEY `payments_user_id` (`user_id`), KEY `plan_id` (`plan_id`), CONSTRAINT `payments_plans_plan_id_fk` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`plan_id`) ON DELETE SET NULL ON UPDATE CASCADE, CONSTRAINT `payments_users_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; 
-- SEPARATOR -- 
CREATE TABLE IF NOT EXISTS `codes` ( `code_id` int(11) NOT NULL AUTO_INCREMENT, `type` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL, `days` int(11) DEFAULT NULL COMMENT 'only applicable if type is redeemable', `plan_id` int(16) DEFAULT NULL, `code` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '', `discount` int(11) NOT NULL, `quantity` int(11) NOT NULL DEFAULT '1', `redeemed` int(11) NOT NULL DEFAULT '0', `date` datetime NOT NULL, PRIMARY KEY (`code_id`), KEY `type` (`type`), KEY `code` (`code`), KEY `plan_id` (`plan_id`), CONSTRAINT `codes_ibfk_1` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`plan_id`) ON DELETE CASCADE ON UPDATE CASCADE ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; 
-- SEPARATOR -- 
CREATE TABLE IF NOT EXISTS `redeemed_codes` ( `id` int(11) NOT NULL AUTO_INCREMENT, `code_id` int(11) NOT NULL, `user_id` int(11) NOT NULL, `date` datetime NOT NULL, PRIMARY KEY (`id`), KEY `code_id` (`code_id`), KEY `user_id` (`user_id`), CONSTRAINT `redeemed_codes_ibfk_1` FOREIGN KEY (`code_id`) REFERENCES `codes` (`code_id`) ON DELETE CASCADE ON UPDATE CASCADE, CONSTRAINT `redeemed_codes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; 
-- SEPARATOR -- 
CREATE TABLE `taxes` ( `tax_id` int(11) unsigned NOT NULL AUTO_INCREMENT, `internal_name` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL, `name` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL, `description` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL, `value` int(11) DEFAULT NULL, `value_type` enum('percentage','fixed') COLLATE utf8mb4_unicode_ci DEFAULT NULL, `type` enum('inclusive','exclusive') COLLATE utf8mb4_unicode_ci DEFAULT NULL, `billing_type` enum('personal','business','both') COLLATE utf8mb4_unicode_ci DEFAULT NULL, `countries` text COLLATE utf8mb4_unicode_ci, `datetime` datetime DEFAULT NULL, PRIMARY KEY (`tax_id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;