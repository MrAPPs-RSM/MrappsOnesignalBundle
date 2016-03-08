<?php

namespace Mrapps\OnesignalBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('MrappsOnesignalBundle:Default:index.html.twig');
    }
}
