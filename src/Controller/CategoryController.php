<?php declare(strict_types=1);

namespace Safebeat\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Safebeat\Annotation\RequestBodyValidator;
use Safebeat\Validator\{EmptyValidator, NullStringValidator};
use Safebeat\Entity\Category;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/category", name="category_")
 */
class CategoryController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route(name="list", methods={"GET"})
     */
    public function list(): JsonResponse
    {
        return JsonResponse::create([
            'categories' => $this->entityManager->getRepository(Category::class)->findAll()
        ]);
    }

    /**
     * @Route(path="/{category}", name="get", methods={"GET"})
     * @IsGranted("CATEGORY_VIEW", subject="category")
     */
    public function getCategory(Category $category): JsonResponse
    {
        return JsonResponse::create(['category' => $category]);
    }

    /**
     * @Route(name="create", methods={"POST"})
     * @RequestBodyValidator(validators={"name": {EmptyValidator::class, NullStringValidator::class}})
     */
    public function create(Request $request): JsonResponse
    {
        $name = $request->request->get('name');

        $category = new Category();

        $category->setName($name);
        $category->setOwner($this->getUser());

        $this->entityManager->persist($category);
        $this->entityManager->flush();

        return JsonResponse::create(['category' => $category], 201);
    }

    /**
     * @Route(path="/{category}", name="delete", methods={"DELETE"})
     * @IsGranted("CATEGORY_DELETE", subject="category")
     */
    public function delete(Category $category): JsonResponse
    {
        $this->entityManager->remove($category);
        $this->entityManager->flush();

        return JsonResponse::create(['deleted' => true]);
    }
}
