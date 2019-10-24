<?php


namespace AppBundle\Controller\Admin;


use AppBundle\Document\CrawlerProduct;
use AppBundle\Document\Product as DocumentProduct;
use AppBundle\DocumentRepository\CrawlerProductRepository;
use AppBundle\Entity\Product;
use AppBundle\Form\ProductForm;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ActionController
 * @package AppBundle\Controller\Admin
 * @Route("/admin")
 */
class AdminController extends Controller
{
    private $em;
    private $dm;

    /**
     * ActionController constructor.
     * @param EntityManagerInterface $entityManager
     * @param DocumentManager $documentManager
     */
    public function __construct(EntityManagerInterface $entityManager, DocumentManager $documentManager)
    {
        $this->em = $entityManager;
        $this->dm = $documentManager;
    }

    /**
     * @Route("/", name="admin_homepage_action")
     * @return Response
     */
    public function adminHomepageAction()
    {
        $products = $this->em->getRepository(Product::class)->findBy([], ['name' => 'ASC'], 50);

        return $this->render(
            "admin/homepage.html.twig", [
                "products" => $products
            ]
        );
    }

    /**
     * @Route("/create", name="admin_create_action")
     * @param Request $request
     * @return Response
     */
    public function createAction(Request $request)
    {
        $form = $this->createForm(ProductForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $product = $form->getData();
            $this->em->persist($product);
            $this->em->flush();
            return new Response("Product was successfully created!");
        }

        return $this->render("admin/create.html.twig", ["form" => $form->createView()]);
    }

    /**
     * @Route("/{id}/edit", name="admin_edit_action")
     * @param Request $request
     * @param Product $product
     * @return Response
     */
    public function editAction(Request $request, Product $product)
    {
        $form = $this->createForm(ProductForm::class, $product);
        $form->handleRequest($request);
        $success = "";
        if ($form->isSubmitted() && $form->isValid()) {
            $product = $form->getData();
            $this->em->persist($product);
            $this->em->flush();
            $success = "Success!";
        }

        return $this->render(
            "admin/edit.html.twig", [
                "form" => $form->createView(),
                "product" => $product,
                "success" => $success
            ]
        );
    }

    /**
     * @Route("/document/{id}/", name="admin_edit_action")
     * @param $id
     * @return void
     */
    public function seeProductsPrice($id)
    {
        /** @var DocumentProduct $documentProduct */
        $documentProduct = $this->get('doctrine_mongodb')
            ->getRepository(CrawlerProduct::class)
            ->findAllOrderedByName();

        var_dump($documentProduct);
    }
}