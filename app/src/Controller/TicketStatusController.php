<?php

namespace App\Controller;

use App\Entity\TicketStatus;
use App\Form\TicketStatusType;
use App\Repository\TicketStatusRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[isGranted('ROLE_ADMIN')]
#[Route('/ticketStatus')]
class TicketStatusController extends AbstractController
{


    #[Route('/', name: 'app_ticket_status_index', methods: ['GET'])]
    public function index(TicketStatusRepository $ticketStatusRepository): Response
    {
        return $this->render('ticket_status/index.html.twig', [
            'ticket_statuses' => $ticketStatusRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_ticket_status_new', methods: ['GET', 'POST'])]
    public function new(Request $request, TicketStatusRepository $ticketStatusRepository): Response
    {
        $ticketStatus = new TicketStatus();
        $form = $this->createForm(TicketStatusType::class, $ticketStatus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ticketStatusRepository->save($ticketStatus, true);

            $session = $request->getSession();
            $session->getFlashBag()->add('success', "ticketStatus ajouté avec succes");

            return $this->redirectToRoute('app_ticket_status_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('ticket_status/new.html.twig', [
            'ticket_status' => $ticketStatus,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_ticket_status_show', methods: ['GET'])]
    public function show(TicketStatus $ticketStatus): Response
    {
        return $this->render('ticket_status/show.html.twig', [
            'ticket_status' => $ticketStatus,
        ]);
    }


    /**
     * @ParamConverter("ticket", options={"id" = "id"})
     */
    #[Route('/{id}/edit', name: 'app_ticket_status_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TicketStatus $ticketStatus, TicketStatusRepository $ticketStatusRepository): Response
    {
        $form = $this->createForm(TicketStatusType::class, $ticketStatus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ticketStatusRepository->save($ticketStatus, true);

            return $this->redirectToRoute('app_ticket_status_index', [], Response::HTTP_SEE_OTHER);
            $session = $request->getSession();
            $session->getFlashBag()->add('success', "ticketStatus modifié avec succes");
        }

        return $this->renderForm('ticket_status/edit.html.twig', [
            'ticket_status' => $ticketStatus,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_ticket_status_delete', methods: ['POST'])]
    public function delete(Request $request, TicketStatus $ticketStatus, TicketStatusRepository $ticketStatusRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ticketStatus->getId(), $request->request->get('_token'))) {
            $ticketStatusRepository->remove($ticketStatus, true);

            $session = $request->getSession();
            $session->getFlashBag()->add('danger', "ticketStatus supprimé avec succes");
        }

        return $this->redirectToRoute('app_ticket_status_index', [], Response::HTTP_SEE_OTHER);
    }
}
