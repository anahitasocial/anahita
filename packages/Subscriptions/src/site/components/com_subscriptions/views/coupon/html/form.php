<?php defined('KOOWA') or die('Restricted access'); ?>

<?php $coupon = empty( $coupon ) ? @service('repos:subscriptions.coupon')->getEntity()->reset() : $coupon; ?>

