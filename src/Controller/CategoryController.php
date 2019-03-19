<?php declare(strict_types=1);

namespace Safebeat\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Safebeat\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/category", name="category_")
 */
class CategoryController extends AbstractController
{
    private $entityManager;

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
     */
    public function getCategory(Category $category): JsonResponse
    {
        return JsonResponse::create(['category' => $category]);
    }

    /**
     * @Route(name="create", methods={"POST"})
     */
    public function create(Request $request): JsonResponse
    {
        $name = $request->request->get('name');

        if (empty($name)) {
            throw new BadRequestHttpException('Missing required name in body');
        }

        $category = new Category();

        $category->setName($name);
        $category->setOwner($this->getUser());

        $this->entityManager->persist($category);
        $this->entityManager->flush();

        return JsonResponse::create(['category' => $category], 201);
    }

    /**
     * @Route(path="/{category}", name="delete", methods={"DELETE"})
     */
    public function delete(Category $category, TranslatorInterface $translator): JsonResponse
    {
        if ($category->getOwner() !== $this->getUser()) {
            throw new AccessDeniedHttpException(
                $translator->trans(
                    "The category %category% doesn't belong to you!",
                    ['category' => $category->getName()],
                    null,
                    $this->getUser()->getLanguage()
                )
            );
        }

        $this->entityManager->remove($category);
        $this->entityManager->flush();

        return JsonResponse::create(['deleted' => true]);
    }
}
