<?php

return [
    'metrika/stats.php' => ['GET', 'Actions@LinkToMetrikaStats'],
    'cloakit/stats.php' => ['GET', 'Actions@LinkToCloakIt'],

    'api/selfUpdate.me' => ['GET', 'Actions@ConnectorUpdate'],
    'api/selfUpdate' => ['GET', 'Actions@ConnectorUpdate'],

    'api/copySite.me' => ['GET', 'Actions@MakePublicCopy'],
    // 'api/copySite' => ['GET', 'Actions@MakePublicCopy'],

    'api/backupSite.me' => ['GET', 'Actions@BackupSite'],
    'api/deleteBackup.me' => ['GET', 'Actions@BackupRemoteDelete'],
    'api/updateSettings.me' => ['POST', 'Actions@UpdateSettings'],

    'api/getLocation.me' => ['GET', 'Actions@GetLocation'],
    'api/getLocation' => ['GET', 'Actions@GetLocation'],

    'send.php' => ['POST', 'Actions@SendForm']
];