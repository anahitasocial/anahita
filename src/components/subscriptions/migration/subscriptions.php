<?php 

function subscriptions_1()
{
    dbexec(dbparse(file_get_contents(dirname(__FILE__).'/schema.sql')));
    dbexec('ALTER TABLE jos_subscriptions_subscriptions DROP end_date');
    dbexec('ALTER TABLE jos_subscriptions_subscriptions CHANGE coupon_id coupon_id INT(11) NULL');
    dbexec('ALTER TABLE jos_subscriptions_transactions ADD COLUMN upgrade TINYINT(1) NOT NULL DEFAULT 0');
    dbexec('ALTER TABLE jos_subscriptions_packages DROP currency');
    dbexec('ALTER TABLE jos_subscriptions_coupons  DROP package_id');
    dbexec('DROP TABLE  jos_subscriptions_subscriptions');
    dbexec("UPDATE jos_anahita_nodes SET type = 'AnSeEntityNode,ComSubscriptionsDomainEntityPackage,com.subscriptions.domain.entity.package' WHERE type LIKE 'AnSeEntityNode,AnSeEntityMedium,ComSubscriptionsDomainEntityPackage,ComSubscriptionsDomainEntityPackageGlobal,com.subscriptions.domain.entity.package.global'");
    dbexec("UPDATE jos_anahita_edges SET node_b_type = 'com.subscriptions.domain.entity.package' WHERE node_b_type LIKE 'com.subscriptions.domain.entity.package.global'");
}

function subscriptions_2()
{
	dbexec('ALTER TABLE `jos_subscriptions_packages` ADD COLUMN `recurring` TINYINT(1) NOT NULL');
	dbexec('ALTER TABLE `jos_subscriptions_packages` ADD COLUMN `billing_period` VARCHAR(10) NOT NULL DEFAULT \'\' ');
	
	dbexec('ALTER TABLE `jos_subscriptions_transactions` ADD COLUMN `recurring` TINYINT(1) NOT NULL');
	dbexec('ALTER TABLE `jos_subscriptions_transactions` ADD COLUMN `billing_period` VARCHAR(10) NOT NULL DEFAULT \'\' ');
}

        