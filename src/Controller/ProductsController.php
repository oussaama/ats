<?php

namespace App\Controller;

use App\Entity\Products;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Routing\Annotation\Route;

class ProductsController extends Controller
{
    /**
     * @Rest\View()
     * @Rest\Get("/api/products/{limit}/{offset}")
     */
    public function getApiProductsAction(Request $request,$limit,$offset)
    {
        $em = $this->getDoctrine()->getManager();
        $order = $request->query->get('orderBy') != null ?$request->query->get('orderBy'):'id';
        $orderType = 'DESC';
        if($request->query->get('type') == 'true' || $request->query->get('type') == null){
            $orderType = 'ASC';
        }
        if($request->query->get('category')!= null){
            $products = $em->getRepository(Products::class)->findBy(['category' => $request->query->get('category')],[$order=>$orderType],$limit,$limit*($offset-1));
        }else{
            $products = $em->getRepository(Products::class)->findBy([],[$order=>$orderType],$limit,$limit*($offset-1));
        }
        if (empty($products)) {
        return new JsonResponse(['message' => 'Products Not Found'], Response::HTTP_NOT_FOUND);
    }

        $normalizer = new ObjectNormalizer();
        $normalizer->setIgnoredAttributes(array("reviews"));
        $encoder = new JsonEncoder();
        $serializer = new Serializer(array($normalizer), array($encoder));
        $object = $serializer->serialize($products, 'json');

        $view = View::create($object);
        $view->setFormat('json');

        return $view;
    }

    /**
     * @Rest\View()
     * @Rest\Get("/api/product/{id}")
     */
    public function getApiProductDetailAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository(Products::class)->findOneById($id);

        if (empty($product)) {
            return new JsonResponse(['message' => 'Product Not Found'], Response::HTTP_NOT_FOUND);
        }

        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceHandler(function ($object) {
            return $object->getId();
        });
        $encoder = new JsonEncoder();
        $serializer = new Serializer(array($normalizer), array($encoder));
        $object = $serializer->serialize($product, 'json');

        $view = View::create($object);
        $view->setFormat('json');

        return $view;
    }

    /**
     * @Rest\View()
     * @Rest\Get("/api/product/edit/{id}")
     */
    public function getApiProductEditAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository(Products::class)->findOneById($id);

        if (empty($product)) {
            return new JsonResponse(['message' => 'Product Not Found'], Response::HTTP_NOT_FOUND);
        }

        $normalizer = new ObjectNormalizer();
        $normalizer->setIgnoredAttributes(array("reviews"));
        $encoder = new JsonEncoder();
        $serializer = new Serializer(array($normalizer), array($encoder));
        $object = $serializer->serialize($product, 'json');

        $view = View::create($object);
        $view->setFormat('json');

        return $view;
    }

    /**
     * @Rest\View()
     * @Rest\Post("/api/product/add")
     */
    public function postApiProductAddAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $json = $request->get('product');
        $productObj = json_decode($json,true);

        $product = new Products();
        if (empty($productObj)) {
            return new JsonResponse(['message' => 'Product Not inserted'], Response::HTTP_NOT_FOUND);
        }
        $product->addProduct(
            $productObj['productName'],
            $productObj['basePrise'],
            $productObj['category'],
            $productObj['brand'],
            $productObj['productMaterial'],
            $productObj['imageUrl'],
            $productObj['Delivery'],
            $productObj['Details']
        );
        $em->persist($product);
        $em->flush();
        $view = View::create(['message'=>'success']);
        $view->setFormat('json');

        return $view;
    }

    /**
     * @Rest\View()
     * @Rest\Post("/api/product/update")
     */
    public function postApiProductEditAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $json = $request->get('product');
        $productObj = json_decode($json,true);

        $product = $em->getRepository(Products::class)->findOneById($productObj['id']);
        if (empty($product)) {
            return new JsonResponse(['message' => 'Product Not Found'], Response::HTTP_NOT_FOUND);
        }
        $product->addProduct(
            $productObj['productName'],
            $productObj['basePrise'],
            $productObj['category'],
            $productObj['brand'],
            $productObj['productMaterial'],
            $productObj['imageUrl'],
            $productObj['Delivery'],
            $productObj['Details']
        );
        $em->flush();

        $normalizer = new ObjectNormalizer();
        $normalizer->setIgnoredAttributes(array("reviews"));
        $encoder = new JsonEncoder();
        $serializer = new Serializer(array($normalizer), array($encoder));
        $object = $serializer->serialize($product, 'json');
        $view = View::create($object);
        $view->setFormat('json');

        return $view;
    }

    /**
     * @Rest\View()
     * @Rest\Get("/api/products/categories")
     */
    public function getApiCategoriesAction()
    {

        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository(Products::class)->getCategories();
        if (empty($categories)) {
            return new JsonResponse(['message' => 'Categories Not Found'], Response::HTTP_NOT_FOUND);
        }

        $normalizer = new ObjectNormalizer();
        $encoder = new JsonEncoder();
        $serializer = new Serializer(array($normalizer), array($encoder));
        $object = $serializer->serialize($categories, 'json');

        $view = View::create($object);
        $view->setFormat('json');

        return $view;
    }

    /**
     * @Route("/api/products/total",name="total-product")
     */
    public function getApiProductsTotalAction(Request $request)
    {
        $params =[
            'category'=>$request->query->get('category')
        ];

        $em = $this->getDoctrine()->getManager();
        $productsNumber = $em->getRepository(Products::class)->countProducts($params);

        return new JsonResponse(['total'=>$productsNumber[1]]);
    }


    /**
     * @Route("/products", name="products")
     */
    public function getProductsAction(Request $request)
    {
        return $this->render('products/index.html.twig');
    }
}
