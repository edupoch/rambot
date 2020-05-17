<?php
namespace Deployer;

require 'recipe/composer.php';

// Configuration

set('repository', 'git@github.com:edupoch/rambot.git');
set('git_tty', true);
set('shared_files', ['config.php']);
set('shared_dirs', ['imgs']);
set('writable_dirs', ['imgs']);

host('production')
    ->hostname('188.166.156.92')
    ->stage('production')
    ->user('ocre')
    ->forwardAgent(true)    
    ->set('branch', 'master')
    ->set('deploy_path', '/var/www/rambot.ocre.soy/www');

?>