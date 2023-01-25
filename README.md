
# Веб-приложение для регистрации захоронений

Быстрый старт
-------------

Для настройки PhpStorm + Docker + Xdebug, пожалуйста, ознакомьтесь со статьей: <https://blog.denisbondar.com/post/phpstorm_docker_xdebug/>

Для работы с проектом требуется установить Docker Compose plugin: <https://docs.docker.com/compose/install/linux/>

Перед началом локальной работы с проектом выполните следующие консольные команды:
```bash
export HOST_USER_UID=$(id -u) && export HOST_USER_GID=$(id -g)
make init

# дождитесь завершения инициализации сервера MySQL (может занять несколько минут), после чего выполните команды:
make -- sf console doctrine:migrations:migrate

# база данных для тестового окружения создается следующими командами:
make -- sf console doctrine:schema:create --env=test            # только после первоначальной инициализации
# или
make -- sf console doctrine:schema:update --env=test --force    # после добавления миграций
```
**Tip**: Чтобы каждый раз вручную не создавать переменные HOST_USER_UID и HOST_USER_GID, просто добавьте их создание в файл ~/.bashrc.

Инструменты командной строки
----------------------------

Для удобства работы с проектом через Docker используйте Makefile. В нем есть примеры того, как можно использовать все доступные вам инструменты командной строки.
