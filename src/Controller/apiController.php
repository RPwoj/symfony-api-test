<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;

class apiController extends AbstractController
{
    #[Route('/api', name: 'main')]
    public function test(EntityManagerInterface $entityManager, Request $request): Response
    {

        $reqMethod = $request->getMethod();
        $reqData = $request->getContent();

        $user = $entityManager->getRepository(User::class)->find(1);
        $userMail = $user->getEmail();
        
        $response['data'] = $userMail;
        $response['test-enconding-special-chars'] = ' asdasd asd<>?>?>?>';
        $response['test-request-method'] = $reqMethod;
        $response['test-data'] = json_decode($reqData);
        
        $data = json_encode($response);

        return new Response($data);
    }
}