<?php defined('KOOWA') or die; ?>

<?php
$params = array();

if ($key) {
    $params[] = 'key='.$key;
}

if (is_array($libraries) && count($libraries)) {
    $params[] = 'libraries='.implode(',', $libraries);
}
?>
<script src="https://maps.googleapis.com/maps/api/js<?= (count($params)) ? '?'.implode('&', $params) : ''; ?>" />
