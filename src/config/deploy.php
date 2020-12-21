<?php
return [
    'git' => [
        'branch' => env('DEPLOY_GIT_BRANCH', 'master'),
        'bin' => [
            'local' => env('DEPLOY_GIT_BIN_LOCAL', 'C:\Program Files\Git\cmd\git.exe'),
            'remote' => env('DEPLOY_GIT_BIN_REMOTE', '/usr/bin/git'),
        ],
        'commit' => env('DEPLOY_GIT_COMMIT', 'deploy updated'),
    ],
    'ssh' => [
        'path' => env('DEPLOY_SSH_PATH', '/www/wwwroot/'),
        'user' => env('DEPLOY_SSH_USER', 'root'),
        'pass' => env('DEPLOY_SSH_PASS', ''),
        'host' => env('DEPLOY_SSH_HOST', '127.0.0.1'),
        'port' => env('DEPLOY_SSH_PORT', 22),
    ],
];