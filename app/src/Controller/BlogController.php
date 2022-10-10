<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Flex\Response;

class BlogController extends AbstractController
{
    #[Route('/blog/{page<\d+>}', name: 'blog_list')]
    public function list(): Response
    {
        // ...
    }
}

?>