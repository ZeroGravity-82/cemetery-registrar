PhpStorm + Docker + Xdebug: https://blog.denisbondar.com/post/phpstorm_docker_xdebug

dev:
    make init
    make -- sf doctrine:migrations:migrate

test:
    make init
    make -- sf doctrine:schema:create --env=test            (for the first time after init)
    make -- sf doctrine:schema:update --env=test --force    (after adding migrations)
