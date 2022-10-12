<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use http\Message\Body;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/product")
 */
class ProductController extends AbstractController
{
    /**
     * @Route("/{pageId}", name="app_product_index", methods={"GET"})
     */
    public function index(Request $request, ProductRepository $productRepository, int $pageId = 1, CategoryRepository $categoryRepository): Response
    {
        $min = $request->get('minPrice');
        $max = $request->get('maxPrice');
        $cat = $request->get('category');
        $word = $request->get('word');
        $this->filterRequestQuery($min, $max, $cat, $word);

//        if ($min == NULL && $max == NULL && $cat == NULL)
//            $products = $productRepository ->findAll();
//        else
            $products = $productRepository ->findAllGreaterThanPrice($min, $max, $cat, $word);

        $numOfItems = count($products);   // total number of items satisfied above query
        $itemsPerPage = 8; // number of items shown each page
        $products = array_slice($products, $itemsPerPage * ($pageId - 1), $itemsPerPage);
        return $this->render('product/index.html.twig', [
            'products' => $products,
            'numOfPages' => ceil($numOfItems/$itemsPerPage),
            'categories' => $categoryRepository ->findAll(),
            'cat' => $cat,
            'totalProduct' => $numOfItems
        ]);
    }

    /**
     * @Route("/new", name="app_product_new", methods={"GET", "POST"})
     */
    public function new(Request $request, ProductRepository $productRepository): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productRepository->add($product, true);

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/phepcong", name="app_product_plus", methods={"GET", "POST"})
     */
    public function plus(Request $request): Response
    {
        $firstNum = $request->query->get('a');
        $secondNum = $request->query->get('b');

        return new Response(
            '<html lang="html"><body>' .($firstNum + $secondNum).'</body></html>'
        );
    }

    /**
     * @Route("/{id}", name="app_product_show", methods={"GET"})
     */
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_product_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productRepository->add($product, true);

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_product_delete", methods={"POST"})
     */
    public function delete(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $productRepository->remove($product, true);
        }

        return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
    }

    private function filterRequestQuery($min, $max, $cat, $word): array
    {

        return [
            is_numeric($min) ? (float)$min : null,
            is_numeric($max) ? (float)$max : null,
            is_numeric($cat) ? (float)$cat : null,
            is_string($word) ? (string)$word : null
        ];
    }
}


