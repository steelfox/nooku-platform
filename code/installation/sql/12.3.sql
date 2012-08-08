# --------------------------------------------------------
# Removing unused extensions

-- Remove administrator latest news module
-- http://nooku.assembla.com/spaces/nooku-server/tickets/217-remove-administrator-latest-news-module
DELETE FROM `#__modules` WHERE `id` = 4;

DELETE FROM `#__modules` WHERE `module` = 'mod_toolbar';
DELETE FROM `#__modules` WHERE `module` = 'mod_submenu';
DELETE FROM `#__modules` WHERE `module` = 'mod_title';

-- Remove mod_related_items
DELETE FROM `#__modules` WHERE `module` = 'mod_related_items';

-- Remove core logs
DROP TABLE `#__core_log_items`, `#__core_log_searches`;

-- Remove messages functionality
DROP TABLE `#__messages`, `#__messages_cfg`;

-- Remove unused tables
DROP TABLE #__stats_agents;
DROP TABLE #__migration_backlinks
DROP TABLE #__groups
DROP TABLE #__templates_menu

-- Remove components
DELETE FROM `#__components` WHERE `option` = 'com_wrapper';
DELETE FROM `#__components` WHERE `option` = 'com_massmail';
DELETE FROM `#__components` WHERE `option` = 'com_mailto';
DELETE FROM `#__components` WHERE `option` = 'com_templates';
DELETE FROM `#__components` WHERE `option` = 'com_messages';

# --------------------------------------------------------
# com_extensions schema changes

-- Remove plugins
DROP TABLE #__plugins;

# --------------------------------------------------------
# com_contacts schema changes

-- Rename contacts_details to contacts_contacts
RENAME TABLE  `#__contact_details` TO `#__contacts`;
UPDATE `#__categories` SET `section` = 'com_contacts' WHERE `section` = 'com_contact_details';

# --------------------------------------------------------
# com_users schema changes

-- Update timezone offsets in user params.
UPDATE `#__users` SET `params` = REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(`params`, 'timezone=-12', 'timezone=Etc/GMT-12'), 'timezone=-11', 'timezone=Pacific/Midway'), 'timezone=-10', 'timezone=Pacific/Honolulu'), 'timezone=-9.5', 'timezone=Pacific/Marquesas'), 'timezone=-9', 'timezone=US/Alaska'), 'timezone=-8', 'timezone=US/Pacific'), 'timezone=-7', 'timezone=US/Mountain'), 'timezone=-6', 'timezone=US/Central'), 'timezone=-5', 'timezone=US/Eastern'), 'timezone=-4.5', 'timezone=America/Caracas'), 'timezone=-4', 'timezone=America/Barbados'), 'timezone=-3.5', 'timezone=Canada/Newfoundland'), 'timezone=-3', 'timezone=America/Buenos_Aires'), 'timezone=-2', 'timezone=Atlantic/South_Georgia'), 'timezone=-1', 'timezone=Atlantic/Azores'), 'timezone=0', 'timezone=Europe/London'), 'timezone=1', 'timezone=Europe/Amsterdam'), 'timezone=2', 'timezone=Europe/Istanbul'), 'timezone=3', 'timezone=Asia/Riyadh'), 'timezone=3.5', 'timezone=Asia/Tehran'), 'timezone=4', 'timezone=Asia/Muscat'), 'timezone=4.5', 'timezone=Asia/Kabul'), 'timezone=5', 'timezone=Asia/Karachi'), 'timezone=5.5', 'timezone=Asia/Calcutta'), 'timezone=5.75', 'timezone=Asia/Katmandu'), 'timezone=6', 'timezone=Asia/Dhaka'), 'timezone=6.5', 'timezone=Indian/Cocos'), 'timezone=7', 'timezone=Asia/Bangkok'), 'timezone=8', 'timezone=Australia/Perth'), 'timezone=8.75', 'timezone=Australia/West'), 'timezone=9', 'timezone=Asia/Tokyo'), 'timezone=9.5', 'timezone=Australia/Adelaide'), 'timezone=10', 'timezone=Australia/Brisbane'), 'timezone=10.5', 'timezone=Australia/Lord_Howe'), 'timezone=11', 'timezone=Pacific/Kosrae'), 'timezone=11.5', 'timezone=Pacific/Norfolk'), 'timezone=12', 'timezone=Pacific/Auckland'), 'timezone=12.75', 'timezone=Pacific/Chatham'), 'timezone=13', 'timezone=Pacific/Tongatapu'), 'timezone=14', 'timezone=Pacific/Kiritimati');

-- Remove unused indexes
ALTER TABLE jos_users DROP INDEX idx_name;
ALTER TABLE jos_users DROP INDEX gid_block;

-- Update indexes
ALTER TABLE  `jos_users` DROP INDEX  `email` , ADD UNIQUE  `email` (  `email` );

-- Remove unused columns from #__session
RENAME TABLE`#__session` TO  `#__users_sessions`;

ALTER TABLE `#__users_sessions` DROP `username`;
ALTER TABLE `#__users_sessions` DROP `usertype`;
ALTER TABLE `#__users_sessions` DROP `gid`;
ALTER TABLE `#__users_sessions` CHANGE  `session_id`  `users_session_id` VARCHAR( 128 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `#__users_sessions` CHANGE  `userid`  `email` VARCHAR( 100 ) NOT NULL COMMENT  '@Filter("email")'
ALTER TABLE `#__users_sessions` DROP INDEX  `userid`;

ALTER TABLE `#__users` DROP `username`;

# --------------------------------------------------------
# com_content schema changes

-- Upgrade modules rows
UPDATE `#__modules` SET `module` = 'mod_articles', `params` = CONCAT_WS('\n', 'show_content=1', `params`) WHERE `module` = 'mod_newsflash';
UPDATE `#__modules` SET `module` = 'mod_articles' WHERE `module` = 'mod_latestnews';
UPDATE `#__modules` SET `params` = REPLACE(REPLACE(REPLACE(REPLACE(`params`, 'catid', 'category'), 'secid', 'section'), 'show_front', 'show_featured'), 'items', 'count') WHERE `module` = 'mod_articles';

-- Rename tables to follow conventions
RENAME TABLE `#__content` TO `#__articles`;
RENAME TABLE `#__content_frontpage` TO `#__articles_featured`;

-- Update schema to follow conventions
ALTER TABLE `#__articles` CHANGE `id` `articles_article_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `#__articles_featured` CHANGE `content_id` `articles_article_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0;
ALTER TABLE `#__articles` CHANGE  `catid`  `categories_category_id` INT( 11 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE `#__articles` DROP INDEX `idx_catid` ADD INDEX  `category` (  `categories_category_id` );

-- Remove unused columns
ALTER TABLE `#__articles` DROP `title_alias`;
ALTER TABLE `#__articles` DROP `mask`;
ALTER TABLE `#__articles` DROP `images`;
ALTER TABLE `#__articles` DROP `urls`;
ALTER TABLE `#__articles` DROP `version`;
ALTER TABLE `#__articles` DROP `parentid`;
ALTER TABLE `#__articles` DROP `hits`
ALTER TABLE `#__articles` DROP `sectionid`

-- Remove loadmodule plugin
DELETE FROM `#__extensions_plugins` WHERE `element` = 'loadmodule' AND `folder` = 'content';

-- Remove pagenavigation plugin
DELETE FROM `#__extensions_plugins` WHERE `element` = 'pagenavigation' AND `folder` = 'content';

-- Remove unused table
DROP TABLE #__content_rating;

# --------------------------------------------------------
# com_categories schema changes

-- Remove unused categories
DELETE FROM `#__categories` WHERE `section` = 'com_content';
DELETE FROM `#__categories` WHERE `section` = 'com_newsfeeds';
DELETE FROM `#__categories` WHERE `section` = 'com_banner';

-- Set parent_id of com_articles categories to the section
UPDATE `#__categories` SET `parent_id` = `section` , `section` = 'com_articles' WHERE `section` > 0;

-- Remove the com_ prefix, the section now refers to the table
UPDATE `#__categories` SET `section` = REPLACE(`section`,'com_','');

-- Migrate date from sections to categories
ALTER TABLE #__categories ADD old_id int(11) NOT NULL;
INSERT INTO #__categories (parent_id, title, alias, image, `table`, description, published, checked_out, checked_out_time, ordering, access, count, params, old_id)
SELECT 0, title, alias, image, 'articles', description, published, checked_out, checked_out_time, ordering, access, count, params, id FROM #__sections;
UPDATE #__categories a, #__categories b SET a.parent_id = b.id WHERE b.old_id = a.parent_id AND a.parent_id != 0
UPDATE #__menu a, #__categories b SET a.link = REPLACE(a.link, CONCAT('id=', b.old_id), CONCAT('id=', b.id)) WHERE `link` LIKE '%com_content%' AND `link` LIKE '%view=section%' AND `link` LIKE CONCAT('%id=', b.old_id ,'%');
ALTER TABLE #__categories DROP old_id;
DROP TABLE #__sections;

-- Update schema to follow conventions
ALTER TABLE `#__categories` CHANGE  `section` `table` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE `#__categories` DROP `count`;
ALTER TABLE `#__categories` CHANGE  `id`  `categories_category_id` INT( 11 ) NOT NULL AUTO_INCREMENT;

# --------------------------------------------------------
# com_newsfeeds schema changes

-- Remove com_newsfeeds
DROP TABLE `#__newsfeeds`;
DELETE FROM `#__components` WHERE `parent` = 11 OR `option` = 'com_newsfeeds';

-- Remove mod_feed
DELETE FROM `#__modules` WHERE `module` = 'mod_feed';

-- Remove menu links to newsfeeds component
DELETE FROM `#__menu` WHERE `componentid` = 11;

# --------------------------------------------------------
# com_banners schema changes

-- Remove com_banners
DROP TABLE `#__banner`, `#__bannerclient`, `#__bannertrack`;
DELETE FROM `#__components` WHERE `parent` = 1 OR `option` = 'com_banners';

-- Remove mod_feed
DELETE FROM `#__modules` WHERE `module` = 'mod_banners';

-- Remove menu links to banners component
DELETE FROM `#__menu` WHERE `componentid` = 1;

# --------------------------------------------------------
# com_polls schema changes

DELETE FROM `#__components` WHERE `option` = 'com_poll';
DROP TABLE `#__polls`, `#__poll_data`, `#__poll_date`, `#__poll_menu`;

DELETE FROM `#__modules` WHERE `module` = 'mod_poll';

# --------------------------------------------------------
# com_installer schema changes

-- Remove com_installer
DELETE FROM `#__components` WHERE `id` = 22

# --------------------------------------------------------
# com_categories schema changes

-- Remove unused columns
ALTER TABLE `#__categories` DROP `image_position`;
ALTER TABLE `#__categories` DROP `name`;
ALTER TABLE `#__categories` DROP `editor`;

# --------------------------------------------------------
# com_weblinks schema changes

-- Remove unused columns
ALTER TABLE `#__weblinks` DROP `sid`;
ALTER TABLE `#__weblinks` DROP `archived`;
ALTER TABLE `#__weblinks` DROP `approved`;

-- Remove weblink submission links
DELETE FROM `#__menu` WHERE `link` = 'index.php?option=com_weblinks&view=weblink&layout=form';

-- Update components table
UPDATE `#__components` SET `link` = 'option=com_weblinks&view=categories' WHERE `link` = 'option=com_categories&section=com_weblinks';

-- Update schema to follow conventions
ALTER TABLE  `#__weblinks` CHANGE  `id`  `weblinks_weblink_id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE  `#__weblinks` DROP PRIMARY KEY , ADD PRIMARY KEY (  `weblinks_weblink_id` );

ALTER TABLE  `#__weblinks` CHANGE  `catid`  `categories_category_id` INT( 11 ) NOT NULL DEFAULT  '0';
ALTER TABLE  `#__weblinks` DROP INDEX  `catid` , ADD INDEX  `category` (  `categories_category_id` );

# --------------------------------------------------------
# com_contacts schema changes

-- Update schema to follow conventions
ALTER TABLE  `#__contacts` CHANGE  `id`  `contacts_contact_id` INT( 11 ) NOT NULL AUTO_INCREMENT;
ALTER TABLE  `#__contacts` DROP PRIMARY KEY , ADD PRIMARY KEY (  `contacts_contact_id` );

ALTER TABLE  `#__contacts` CHANGE  `catid`  `categories_category_id` INT( 11 ) NOT NULL DEFAULT  '0';
ALTER TABLE  `#__contacts` DROP INDEX  `catid` , ADD INDEX  `category` (  `categories_category_id` );

ALTER TABLE `jos_contacts` DROP `imagepos`;

-- Update components table
UPDATE `#__components` SET `link` = 'option=com_contacts&view=categories' WHERE `link` = 'option=com_categories&section=com_contact_details';

# --------------------------------------------------------
# com_pages schema changes

--  Upgrade menu items links
UPDATE `#__menu` SET `link` = REPLACE(`link`, 'com_content', 'com_articles') WHERE `link` LIKE '%com_content%';
UPDATE `#__menu` SET `link` = REPLACE(`link`, 'view=category&layout=blog', 'view=articles') WHERE `link` LIKE '%com_articles%' AND `link` LIKE '%view=category&layout=blog%';
UPDATE `#__menu` SET `link` = REPLACE(`link`, 'view=section&layout=blog', 'view=articles') WHERE `link` LIKE '%com_articles%' AND `link` LIKE '%view=section&layout=blog%';
UPDATE `#__menu` SET `link` = REPLACE(`link`, 'view=category', 'view=articles&layout=table') WHERE `link` LIKE '%com_articles%' AND `link` LIKE '%view=category%';
UPDATE `#__menu` SET `link` = REPLACE(`link`, 'view=section', 'view=articles&layout=table') WHERE `link` LIKE '%com_articles%' AND `link` LIKE '%view=section%';
UPDATE `#__menu` SET `link` = REPLACE(`link`, 'id=', 'category=') WHERE `link` LIKE '%com_articles%' AND `link` LIKE '%view=articles%';
UPDATE `#__menu` SET `link` = REPLACE(`link`, '&layout=blog', '') WHERE `link` LIKE '%com_articles%' AND `link` LIKE '%view=articles%';
UPDATE `#__menu` SET `link` = REPLACE(`link`, 'view=frontpage', 'view=articles'), `params` = CONCAT_WS('\n', 'show_featured=1', `params`) WHERE `link` LIKE '%com_articles%' AND `link` LIKE '%view=frontpage%';

# --------------------------------------------------------
# com_modules schema changes

DELETE FROM `#__modules` WHERE `module` = 'mod_footer';
DELETE FROM `#__modules` WHERE `module` = 'mod_wrapper';
DELETE FROM `#__modules` WHERE `module` = 'mod_stats';
DELETE FROM `#__modules` WHERE `module` = 'mod_whoisonline';
DELETE FROM `#__modules` WHERE `module` = 'mod_sections';


# --------------------------------------------------------
# change engine to InnoDB

ALTER TABLE `#__articles` ENGINE = INNODB;
ALTER TABLE `#__articles_featured` ENGINE = INNODB;
ALTER TABLE `#__categories` ENGINE = INNODB;
ALTER TABLE `#__components` ENGINE = INNODB;
ALTER TABLE `#__contacts` ENGINE = INNODB;
ALTER TABLE  `#__menu` ENGINE = INNODB;
ALTER TABLE  `#__menu_types` ENGINE = INNODB;
ALTER TABLE  `#__modules` ENGINE = INNODB;
ALTER TABLE  `#__modules_menu` ENGINE = INNODB;
ALTER TABLE  `#__extensions_plugins` ENGINE = INNODB;
ALTER TABLE  `#__users_sessions` ENGINE = INNODB;
ALTER TABLE  `#__weblinks` ENGINE = INNODB;

# --------------------------------------------------------
# com_menus schema changes

-- Rename component
UPDATE `#__components` SET `name` = 'Pages', `admin_menu_alt` = 'Pages', `option` = 'com_pages' WHERE `id` = 25;
UPDATE `#__components` SET `admin_menu_link` = '' WHERE `admin_menu_link` = 'option=com_files';

-- Rename tables to follow conventions
RENAME TABLE `#__modules_menu` TO `#__pages_modules`;
RENAME TABLE `#__menu` TO `#__pages_pages`;
RENAME TABLE `#__menu_types` TO `#__pages_menus`;

-- Update schema to follow conventions
ALTER TABLE `#__pages_pages` CHANGE `id` `pages_page_id` INT UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `#__pages_pages` CHANGE `name` `title` VARCHAR(255) NOT NULL;
ALTER TABLE `#__pages_pages` CHANGE `alias` `slug` VARCHAR(255);
ALTER TABLE `#__pages_pages` MODIFY `type` VARCHAR(50);
ALTER TABLE `#__pages_pages` CHANGE `published` `enabled` BOOLEAN NOT NULL DEFAULT 0;
ALTER TABLE `#__pages_pages` CHANGE `componentid` `component_id` INT UNSIGNED;
ALTER TABLE `#__pages_pages` CHANGE `checked_out` `locked_by` INT UNSIGNED;
ALTER TABLE `#__pages_pages` CHANGE `checked_out_time` `locked_on` DATETIME;
ALTER TABLE `#__pages_pages` ADD COLUMN `hidden` BOOLEAN NOT NULL DEFAULT 0 AFTER `enabled`;
ALTER TABLE `#__pages_pages` MODIFY `home` BOOLEAN NOT NULL DEFAULT 0 AFTER `hidden`;

ALTER TABLE `#__pages_menus` CHANGE `id` `pages_menu_id` INT UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `#__pages_menus` CHANGE `menutype` `slug` VARCHAR(255) AFTER `title`;
ALTER TABLE `#__pages_menus` MODIFY `title` VARCHAR(255) NOT NULL;
ALTER TABLE `#__pages_menus` MODIFY `description` VARCHAR(255);

ALTER TABLE `#__pages_modules` CHANGE `moduleid` `modules_module_id` INT UNSIGNED NOT NULL;
ALTER TABLE `#__pages_modules` CHANGE `menuid` `pages_page_id` INT UNSIGNED NOT NULL;

ALTER TABLE `#__pages_pages` ADD COLUMN `pages_menu_id` INT UNSIGNED NOT NULL AFTER `pages_page_id`;
UPDATE `#__pages_pages` AS `pages`, `#__pages_menus` AS `menus` SET `pages`.`pages_menu_id` = `menus`.`pages_menu_id` WHERE `menus`.`slug` = `pages`.`menutype`;

ALTER TABLE `#__pages_pages` DROP INDEX `componentid`;
ALTER TABLE `#__pages_pages` ADD INDEX `ix_enabled` (`enabled`);
ALTER TABLE `#__pages_pages` ADD INDEX `ix_component_id` (`component_id`);
ALTER TABLE `#__pages_pages` ADD INDEX `ix_home` (`home`);
ALTER TABLE `#__pages_pages` ADD CONSTRAINT `pages_menu_id` FOREIGN KEY (`pages_menu_id`) REFERENCES `#__pages_menus` (`pages_menu_id`) ON DELETE CASCADE;

ALTER TABLE `#__pages_modules` ADD INDEX `ix_pages_page_id` (`pages_page_id`);

-- Update existing data
UPDATE `#__components` SET `admin_menu_link` = 'option=com_articles&view=articles' WHERE `admin_menu_link` = 'option=com_articles';
UPDATE `#__components` SET `admin_menu_link` = 'option=com_contacts&view=contacts' WHERE `admin_menu_link` = 'option=com_contacts' OR `link` = 'option=com_contacts';
UPDATE `#__components` SET `admin_menu_link` = 'option=com_weblinks&view=weblinks' WHERE `admin_menu_link` = 'option=com_weblinks' OR `link` = 'option=com_weblinks';

UPDATE `#__modules` SET `title` = 'Admin Pages', `module` = 'mod_pages' WHERE `module` = 'mod_menu' AND `client_id` = 1;
UPDATE `#__modules` SET `module` = 'mod_pages' WHERE `module` = 'mod_mainmenu';
UPDATE `#__modules` AS `modules` SET `modules`.`params` = REPLACE(`modules`.`params`, CONCAT('menutype=', SUBSTRING_INDEX(SUBSTRING_INDEX(`modules`.`params`, 'menutype=', -1), '\n', 1)), CONCAT('menu_id=', (SELECT `id` FROM `#__pages_menus` AS `menus` WHERE `menus`.`slug` = SUBSTRING_INDEX(SUBSTRING_INDEX(`modules`.`params`, 'menutype=', -1), '\n', 1)))) WHERE `modules`.`module` = 'mod_pages';

DELETE FROM `#__pages_pages` WHERE `enabled` < 0;

-- Add relations table
CREATE TABLE IF NOT EXISTS `#__pages_page_relations` (
    `ancestor_id` INT UNSIGNED NOT NULL,
    `descendant_id` INT UNSIGNED NOT NULL,
    `level` TINYINT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (`ancestor_id`, `descendant_id`),
    CONSTRAINT `ancestor_id` FOREIGN KEY (`ancestor_id`) REFERENCES `#__pages_pages` (`pages_page_id`) ON DELETE CASCADE,
    CONSTRAINT `descendant_id` FOREIGN KEY (`descendant_id`) REFERENCES `#__pages_pages` (`pages_page_id`) ON DELETE CASCADE,
    INDEX `ix_level` (`level`),
    INDEX `ix_descendant_id` (`descendant_id`)
) ENGINE = InnoDB CHARSET = utf8;

-- Convert adjacency hierarchy to closure
DROP PROCEDURE IF EXISTS `#__convert_adjacency_to_closure`;

DELIMITER //
CREATE PROCEDURE #__convert_adjacency_to_closure()
BEGIN
    DECLARE distance TINYINT UNSIGNED DEFAULT 0;
    DECLARE max_level TINYINT UNSIGNED;
    SELECT MAX(`sublevel`) INTO max_level FROM `#__pages_pages`;

    TRUNCATE `#__pages_page_relations`;
    INSERT INTO `#__pages_page_relations` SELECT `pages_page_id`, `pages_page_id`, 0 FROM `#__pages_pages`;

    WHILE distance < max_level DO
        INSERT INTO `#__pages_page_relations`
            SELECT `relations`.`ancestor_id`, `pages`.`pages_page_id`, distance + 1
            FROM `#__pages_page_relations` AS `relations`, `#__pages_pages` AS `pages`
            WHERE `relations`.`descendant_id` = `pages`.`parent` AND `relations`.`level` = distance;

        SET distance = distance + 1;
    END WHILE;
END//
DELIMITER ;

CALL #__convert_adjacency_to_closure();
DROP PROCEDURE #__convert_adjacency_to_closure;

-- Add orderings table
CREATE TABLE `#__pages_page_orderings` (
    `pages_page_id` INT UNSIGNED NOT NULL,
    `title` INT UNSIGNED NOT NULL DEFAULT 0,
    `custom` INT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (`pages_page_id`),
    CONSTRAINT `pages_page_id` FOREIGN KEY (`pages_page_id`) REFERENCES `#__pages_pages` (`pages_page_id`) ON DELETE CASCADE,
    INDEX `ix_title` (`title`),
    INDEX `ix_custom` (`custom`)
) ENGINE = InnoDB CHARSET = utf8;

-- Populate id and custom columns
INSERT INTO `#__pages_page_orderings` (`pages_page_id`, `custom`) SELECT `pages_page_id`, `ordering` AS `custom` FROM `#__pages_pages`;

-- Populate title column
DROP PROCEDURE IF EXISTS `#__populate_ordering_title`;

DELIMITER //
CREATE PROCEDURE #__populate_ordering_title()
BEGIN
    DECLARE menu_id INT;
    DECLARE distance TINYINT UNSIGNED DEFAULT 0;
    DECLARE max_level TINYINT UNSIGNED;
    DECLARE done BOOLEAN DEFAULT FALSE;
    DECLARE menu_cursor CURSOR FOR SELECT DISTINCT `pages_menu_id` FROM `#__pages_pages`;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    OPEN menu_cursor;
    menu_loop: LOOP
        FETCH menu_cursor INTO menu_id;

        IF done THEN
            LEAVE menu_loop;
        END IF;

        SELECT MAX(`sublevel`) INTO max_level FROM `#__pages_pages` WHERE `pages_menu_id` = menu_id;
        SET distance = 0;

        WHILE distance <= max_level DO
            SET @index = 1, @parent_id = -1;
            UPDATE `#__pages_page_orderings` AS `orderings`, (SELECT `pages_page_id`, @index := IF(@parent_id = `parent`, @index + 1, 1) AS `index`, @parent_id := `parent` FROM `#__pages_pages` WHERE `pages_menu_id` = menu_id AND `sublevel` = distance ORDER BY `parent`, `title` ASC) AS `pages`
                SET `orderings`.`title` = `index` WHERE `orderings`.`pages_page_id` = `pages`.`pages_page_id`;

            SET distance = distance + 1;
        END WHILE;
    END LOOP;
    CLOSE menu_cursor;
END//
DELIMITER ;

CALL #__populate_ordering_title();
DROP PROCEDURE #__populate_ordering_title;

-- Drop unnecessary columns
ALTER TABLE `#__pages_pages` DROP COLUMN `menutype`;
ALTER TABLE `#__pages_pages` DROP COLUMN `parent`;
ALTER TABLE `#__pages_pages` DROP COLUMN `sublevel`;
ALTER TABLE `#__pages_pages` DROP COLUMN `ordering`;
ALTER TABLE `#__pages_pages` DROP COLUMN `pollid`;
ALTER TABLE `#__pages_pages` DROP COLUMN `browserNav`;
ALTER TABLE `#__pages_pages` DROP COLUMN `utaccess`;
ALTER TABLE `#__pages_pages` DROP COLUMN `lft`;
ALTER TABLE `#__pages_pages` DROP COLUMN `rgt`