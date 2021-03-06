<?php
/**
 * Created by PhpStorm.
 * User: wpanderson
 * Date: 9/19/18
 * Time: 1:29 PM
 */
// src/Controller/ArticlesController.php

namespace App\Controller;

use App\Controller\AppController;

class ArticlesController extends AppController
{

    public function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub

        $this->loadComponent('Paginator');
        // Flash allows for notifications.
        $this->loadComponent('Flash');
    }

    public function index()
    {
        $this->loadComponent('Paginator');
        $articles = $this->Paginator->paginate($this->Articles->find());
        $this->set(compact('articles'));
    }

    // When user clicks a slug allow them to view the article.
    public function view($slug = null)
    {
        $article = $this->Articles->findBySlug($slug)->firstOrFail();
        $this->set(compact('article'));
    }

    // Add action for allowing users to add their own articles
    public function add()
    {
        // PHP if statement
        $article = $this->Articles->newEntity();
        if ($this->request->is('post')) {
            // Debug to print stuff
//            debug($this->request->getData());
            $article = $this->Articles->patchEntity($article, $this->request->getData());

            // Hardcoding user_id is temporary and will be changed with authenticator
            $article->user_id = 1;

            // Attempts to save an article if success notifies user and redirects
            if ($this->Articles->save($article)) {
                $this->Flash->success(__('Your article has been saved.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Unable to add your article. :( \n Article
             data: {0}', $article));
            // For some reason if statement above was failing. So I took it out of it.
//            $this->Articles->save($article);
//            return $this->redirect(['action' => 'index']);

        }
        // Get a list of available tags.
        $tags = $this->Articles->Tags->find('list');

        // Set tags to the view context.
        $this->set('tags', $tags);


        // Set article for use inside the template.
        $this->set('article', $article);
    }

    // Add the ability to Edit Articles.
    public function edit($slug)
    {
        /**
         * This action first ensures that the user has tried to access an existing record. If they haven’t passed in
         * an $slug parameter, or the article does not exist, a NotFoundException will be thrown, and the CakePHP
         * ErrorHandler will render the appropriate error page.

            Next the action checks whether the request is either a POST or a PUT request. If it is, then we use the
         * POST/PUT data to update our article entity by using the patchEntity() method. Finally, we call save() set
         * the appropriate flash message and either redirect or display validation errors.
         */

        $article = $this->Articles->findBySlug($slug)
            ->contain('Tags') // Loads associated Tags
            ->firstOrFail();
        if ($this->request->is(['post', 'put'])) {
            $this->Articles->patchEntity($article, $this->request->getData());
            if ($this->Articles->save($article)) {
                $this->Flash->success(__('Your article has been updated.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Unable to update your article.'));
        }

        // Get a list of tags.
        $tags = $this->Articles->Tags->find('list');

        // Set tags to the view context so they are visible to the user.
        $this->set('tags', $tags);

        // Set article
        $this->set('article', $article);
    }

    // Add ability to delete Articles.
    public function delete($slug)
    {
        // Validates that only post or delete methods can use this function.
        $this->request->allowMethod(['post', 'delete']);

        $article = $this->Articles->findBySlug($slug)->firstOrFail();
        if ($this->Articles->delete($article)) {
            $this->Flash->success(__('The {0} article has been deleted.', $article->title));
            return $this->redirect(['action' => 'index']);
        }
    }

    // Controller for making tags available to the view.
    public function tags()
    {
        // The 'pass' key is provided by CakePHP and contains all
        // the passed URL path segments in the request.
        $tags = $this->request->getParam('pass');

        // Use the ArticlesTable to find tagged articles
        $articles = $this->Articles->find('tagged',
            ['tags' => $tags]);

        // Pass variables into the view template context.
        $this->set([
                'articles' => $articles,
                'tags' => $tags
            ]);
    }
}
