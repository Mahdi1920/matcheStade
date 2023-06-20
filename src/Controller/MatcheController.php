<?php

namespace App\Controller;

use App\Entity\Matche;
use App\Entity\Stade;
use App\Form\MatcheType;
use App\Form\SearchType;
use App\Repository\MatcheRepository;
use App\Repository\StadeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MatcheController extends AbstractController
{
    #[Route('/matche', name: 'app_matche')]
    public function index(): Response
    {
        return $this->render('matche/index.html.twig', [
            'controller_name' => 'MatcheController',
        ]);
    }

    #[Route('/list', name: 'app_list')]
    public function list(StadeRepository $staderepo): Response
    {
        $stades = $staderepo->findAll();
        return $this->render('matche/stades.html.twig', [
            'show' => $stades,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_delete')]
    public function delete(StadeRepository $satderepo, $id): Response
    {
        $stadedelete = $satderepo->find($id);
        $satderepo->remove($stadedelete, true);
        return $this->redirectToRoute('app_list');
    }

    #[Route('/add', name: 'app_showroom')]
    public function add(Request $req, MatcheRepository $matchrepo): Response
    {
        $match = new Matche();
        $form = $this->createForm(MatcheType::class, $match);
        $form->handleRequest($req);
        if ($form->isSubmitted()) {
            $matchrepo->save($match, true);
            return $this->redirectToRoute('app_list_matches');
        }
        return $this->renderForm('matche/add.html.twig', [
            'form' => $form
        ]);
    }
    #[Route('/listmatches', name: 'app_list_matches')]
    public function listMatches(MatcheRepository $matcherepo): Response
    {
        $matches = $matcherepo->findAll();
        return $this->render('matche/matches.html.twig', [
            'matches' => $matches,
        ]);
    }

    #[Route('/order', name: 'app_order')]
    public function listeOrder(MatcheRepository $matcherepo, Request $req): Response
    {
        $matches = $matcherepo->findAll();
        $order = $matcherepo->getOrderbynbSpectateurs();
        //$orderl = $matcherepo->getMatchesByStade();
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($req);
        if ($form->isSubmitted()) {
            $dataform = $form->getData();

            $result = $matcherepo->searchMatch($dataform);
            

            return $this->render('matche/order.html.twig', array('matches' => $result, 'order' => $order, 'search' => $form->createView()));
        }
        return $this->render('matche/order.html.twig', array('matches' => $matches, 'order' => $order, 'search' => $form->createView()));
    }
}