<?php

// $app->post('/samcart', 'App\Integrations\SamCartToImprovely::run');
$app->post('/samcart', 'App\Integrations\SamCartToWoopra::run');
