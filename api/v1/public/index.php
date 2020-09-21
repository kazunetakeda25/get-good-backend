<?php


define("environment", 'dev');

if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}

require __DIR__ . '/../vendor/autoload.php';

session_start();

// Instantiate the app
$settings = require __DIR__ . '/../src/settings.php';
$app = new \Slim\App($settings);

require __DIR__ . '/../src/utils.php';


// Set up dependencies
require __DIR__ . '/../src/dependencies.php';

// Register middleware
require __DIR__ . '/../src/middleware.php';

// Models
require __DIR__ . '/../src/models/user.php';
require __DIR__ . '/../src/models/session.php';
require __DIR__ . '/../src/models/verify.php';
require __DIR__ . '/../src/models/forgot.php';
require __DIR__ . '/../src/models/group.php';
require __DIR__ . '/../src/models/lesson.php';
require __DIR__ . '/../src/models/dialog.php';
require __DIR__ . '/../src/models/trainee_rating.php';
require __DIR__ . '/../src/models/coach_rating.php';
require __DIR__ . '/../src/models/player_rating.php';
require __DIR__ . '/../src/models/admin_post.php';
require __DIR__ . '/../src/models/thread.php';
require __DIR__ . '/../src/models/comment.php';
require __DIR__ . '/../src/models/like.php';
require __DIR__ . '/../src/models/activity.php';
require __DIR__ . '/../src/models/feedback.php';

// Register routes
require __DIR__ . '/../src/routes.php';

// Authentication
require __DIR__ . '/../src/auth.php';
require __DIR__ . '/../src/account.php';
require __DIR__ . '/../src/overwatch.php';
require __DIR__ . '/../src/dialog.php';
require __DIR__ . '/../src/group.php';
require __DIR__ . '/../src/lesson.php';
require __DIR__ . '/../src/thread.php';
require __DIR__ . '/../src/feedback.php';

// CORS
$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization, Token')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});
// Run app
$app->run();
