<?php

require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use DI\Container;
use Valitron\Validator;
use Hexlet\Code\Url;
use Hexlet\Code\UrlRepository;
use Hexlet\Code\Check;
use Hexlet\Code\CheckRepository;

session_start();

$container = new Container();
$container->set(
    'renderer', function () {
        return new \Slim\Views\PhpRenderer(__DIR__ . '/../templates');
    }
);

$container->set(
    'flash', function () {
        return new \Slim\Flash\Messages();
    }
);

$container->set(
    PDO::class, function () {
        $databaseUrl = parse_url(getenv('DATABASE_URL'));
        $user = $databaseUrl['user'];
        $password = $databaseUrl['pass'];
        $host = $databaseUrl['host'];
        $port = $databaseUrl['port'] ?? '5432';
        $dbName = ltrim($databaseUrl['path'], '/');

        $connStr = sprintf(
            "pgsql:host=%s;port=%d;dbname=%s;user=%s;password=%s",
            $host,
            $port,
            $dbName,
            $user,
            $password
        );
        $conn = new PDO($connStr);
        $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $conn;
    }
);

$app = AppFactory::createFromContainer($container);
$app->addErrorMiddleware(true, true, true);

$router = $app->getRouteCollector()->getRouteParser();

$urlRepo = $container->get(UrlRepository::class);
$checkRepo = $container->get(CheckRepository::class);

$app->get('/', function ($request, $response) {
        return $this->get('renderer')->render($response, 'index.phtml');
    }
)->setName('index');

$app->get('/urls', function ($request, $response) use ($urlRepo, $checkRepo) {

        $urls = $urlRepo->getUrlsData();
        $checks = $checkRepo->getLastChecks();
        foreach($urls as $key => $url) {
            foreach($checks as $check) {
                if ($url["id"] == $check["url_id"])
                    $urls[$key]['check'] = $check;
            }
        }
        $params = ['urls' => $urls];
        return $this->get('renderer')->render($response, 'urls.phtml', $params);
    }
)->setName('urls');

$app->post('/urls', function ($request, $response) use ($router, $urlRepo) {

        $urlData = $request->getParsedBodyParam('url');
        $urlName = htmlspecialchars($urlData['name']);

        $validator = new Valitron\Validator(['url' => $urlName]);
        $validator->rule('required', 'url')->message('URL не должен быть пустым ');
        $validator->rule('lengthMax', 'url', 255)->message('Длинна URL не должена превышать 255 символов');
        $validator->rule('url', 'url')->message('Некорректный URL');
        if ($validator->validate()) {

            $parsedUrl = parse_url($urlName);
            $normalizedUrl = $parsedUrl['scheme'] . "://" . $parsedUrl['host'];
            $existUrl = $urlRepo->getUrlByName($normalizedUrl);
            if (!is_null($existUrl)) {

                $this->get('flash')->addMessage('success', 'Страница уже существует');
                return $response->withRedirect($router->urlFor('url', ['id' => $existUrl->getId()]));

            }

            $url = new Url($normalizedUrl);
            $urlRepo->save($url);
            $this->get('flash')->addMessage('success', 'Страница успешно добавлена');
            return $response->withRedirect($router->urlFor('url', ['id' => $url->getId()]));

        } else {
            $errors = $validator->errors('url');
            $params = ['error' => $errors[0]];
            $response = $response->withStatus(422);
            return $this->get('renderer')->render($response, 'index.phtml', $params);
        }
    }
)->setName('post-urls');

$app->get('/urls/{id}', function ($request, $response, array $args) use ($urlRepo, $checkRepo) {
        $id = (int)$args['id'];
        $url = $urlRepo->getUrlById($id);
        if (!is_null($url)) {

            $messages = $this->get('flash')->getMessages();

            $checks = $checkRepo->getChecksDataByUrlId($url->getId());

            $params = [
                'id' => $url->getId(),
                'name' => $url->getName(),
                'createdAt' => $url->getCreatedAt(),
                'message' => $messages['success'][0] ?? '',
                'checks' => $checks
            ];
            return $this->get('renderer')->render($response, 'url.phtml', $params);
        }

        return $this->get('renderer')->render($response->withStatus(404), '404.phtml');
    }
)->setName('url');

$app->post('/urls/{url_id}/checks', function ($request, $response, array $args) use ($checkRepo, $router) {
    $urlId = (int)$args['url_id'];
    $check = new Check($urlId);
    $checkRepo->save($check);
    return $response->withRedirect($router->urlFor('url', ['id' => $urlId]));
    }
)->setName('post-check');

$app->run();

