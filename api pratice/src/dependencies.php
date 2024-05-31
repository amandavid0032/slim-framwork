 <?php

    use Psr\Container\ContainerInterface;

    $container = $app->getContainer();

    $container['db'] = function (ContainerInterface $c) {
        $db = new PDO(
            'mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'] . ';charset=utf8',
            $_ENV['DB_USER'],
            $_ENV['DB_PASS']
        );
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $db;
    };

    $container['jwt_secret'] = function () {
        return $_ENV['JWT_SECRET'];
    };