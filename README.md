Docker + php-fpm + PhpStorm + Xdebug: https://habr.com/ru/post/473184/

dev:
    make init
    make -- sf console doctrine:migrations:migrate

test:
    make init
    make -- sf console doctrine:schema:create --env=test            (for the first time after init)
    make -- sf console doctrine:schema:update --env=test --force    (after adding migrations)
