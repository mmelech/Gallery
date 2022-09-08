<?php
/**
 * UserData controller.
 */

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserData;
use App\Form\UserDataType;
use App\Service\UserDataService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserDataController.
 *
 * @Route("/userData")
 */
class UserDataController extends AbstractController
{
    /**
     * UserData service.
     *
     * @var \App\Service\UserDataService
     */
    private $userDataService;

    /**
     * UserDataController constructor.
     *
     * @param \App\Service\UserDataService $userDataService UserData service
     */
    public function __construct(UserDataService $userDataService)
    {
        $this->userDataService = $userDataService;
    }

    /**
     * Edit action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request  HTTP request
     * @param \App\Entity\UserData                      $userdata UserData entity
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route(
     *     "/{id}/edit",
     *     methods={"GET", "PUT"},
     *     requirements={"id": "[1-9]\d*"},
     *     name="userData_edit",
     * )
     */
    public function edit(Request $request, UserData $userdata): Response
    {
        $form = $this->createForm(UserDataType::class, $userdata, ['method' => 'PUT']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userDataService->save($userdata);
            $this->addFlash('success', 'message_updated_successfully');

            return $this->redirectToRoute('post_index');
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