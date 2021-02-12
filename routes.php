<?php

return [
    'metrika/stats.php' => ['GET', 'Actions@LinkToMetrikaStats'],
    'cloakit/stats.php' => ['GET', 'Actions@LinkToCloakIt'],

    'api/selfUpdate.me' => ['GET', 'Actions@ConnectorUpdate'],
    'api/selfUpdate' => ['GET', 'Actions@ConnectorUpdate'],

    'api/getLocation.me' => ['GET', 'Actions@GetLocation'],
    'api/getLocation' => ['GET', 'Actions@GetLocation'],

    'send.php' => ['POST', 'Actions@SendForm']
];