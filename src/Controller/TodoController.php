<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TodoController
 * @package App\Controller
 * @Route("todo")
 */
class TodoController extends AbstractController
{
    /**
     * @Route("", name="todo")
     */
    public function index(SessionInterface $session): Response
    {
        if(! $session->has('todos')) {
            $todos = [
                'lundi' => 'HTML',
                'mardi' => 'CSS',
                'mercredi' => 'Js',
            ];
            $session->set('todos', $todos);
            if (!$session->get('reset')) {
                $this->addFlash('info', "Bienvenu dans votre plateforme de gestion des todos");
            }
            else{
                $session->set('reset',false);
            }

        }
        return $this->render('todo/index.html.twig');
    }

    /**
     * @Route("/add/{name}/{content}", name="addTodo")
     */
    public function addTodo($name, $content, SessionInterface $session) {

        // Vérifier que ma session contient le tableau de todo
        if (!$session->has('todos')) {
            //ko => messsage erreur + redirection
            $this->addFlash('error', "La liste des todos n'est pas encore initialisée");
        } else {
            //ok
            // Je vérifie si le todo existe
            $todos = $session->get('todos');
            if (isset($todos[$name])) {
                //ko => messsage erreur + redirection
                $this->addFlash('error', "Le todo $name existe déjà");
            } else {
                //ok => j ajoute et je redirige avec message succès
                $todos[$name] = $content;
                $session->set('todos', $todos);
                $this->addFlash('success', "Le todo $name a été ajouté avec succès");
            }
        }
        return $this->redirectToRoute('todo');
    }
    /**
     * @Route("/delete/{name}/", name="deleteTodo")
     */
    public function deleteTodo($name, SessionInterface $session) {

        // Vérifier que ma session contient le tableau de todo
        if (!$session->has('todos')) {
            //ko => messsage erreur + redirection

            $this->addFlash('error', "La liste des todos n'est pas encore initialisée");
        } else {
            //ok
            // Je vérifie si le todo existe
            $todos = $session->get('todos');
            if (!isset($todos[$name])) {
                //ok => messsage erreur + redirection
                $this->addFlash('error', "Le todo $name n'existe pas");
            } else {
                //ko => j supprime et je redirige avec message succès

                unset($todos[$name]);
                $session->set('todos', $todos);
                $this->addFlash('success', "Le todo $name a été supprimé avec succès");
            }
        }
        return $this->redirectToRoute('todo');
    }

    /**
     * @Route("/reset", name="resetTodo")
     */
    public function resetTodo(SessionInterface $session) {
        $session->remove('todos');
        $this->addFlash('success', "La liste des todos a été bien réinitialiseé");
        $session->set('reset', true);
        return $this->redirectToRoute('todo');
    }
}