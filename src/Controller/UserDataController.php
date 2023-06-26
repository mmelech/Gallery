<?php
/**
 * UserData controller.
 */

namespace App\Controller;

use App\Entity\UserData;
use App\Form\Type\UserDataType;
use App\Service\UserDataService;
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
     * @param Request  $request  HTTP request
     * @param UserData $userdata UserData entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/edit', name: 'userData_edit', methods: 'GET|PUT', requirements: ['id' => '[1-9]\d*'])]
    public function edit(Request $request, UserData $userdata): Response
    {
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
