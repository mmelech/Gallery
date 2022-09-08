<?php
/**
 * User controller.
 */

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\ChangePasswordType;
use App\Form\Type\UserType;
use App\Service\UserServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class UserController.
 */
#[Route('/user')]
class UserController extends AbstractController
{
    /**
     * User service.
     */
    private UserServiceInterface $userService;

    /**
     * Translator.
     */
    private TranslatorInterface $translator;

    /**
     * Constructor.
     *
     * @param UserServiceInterface $userService User service
     * @param TranslatorInterface      $translator      Translator
     */
    public function __construct(UserServiceInterface $userService, TranslatorInterface $translator)
    {
        $this->userService = $userService;
        $this->translator = $translator;
    }

    /**
     * Index action.
     *
     * @param Request $request HTTP Request
     *
     * @return Response HTTP response
     */
    #[Route(name: 'user_index', methods: 'GET')]
    public function index(Request $request): Response
    {
        $pagination = $this->userService->getPaginatedList(
            $request->query->getInt('page', 1)
        );

        return $this->render('user/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * Show action.
     *
     * @param User $user User
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{id}',
        name: 'user_show',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET'
    )]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', ['user' => $user]);
    }

    /**
     * Create action.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    #[Route(
        '/create',
        name: 'user_create',
        methods: 'GET|POST',
    )]
    public function create(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userService->save($user);

            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('user_index');
        }

        return $this->render(
            'user/create.html.twig',
            ['form' => $form->createView()]
        );
    }

//    /**
//     * Edit action.
//     *
//     * @param Request  $request  HTTP request
//     * @param User $index.html.twig User entity
//     *
//     * @return Response HTTP response
//     */
//    #[Route('/{id}/edit', name: 'user_edit', requirements: ['id' => '[1-9]\d*'], methods: 'GET|PUT')]
//    public function edit(Request $request, User $index.html.twig): Response
//    {
//        $form = $this->createForm(UserType::class, $index.html.twig, [
//            'method' => 'PUT',
//            'action' => $this->generateUrl('user_edit', ['id' => $index.html.twig->getId()]),
//        ]);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $this->userService->save($index.html.twig);
//
//            $this->addFlash(
//                'success',
//                $this->translator->trans('message.created_successfully')
//            );
//
//            return $this->redirectToRoute('user_index');
//        }
//
//        return $this->render(
//            'index.html.twig/show.html.twig',
//            [
//                'form' => $form->createView(),
//                'index.html.twig' => $index.html.twig,
//            ]
//        );
//    }
//
//    /**
//     * Delete action.
//     *
//     * @param Request  $request  HTTP request
//     * @param User $index.html.twig User entity
//     *
//     * @return Response HTTP response
//     */
//    #[Route('/{id}/delete', name: 'user_delete', requirements: ['id' => '[1-9]\d*'], methods: 'GET|DELETE')]
//    public function delete(Request $request, User $index.html.twig): Response
//    {
//        $form = $this->createForm(FormType::class, $index.html.twig, [
//            'method' => 'DELETE',
//            'action' => $this->generateUrl('user_delete', ['id' => $index.html.twig->getId()]),
//        ]);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $this->userService->delete($index.html.twig);
//
//            $this->addFlash(
//                'success',
//                $this->translator->trans('message.deleted_successfully')
//            );
//
//            return $this->redirectToRoute('user_index');
//        }
//
//        return $this->render(
//            'index.html.twig/delete.html.twig',
//            [
//                'form' => $form->createView(),
//                'index.html.twig' => $index.html.twig,
//            ]
//        );
//    }

    /**
     * Change password action.
     *
     * @param Request $request
     * @param User $user
     * @param UserPasswordHasherInterface $passwordHasher
     *
     * @return Response
     */
    #[Route('/{id}/change_password', name: 'change_password', requirements: ['id' => '[1-9]\d*'], methods: 'GET|PUT')]
    public function changePassword(Request $request, User $user, UserPasswordHasherInterface $passwordHasher): Response
    {
        $form = $this->createForm(ChangePasswordType::class, $user, ['method' => 'PUT',
            'action' => $this->generateUrl('change_password', ['id' => $user->getId()]),
            ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $user->setPassword($passwordHasher->hashPassword($user, $form->get('password')->getData()));

            $this->userService->save($user);

            $this->addFlash(
                'success',
                $this->translator->trans('message.password_edited_successfully')
            );

            return $this->redirectToRoute('photo_index');
        }

        return $this->render(
            'user/change_password.html.twig',
            [
                'form' => $form->createView(),
                'user' => $user
            ]
        );
    }
}
