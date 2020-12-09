<?php

return [
    'metrika/stats' => ['GET', 'Actions@LinkToMetrikaStats'],
    'api/selfUpdate.me' => ['GET', 'Actions@ConnectorUpdate'],
    'api/copySite.me' => ['GET', 'Actions@MakePublicCopy'],
    'api/backupSite.me' => ['GET', 'Actions@BackupSite'],
    'api/deleteBackup.me' => ['GET', 'Actions@BackupRemoteDelete'],
    'api/updateSettings.me' => ['POST', 'Actions@UpdateSettings'],
    'send.php' => ['POST', 'Actions@SendForm']
];