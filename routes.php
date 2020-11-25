<?php

return [
    'api/selfUpdate.me' => ['GET', 'Actions@ConnectorUpdate'],
    'api/copySite.me' => ['GET', 'Actions@MakePublicCopy'],
    'api/updateSettings.me' => ['POST', 'Actions@UpdateSettings'],
    'send.php' => ['POST', 'Actions@SendForm']
];