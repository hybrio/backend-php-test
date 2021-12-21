<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app['twig'] = $app->share($app->extend('twig', function ($twig, $app) {
    $twig->addGlobal('user', $app['session']->get('user'));

    return $twig;
}));


$app->get('/', function () use ($app) {
    return $app['twig']->render('index.html', [
        'readme' => file_get_contents('README.md'),
    ]);
});


$app->match('/login', function (Request $request) use ($app) {
    $username = $request->get('username');
    $password = $request->get('password');

    if ($username) {
        $user = $app->users->get_user($username, $password);

        if ($user) {
            $app['session']->set('user', $user);
            return $app->redirect('/todo');
        }
    }

    return $app['twig']->render('login.html', array());
});


$app->get('/logout', function () use ($app) {
    $app['session']->set('user', null);
    return $app->redirect('/');
});

$app->get('/todo', function (Request $request, $id) use ($app) {
    if (null === $user = $app['session']->get('user')) {
        return $app->redirect('/login');
    }

    //get page number or redirect to page 1
    $page = $request->query->get('pageno');
    if ($page == null) {
        return $app->redirect('/todo?pageno=1');
    }

    $total_pages = $app->todos->get_total_pages($user);

    $todos = $app->todos->get_page($user, $page);

    return $app['twig']->render('todos.html', [
        'todos' => $todos,
        'pageno' => $page,
        'total_pages' => $total_pages,
    ]);
})
    ->value('id', null);


$app->get('/todo/{id}', function ($id) use ($app) {
    if (null === $app['session']->get('user')) {
        return $app->redirect('/login');
    }
    $todo = $app->todos->get_todo($id);

    return $app['twig']->render('todo.html', [
        'todo' => $todo,
    ]);
})
    ->value('id', null);


$app->get('/todo/{id}/json', function ($id) use ($app) {
    if (null === $app['session']->get('user')) {
        return $app->redirect('/login');
    }

    if ($id) {
        $todo = $app->todos->get_todo($id);
        return json_encode($todo);
    }
})
    ->value('id', null);


$app->post('/todo/add', function (Request $request) use ($app) {
    if (null === $user = $app['session']->get('user')) {
        return $app->redirect('/login');
    }

    $description = $request->get('description');

    $app->todos->add_todo($user, $description);

    $app['session']->getFlashBag()->add('add', 'todo added');

    //get page number or redirect to page 1
    $page = $request->query->get('pageno');
    if ($page == null) {
        return $app->redirect('/todo?pageno=1');
    }
    return $app->redirect("/todo?pageno=$page");
});

$app->match('/todo/completed_toggle/{id}', function (Request $request, $id) use ($app) {
    if (null === $user = $app['session']->get('user')) {
        return $app->redirect('/login');
    }

    $app->todos->toggle_completed($id);

    //get page number or redirect to page 1
    $page = $request->query->get('pageno');
    if ($page == null) {
        return $app->redirect('/todo?pageno=1');
    }
    return $app->redirect("/todo?pageno=$page");
});


$app->match('/todo/delete/{id}', function (Request $request, $id) use ($app) {
    if (null === $user = $app['session']->get('user')) {
        return $app->redirect('/login');
    }

    $app->todos->delete_todo($id);

    $app['session']->getFlashBag()->add('delete', 'todo removed');

    //get page number or redirect to page 1
    $page = $request->query->get('pageno');
    if ($page == null) {
        return $app->redirect('/todo?pageno=1');
    }
    return $app->redirect("/todo?pageno=$page");
});
