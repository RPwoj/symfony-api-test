<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;

class apiController extends AbstractController
{
    #[Route('/api', name: 'main')]
    public function test(EntityManagerInterface $entityManager): Response
    {

        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: *");
        header("Access-Control-Allow-Methods: *");
        header("Allow: *");

        $user = $entityManager->getRepository(User::class)->find(1);
        $userMail = $user->getEmail();

        $response['data'] = $userMail;
        $response['test-data'] = ' asdasd asd<>?>?>?>';
        
        $data = json_encode($response);

        return new Response($data);
    }
}