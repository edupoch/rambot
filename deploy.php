<?php
namespace Deployer;

require 'recipe/composer.php';

// Configuration

set('repository', 'git@github.com:edupoch/rambot.git');
set('git_tty', true);
set('shared_files', ['config.php']);
set('shared_dirs', ['imgs']);
set('writable_dirs', ['imgs']);

server('prod', '188.166.156.92', 22)
    ->user('ocre')
    ->forwardAgent()
    ->stage('production')
    ->env('branch', 'master')
    ->env('deploy_path', '/var/www/rambot.ocre.soy/www');

?>