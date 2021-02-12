<?php

return [
    'metrika/stats.php' => ['GET', 'Actions@LinkToMetrikaStats'],
    'api/selfUpdate.me' => ['GET', 'Actions@ConnectorUpdate'],
    'api/selfUpdate' => ['GET', 'Actions@ConnectorUpdate'],
    'api/updateSettings.me' => ['POST', 'Actions@UpdateSettings'],
    'api/getLocation.me' => ['GET', 'Actions@GetLocation'],
    'api/getLocation' => ['GET', 'Actions@GetLocation'],
    'send.php' => ['POST', 'Actions@SendForm']
];