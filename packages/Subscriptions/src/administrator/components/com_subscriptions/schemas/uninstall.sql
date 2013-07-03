-- --------------------------------------------------------

DROP TABLE IF EXISTS `#__subscriptions_coupons`;

-- --------------------------------------------------------

DROP TABLE IF EXISTS `#__subscriptions_packages`;

-- --------------------------------------------------------

DROP TABLE IF EXISTS `#__subscriptions_transactions`;

-- --------------------------------------------------------

DROP TABLE IF EXISTS `#__subscriptions_vats`;

DELETE #__migrator_versions  WHERE `component` = 'subscriptions';