<?php
/**
 * Gallery controller.
 */

namespace App\Controller;

use App\Entity\Gallery;
use App\Form\Type\GalleryType;
use App\Service\GalleryServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class GalleryController.
 */
#[Route('/gallery')]
class GalleryController extends AbstractController
{
    /**
     * Gallery service.
     */
    private GalleryServiceInterface $galleryService;

    /**
     * Translator.
     */
    private TranslatorInterface $translator;

    /**
     * Constructor.
     *
     * @param GalleryServiceInterface $galleryService Gallery service
     * @param TranslatorInterface     $translator     Translator
     */
    public function __construct(GalleryServiceInterface $galleryService, TranslatorInterface $translator)
    {
        $this->galleryService = $galleryService;
        $this->translator = $translator;
    }

    /**
     * Index action.
     *
     * @param Request $request HTTP Request
     *
     * @return Response HTTP response
     */
    #[Route(
        name: 'gallery_index',
        methods: 'GET'
    )]
    public function index(Request $request): Response
    {
        $pagination = $this->galleryService->getPaginatedList(
            $request->query->getInt('page', 1)
        );

        return $this->render('gallery/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * Show action.
     *
     * @param Gallery $gallery Gallery
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{id}',
        name: 'gallery_show',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET'
    )]
    public function show(Gallery $gallery): Response
    {
        return $this->render('gallery/show.html.twig', ['gallery' => $gallery]);
    }

    /**
     * Create action.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    #[IsGranted('ROLE_USER')]
    #[Route(
        '/create',
        name: 'gallery_create',
        methods: 'GET|POST',
    )]
    public function create(Request $request): Response
    {
        $gallery = new Gallery();
        $form = $this->createForm(GalleryType::class, $gallery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->galleryService->save($gallery);

            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('gallery_index');
        }

        return $this->render(
            'gallery/create.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * Edit action.
     *
     * @param Request $request HTTP request
     * @param Gallery $gallery Gallery entity
     *
     * @return Response HTTP response
     */
    #[IsGranted('ROLE_USER')]
    #[Route('/{id}/edit', name: 'gallery_edit', requirements: ['id' => '[1-9]\d*'], methods: 'GET|PUT')]
    public function edit(Request $request, Gallery $gallery): Response
    {
        $form = $this->createForm(GalleryType::class, $gallery, [
            'method' => 'PUT',
            'action' => $this->generateUrl('gallery_edit', ['id' => $gallery->getId()]),
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->galleryService->save($gallery);

            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('gallery_index');
        }

        return $this->render(
            'gallery/edit.html.twig',
            [
                'form' => $form->createView(),
                'gallery' => $gallery,
            ]
        );
    }

    /**
     * Delete action.
     *
     * @param Request $request HTTP request
     * @param Gallery $gallery Gallery entity
     *
     * @return Response HTTP response
     */
    #[IsGranted('ROLE_USER')]
    #[Route('/{id}/delete', name: 'gallery_delete', requirements: ['id' => '[1-9]\d*'], methods: 'GET|DELETE')]
    public function delete(Request $request, Gallery $gallery): Response
    {
        $form = $this->createForm(FormType::class, $gallery, [
            'method' => 'DELETE',
            'action' => $this->generateUrl('gallery_delete', ['id' => $gallery->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->galleryService->delete($gallery);

            $this->addFlash(
                'success',
                $this->translator->trans('message.deleted_successfully')
            );

            return $this->redirectToRoute('gallery_index');
        }

        return $this->render(
            'gallery/delete.html.twig',
            [
                'form' => $form->createView(),
                'gallery' => $gallery,
            ]
        );
    }
}
