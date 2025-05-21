<?php declare(strict_types=1);

namespace Blog\Storefront\Controller;

use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route; 



#[Route(defaults: ['_routeScope' => ['storefront']])]

class HelloworldController extends StorefrontController
{ 
 
   #[Route(
        path: '/helloworld',
        name: 'frontend.helloworld.helloworld',
        methods: ['GET']
    )]
    public function index(): Response
    {
        // return $this->renderStorefront('@Blog/storefront/page/helloworld.html.twig',[
        //     'example'=>'Hello world'
        // ]);

        return $this->renderStorefront('@Blog/storefront/page/helloworld.html.twig',[
            'example'=>'Hello world'
        ]);
    }
}