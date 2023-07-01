<?php
/**
 * UserData controller.
 */

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserData;
use App\Form\Type\UserDataType;
use App\Service\UserDataService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

 /**
  * Class UserDataController.
  */
 #[Route('/userData')]
class UserDataController extends AbstractController
{
    /**
     * UserData service.
     */
    private $userDataService;

    /**
     * Translator.
     */
    private TranslatorInterface $translator;

    /**
     * Constructor.
     *
     * @param UserDataService     $userDataService UserData service
     * @param TranslatorInterface $translator      Translator
     */
    public function __construct(UserDataService $userDataService, TranslatorInterface $translator)
    {
        $this->userDataService = $userDataService;
        $this->translator = $translator;
    }

    /**
     * Edit action.
     *
     * @param Request  $request  HTTP request
     * @param User     $user     User entity
     * @param UserData $userData User Data entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/edit', name: 'userData_edit', methods: ['GET', 'PUT'], requirements: ['id' => '[1-9]\d*'])]
    public function edit(Request $request, User $user, UserData $userData): Response
    {
        $loggedInUser = $this->getUser();

        // Check if the logged-in user is not null and has the necessary permissions
        if (!$this->isGranted('ROLE_ADMIN') && $loggedInUser !== $user) {
            // Handle the case when the user is not authorized to edit this user
            // Redirect or show an error message
            // For example:
            $this->addFlash(
                'warning',
                $this->translator->trans('message_action_impossible')
            );

            return $this->redirectToRoute('photo_index');
        }
        $form = $this->createForm(UserDataType::class, $userData, ['method' => 'PUT']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userDataService->save($userData);
            $this->addFlash('success', 'message_updated_successfully');

            return $this->redirectToRoute('photo_index');
        }

        return $this->render(
            'userData/edit.html.twig',
            [
                'form' => $form->createView(),
                'userData' => $userData,
            ]
        );
    }
}
