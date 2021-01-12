<?php

return [
    'metrika/stats.php' => ['GET', 'Actions@LinkToMetrikaStats'],
    'cloakit/stats.php' => ['GET', 'Actions@LinkToCloakIt'],
    'api/selfUpdate.me' => ['GET', 'Actions@ConnectorUpdate'],
    'api/updateSettings.me' => ['POST', 'Actions@UpdateSettings'],
    'api/getLocation.me' => ['GET', 'Actions@GetLocation'],
    'send.php' => ['POST', 'Actions@SendForm']
];