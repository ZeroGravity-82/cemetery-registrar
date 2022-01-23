PhpStorm + Docker + Xdebug: https://blog.denisbondar.com/post/phpstorm_docker_xdebug

dev:
    make init
    make -- sf doctrine:migrations:migrate

test:
    make init
    make -- sf doctrine:database:create --env=test
    make -- sf doctrine:schema:create --env=test
