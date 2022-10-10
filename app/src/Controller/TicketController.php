<?php
// src/Controller/TicketController.php
namespace App\Controller;

use App\Entity\Ticket;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tickets')]
class TicketController extends AbstractController
{
    #[Route('/', name: "tickets_list")]
    public function list(ManagerRegistry $doctrine): Response
    {
        $tickets = $doctrine->getRepository(Ticket::class)->findAll();
        return $this->render('tickets/list.html.twig', [
            'tickets' => $tickets
        ]);
    }
}

?>