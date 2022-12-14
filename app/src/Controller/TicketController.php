<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Form\TicketType;
use App\Repository\TicketRepository;
use App\Repository\TicketStatusRepository;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[isGranted('ROLE_USER')]
#[Route('/ticket')]
class TicketController extends AbstractController
{

    #[Route('/', name: 'app_ticket_index', methods: ['GET'])]
    public function index(TicketRepository $ticketRepository): Response
    {
        $request = Request::createFromGlobals();
        $query = $request->query->get('label');


        //vérification que le nom est pas null
        if($query != '' && $query != Null) {
            $tickets = $ticketRepository->findTicketByLabel($query);
        } else {
            $tickets = $ticketRepository->findAll();
        }
        return $this->render('ticket/list.html.twig', [
            'tickets' => $tickets,
            'user' => $this->getUser()
        ]);
    }

    #[Route('/new', name: 'app_ticket_new', methods: ['GET', 'POST'])]
    public function new(Request $request, TicketRepository $ticketRepository,TicketStatusRepository $ticketStatusRepository): Response
    {
        $ticket = new Ticket();
        $form = $this->createForm(TicketType::class, $ticket);
        $form->handleRequest($request);
        $ticket->setUser($this->getUser());

        if ($form->isSubmitted() && $form->isValid()) {

            $ticketStatus = $ticketStatusRepository->findAll();

            $ticketRepository->save($ticket, true);

            $session = $request->getSession();
            $session->getFlashBag()->add('success', "ticket ajouté avec succes");

            return $this->redirectToRoute('app_ticket_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('ticket/new.html.twig', [
            'ticket' => $ticket,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_ticket_show', methods: ['GET'])]
    public function show(Ticket $ticket): Response
    {
        return $this->render('ticket/show.html.twig', [
            'ticket' => $ticket,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_ticket_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Ticket $ticket, TicketRepository $ticketRepository): Response
    {

        if($ticket->getUser() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            $session = $request->getSession();
            $session->getFlashBag()->add('danger', 'Ce ticket ne vous appartient pas !');
            return $this->redirectToRoute('app_ticket_index', [], Response::HTTP_SEE_OTHER);
        }

        $form = $this->createForm(TicketType::class, $ticket);
        $form->handleRequest($request);



            if ($form->isSubmitted() && $form->isValid()) {
                $ticketRepository->save($ticket, true);

                $session = $request->getSession();
                $session->getFlashBag()->add('success', "ticket modifié avec succes");

                return $this->redirectToRoute('app_ticket_index', [], Response::HTTP_SEE_OTHER);
            }


        return $this->renderForm('ticket/edit.html.twig', [
            'ticket' => $ticket,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_ticket_delete', methods: ['POST'])]
    public function delete(Request $request, Ticket $ticket, TicketRepository $ticketRepository): Response
    {

        if($ticket->getUser() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            $session = $request->getSession();
            $session->getFlashBag()->add('danger', 'Ce ticket ne vous appartient pas !');
            return $this->redirectToRoute('app_ticket_index', [], Response::HTTP_SEE_OTHER);
        }

        if ($this->isCsrfTokenValid('delete'.$ticket->getId(), $request->request->get('_token'))) {
            $ticketRepository->remove($ticket, true);
        }

        $session = $request->getSession();
        $session->getFlashBag()->add('danger', "ticket supprimé avec succes");
        return $this->redirectToRoute('app_ticket_index', [], Response::HTTP_SEE_OTHER);

    }

   /* #[Route('/{ticketId}', name: "tickets_show")]
    public function showById(?int $ticketId, ManagerRegistry $doctrine): Response
    {
        $ticket = $doctrine->getRepository(Ticket::class)->find($ticketId);

        if (!$ticket) {
            throw $this->createNotFoundException(
                'No ticket found for id '.$ticketId
            );
        }
        return $this->render('tickets/show.html.twig', [
            'ticket' => $ticket
        ]);
    }*/
}
