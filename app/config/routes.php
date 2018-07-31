<?php

// $app->post("/samcart", "Startupbros\Integrations\SamCartToImprovely:run");
$app->post("/samcart", "Startupbros\Integrations\SamCartToWoopra:run");
