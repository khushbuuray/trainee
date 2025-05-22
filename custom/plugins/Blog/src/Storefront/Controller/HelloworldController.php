<?php declare(strict_types=1);

namespace Blog\Storefront\Controller;

use Dom\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route; 



#[Route(defaults: ['_routeScope' => ['storefront']])]

class HelloworldController extends StorefrontController
{ 
    protected EntityRepository $blogRepository;

    public function __construct(EntityRepository $blogRepository)
    {
        $this->blogRepository = $blogRepository;
    }
 
   #[Route(
        path: '/helloworld',
        name: 'frontend.helloworld.helloworld',
        methods: ['GET']
    )]
    public function index(SalesChannelContext $salesChannelContext): Response
    {
        $criteria = new Criteria();
        $criteria->addAssociation('blogCategories');
        $fetchBlog = $this->blogRepository->search($criteria, $salesChannelContext->getContext());
        // dd($fetchBlog->getEntities());
        return $this->renderStorefront('@Blog/storefront/page/helloworld.html.twig',[
            'blogList'=>$fetchBlog->getEntities()
        ]);
    }
}