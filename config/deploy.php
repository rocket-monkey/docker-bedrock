<?php

date_default_timezone_set('TIMEZONE');

require_once 'recipe/common.php';
require_once 'vendor/ekandreas/docker-bedrock/recipe.php';

env('container', 'container_name_prefix');

server('development', 'the_project.dev', 2222)
    ->env('deploy_path', '/var/www/html')
    ->env('branch', 'master')
    ->stage('development')
    ->user('docker', 'tcuser');

server('production', 'ip-to-prod', 22)
    ->env('deploy_path', '/path/to/docroot/in/prod')
    ->user('root')
    ->env('branch', 'master')
    ->stage('production')
    ->identityFile();

set('repository', 'https://github.com/your-name/your-project');

set('env', 'prod');
set('keep_releases', 10);
set('shared_dirs', ['web/app/uploads']);
set('shared_files', ['.env', 'web/.htaccess', 'web/robots.txt']);
set('env_vars', '/usr/bin/env');

task('deploy', [
    'deploy:prepare',
    'deploy:release',
    'deploy:update_code',
    'deploy:vendors',
    'deploy:shared',
    'deploy:symlink',
    'cleanup',
    'success',
])->desc('Deploy your Bedrock project, eg dep deploy production');
