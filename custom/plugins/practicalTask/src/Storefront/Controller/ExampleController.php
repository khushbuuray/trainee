<?php declare(strict_types=1);

namespace PracticalTask\Storefront\Controller;

use OpenSearchDSL\Aggregation\Bucketing\TermsAggregation;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;

#[Route(defaults: ['_routeScope' => ['storefront']])]
class ExampleController extends StorefrontController
{
    private EntityRepository $EntityRepository;

       public function __construct(EntityRepository $EntityRepository)
    {
        $this->EntityRepository = $EntityRepository;
    }
    #[Route(
        path: '/example',
        name: 'frontend.example.example',
        methods: ['GET']
    )]
    public function showExample(Request $request, SalesChannelContext $salesChannelContext): Response
    {
        $criteria =  new Criteria();
        $criteria->addAssociation('manufacturer');
        $criteria->addAssociation('media');
        $criteria->addAssociation('products');
        $fetch = $this->EntityRepository->search($criteria, $salesChannelContext->getContext()); 
        $count = 0;
        $productCounts = [];

    foreach ($fetch->getEntities() as $manufacturer) {
    $manufacturerId = $manufacturer->getId();
    $products = $manufacturer->getProducts();
    $count = count($products); 
    $productCounts[$manufacturerId] = $count;
    }

        return $this->renderStorefront('@practicalTask/storefront/page/example.html.twig', [
            'data' => $fetch->getEntities(),
            'productCounts'=>$productCounts
        ]);
    }

    #[Route(
        path: '/detail',
        name: 'frontend.example.detail',
        methods: ['GET']
    )]   
    public function showDetail(Request $request, SalesChannelContext $salesChannelContext): Response
    {   
        $id = $request->get('id');
        $criteria =  new Criteria([$id]);
        $criteria->addAssociation('media');
        $fetch = $this->EntityRepository->search($criteria, $salesChannelContext->getContext());  
        return $this->renderStorefront('@practicalTask/storefront/page/detail.html.twig', [
            'DetailByID' => $fetch->getEntities()
        ]);
    }
    
    
}




