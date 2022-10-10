<?php
// src/Controller/LuckyController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/lucky')]
class LuckyController extends AbstractController
{
    #[Route('/number/{max}', name: "lucky_number_max")]
    public function number(int $max): Response
    {
        //dd($max);
        $number = random_int(0, $max);

        return $this->render('lucky/number.html.twig', [
            'number' => $number,
        ]);
    }
}