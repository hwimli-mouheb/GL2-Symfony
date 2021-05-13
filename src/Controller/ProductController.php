<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use Doctrine\ORM\EntityManagerInterface;
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
     * @Route("/list/{page<\d+>?1}/{number<\d+>?6}", name="product.list")
     */
    public function index($page,$number): Response
    {
       $repository = $this->getDoctrine()->getRepository('App:Product');
       $products= $repository->findBy([],['price'=>'asc'],$number,(($page-1)*$number));
        return $this->render('product/index.html.twig', [
            'controller_name' => 'ProductController',
            'products'=> $products
        ]);
    }

    /**
     * @Route ("/add/{product?0}",name="product.add")
     */
    public function addProduct(EntityManagerInterface $manager,Request $request,Product $product=null){
       if(!$product){
           $product =new Product();
       }


       $form= $this->createForm(ProductType::class , $product);
       $form->handleRequest($request);
       if($form->isSubmitted()){
           $manager->persist($product);
           $manager->flush();
           $this->addFlash('success',"le produit ".$product->getName()." a été ajouté avec succes");
           return $this->redirectToRoute('product.list');
       }

        return $this->render('product/add.html.twig',[

            'form'=>$form->createView()
        ]);

    }
    /**
     * @Route ("/update/{product}/{name}/{description}/{price<\d+>}",name="product.update")
     */
    public function updateProduct(Product $product = null,$name,$description,$price,EntityManagerInterface $manager){

  if($product){
      $product->setName($name);
      $product->setDescription($description);
      $product->setPrice($price);
      $manager->persist($product);
      $manager->flush();

  }

        return $this->render('product/detail.html.twig',[
            'product'=> $product
        ]);

    }


    /**
     * @Route ("/delete/{product}",name="product.delete")
     */
    public function deleteProduct(Product $product = null,EntityManagerInterface $manager){

        if($product){
            $productName=$product->getName();
            $manager->remove($product);
            $manager->flush();
            $this->addFlash('success',"le produit $productName a ete supprime");
        }else{
           $this->addFlash('error',"le produit inexistant");
        }



       return $this->redirectToRoute('product.list');
    }
    /**
     * @Route ("/detail/{product}",name="product.detail")
     */
    public function detailProduct(Product $product = null){
        $isDeleted=false;
        return $this->render('product/detail.html.twig',[
            'product'=> $product
        ]);
    }
}
