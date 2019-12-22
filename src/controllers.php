<?php

use Model\Entities\Todo;
use Model\Entities\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Utils\Helper;

$app['twig'] = $app->share($app->extend('twig', function($twig, $app) {
    $twig->addGlobal('user', $app['session']->get('user'));

    return $twig;
}));


$app->get('/', function () use ($app) {
    return $app['twig']->render('index.html', [
        'readme' => file_get_contents('README.md'),
    ]);
});


$app->match('/login', function (Request $request) use ($app) {
    $conditions = [
        'username' => $request->get('username'),
        'password' => $request->get('password')
    ];

    if ($conditions['username']) {
        $user = User::first($conditions);

        if ($user){
            $app['session']->set('user', $user->toArray());
            return $app->redirect('/todo');
        }
    }

    return $app['twig']->render('login.html', array());
});


$app->get('/logout', function () use ($app) {
    $app['session']->set('user', null);
    return $app->redirect('/');
});


$app->get('/todo/{id}', function ($id) use ($app) {
    $user = Helper::getSessionUser($app);
    if (null === $user) {
        return $app->redirect('/login');
    }

    if ($id){
        $todo = Todo::find($id);

        return $app['twig']->render('todo.html', [
            'todo' => $todo,
        ]);
    } else {
        $todos = Todo::all(['user_id' => $user->id]);

        return $app['twig']->render('todos.html', [
            'todos' => $todos,
        ]);
    }
})
->value('id', null);


$app->get('/todo/{id}/json', function ($id) use ($app) {
    $user = Helper::getSessionUser($app);
    if (null === $user) {
        return $app->redirect('/login');
    }

    if ($id){
        $todo = Todo::find($id);

        return $app['twig']->render('todo.html', [
            'todo' => json_encode($todo),
            'json' => true,
        ]);
    } else {
        $todos = Todo::all(['user_id' => $user->id]);

        return $app['twig']->render('todos.html', [
            'todos' => $todos,
        ]);
    }
})
->value('id', null);


$app->post('/todo/add', function (Request $request) use ($app) {
    $user = Helper::getSessionUser($app);
    if (null === $user) {
        return $app->redirect('/login');
    }

    $todo = new Todo();
    $todo->setDescription($request->get('description'));
    $todo->setUserId($user->id);
    $todo->save();

    $app['session']->getFlashBag()->add('confirmMsg', 'Added a task.');

    return $app->redirect('/todo');
});


$app->match('/todo/delete/{id}', function ($id) use ($app) {

    $todo = Todo::find($id);
    $todo->delete();

    $app['session']->getFlashBag()->add('confirmMsg', 'Deleted a task.');

    return $app->redirect('/todo');
});


$app->match('/todo/completed/{id}', function ($id, Request $request) use ($app) {
    $user = Helper::getSessionUser($app);
    if (null === $user) {
        return $app->redirect('/login');
    }

    $completed = $request->get('completed') ?? 0;
    $todo = Todo::find($id);
    $todo->setCompleted($completed);
    $todo->save();

    $app['session']->getFlashBag()->add('confirmMsg', $completed ? 'Completed a task.' : 'Reset a completed task.');

    return $app->redirect('/todo');
});
