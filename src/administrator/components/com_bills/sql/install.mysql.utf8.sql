
CREATE TABLE IF NOT EXISTS `#__bill_groups` ( `id` INT(11) NOT NULL AUTO_INCREMENT, `created_by` INT(11) NOT NULL, `created_date` TIMESTAMP NOT NULL , `title` VARCHAR(1000) NOT NULL , `description` VARCHAR(5000) NOT NULL , `params` VARCHAR(1000) NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `#__bill_bill_types` ( `id` INT NOT NULL AUTO_INCREMENT, `title` VARCHAR(500) NOT NULL , `created_by` INT NOT NULL , `created_date` TIMESTAMP NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `#__bill_user_bills` ( `id` INT NOT NULL AUTO_INCREMENT, `created_by` INT NOT NULL, `created_date` TIMESTAMP NOT NULL , `for_users` VARCHAR(1000) NOT NULL , `params` VARCHAR(1000) NOT NULL , `bill_type` INT NOT NULL , `title` VARCHAR(1000) NOT NULL , `description` VARCHAR(2000) NOT NULL , `attachments` VARCHAR(1000) NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `#__bill_user_map` ( `id` INT NOT NULL AUTO_INCREMENT, `bill_id` INT NOT NULL , `user_id` INT NOT NULL , `amount` DOUBLE NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;

ALTER TABLE `#__bill_groups` ADD `members` VARCHAR(2000) NOT NULL AFTER `description`;
ALTER TABLE `#__bill_user_bills` ADD `group_id` INT NOT NULL AFTER `created_date`;
ALTER TABLE `#__bill_user_bills` ADD `amount` DOUBLE NOT NULL AFTER `created_date`;
/*
	INSERT INTO `ichal_bill_groups` (`id`, `created_by`, `created_date`, `title`, `description`, `params`) VALUES (NULL, '198', CURRENT_TIMESTAMP, 'Test Group1', 'This is test group1', '');
*/


