<?php

// $app->post("/samcart", "Integrations\SamCartToImprovely:run");
$app->post("/samcart", "Integrations\SamCartToWoopra:run");
