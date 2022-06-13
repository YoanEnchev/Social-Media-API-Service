<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends AbstractController
{
 
    /**
     * @Route("/", methods={"GET"})
     *
     * It's only purpose is to check if server is up through the browser.
     */
    public function welcome(): Response
    {
        return $this->render('welcome.html.twig');
    }
}
