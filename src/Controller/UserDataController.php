<?php
/**
 * UserData controller.
 */

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserData;
use App\Form\Type\UserDataType;
use App\Service\UserDataService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
     * UserDataController constructor.
     *
     * @param UserDataService $userDataService UserData service
     */
    public function __construct(UserDataService $userDataService)
    {
        $this->userDataService = $userDataService;
    }

    /**
     * Edit action.
     *
     * @param Request $request HTTP request
     * @param Request $request HTTP reques
     *
     * @return Response HTTP response
     */
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}/edit', name: 'userData_edit', methods: 'GET|PUT', requirements: ['id' => '[1-9]\d*'])]
    public function edit(Request $request, User $user, UserData $userdata): Response
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
        $form = $this->createForm(UserDataType::class, $userdata, ['method' => 'PUT']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userDataService->save($userdata);
            $this->addFlash('success', 'message_updated_successfully');

            return $this->redirectToRoute('photo_index');
        }

        return $this->render(
            'userData/edit.html.twig',
            [
                'form' => $form->createView(),
                'userData' => $userdata,
            ]
        );
    }
}
