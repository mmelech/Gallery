<?php
/**
 * Photo controller.
 */

namespace App\Controller;

use App\Entity\Gallery;
use App\Entity\Photo;
use App\Form\Type\PhotoEditType;
use App\Form\Type\PhotoType;
use App\Repository\CommentRepository;
use App\Service\PhotoServiceInterface;
use DateTimeImmutable;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class PhotoController.
 */
#[Route('/photo')]
class PhotoController extends AbstractController
{
    /**
     * Photo service.
     */
    private PhotoServiceInterface $photoService;

    /**
     * Translator.
     */
    private TranslatorInterface $translator;

    /**
     * Constructor.
     *
     * @param PhotoServiceInterface $photoService Photo service
     * @param TranslatorInterface   $translator   Translator
     */
    public function __construct(PhotoServiceInterface $photoService, TranslatorInterface $translator)
    {
        $this->photoService = $photoService;
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
        name: 'photo_index',
        methods: 'GET'
    )]
    public function index(Request $request): Response
    {
        $filters = $this->getFilters($request);
        $pagination = $this->photoService->getPaginatedList(
            $request->query->getInt('page', 1),
            $filters
        );

        return $this->render(
            'photo/index.html.twig',
            ['pagination' => $pagination]
        );
    }

    /**
     * Show action.
     *
     * @param Photo             $photo             Photo entity
     * @param CommentRepository $commentRepository CommentRepository entity
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{id}',
        name: 'photo_show',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET',
    )]
    public function show(Photo $photo, CommentRepository $commentRepository): Response
    {
        return $this->render(
            'photo/show.html.twig',
            ['photo' => $photo]
        );
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
        name: 'photo_create',
        methods: 'GET|POST',
    )]
    public function create(Request $request): Response
    {
        $photo = new Photo();
        $form = $this->createForm(PhotoType::class, $photo);
        $form->handleRequest($request);
        $photo->setDate(new DateTimeImmutable());

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $file */
            $file = $form->get('file')->getData();
            $user = $this->getUser();
            $this->photoService->create(
                $file,
                $photo,
                $user
            );

            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('photo_index');
        }

        return $this->render(
            'photo/create.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * Edit action.
     *
     * @param Request $request HTTP request
     * @param Photo   $photo   Photo entity
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{id}/edit',
        name: 'photo_edit',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET|PUT'
    )]
    public function edit(Request $request, Photo $photo): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(PhotoType::class, $photo, ['method' => 'PUT', 'required' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $file */
            $file = $form->get('file')->getData();
            $this->photoService->update(
                $file,
                $photo,
                $user,
            );

            $this->addFlash('success', 'message_updated_successfully');

            return $this->redirectToRoute('photo_index');
        }

        return $this->render(
            'photo/edit.html.twig',
            [
                'form' => $form->createView(),
                'photo' => $photo,
            ]
        );
    }

    /**
     * Delete action.
     *
     * @param Request $request HTTP request
     * @param Photo   $photo   Photo entity
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{id}/delete',
        name: 'photo_delete',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET|DELETE'
    )]
    public function delete(Request $request, Photo $photo): Response
    {
        $form = $this->createForm(FormType::class, $photo, [
            'method' => 'DELETE',
            'action' => $this->generateUrl('photo_delete', ['id' => $photo->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->photoService->delete($photo);

            $this->addFlash(
                'success',
                $this->translator->trans('message.deleted_successfully')
            );

            return $this->redirectToRoute('photo_index');
        }

        return $this->render(
            'photo/delete.html.twig',
            [
                'form' => $form->createView(),
                'photo' => $photo,
            ]
        );
    }

    /**
     * Get filters from request.
     *
     * @param Request $request HTTP request
     *
     * @return array<string, int> Array of filters
     *
     * @psalm-return array{gallery_id: int, tag_id: int, status_id: int}
     */
    private function getFilters(Request $request): array
    {
        $filters = [];
        $filters['gallery_id'] = $request->query->getInt('filters_gallery_id');
        $filters['photos_tags_id'] = $request->query->getInt('filters_tags_id');

        return $filters;
    }
}
