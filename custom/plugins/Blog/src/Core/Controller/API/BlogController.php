<?php declare(strict_types=1);

namespace Blog\Core\Controller\API;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\Routing\Annotation\Route;

#[Route(defaults: ['_routeScope' => ['store-api']])]

class BlogController extends StorefrontController
{
    protected EntityRepository $blogRepository;
    public function __construct(EntityRepository $blogRepository)
    {
        $this->blogRepository = $blogRepository;
    } 

   #[Route(path: '/store-api/blog-list', name: 'store-api.blog.list', methods: ['GET'])]

    public function index(SalesChannelContext $salesChannelContext)
    {
         $criteria = new Criteria();
        $result = $this->blogRepository->search($criteria, $salesChannelContext->getContext());
        return $this->json($result->getEntities());
    }

    #[Route(path: '/store-api/blog-detail/{id}', name: 'store-api.blog.detail', methods: ['GET'])]
    public function detail(string $id, SalesChannelContext $salesChannelContext)
    {
        $criteria = new Criteria([$id]);
        $blog = $this->blogRepository->search($criteria, $salesChannelContext->getContext());
         if (!$blog) {
        return $this->json(['error' => 'Blog not found'], 404);
    }        
        return $this->json($blog);
    }
}