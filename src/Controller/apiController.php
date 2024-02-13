<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class apiController extends AbstractController
{
    #[Route('/api', name: 'main')]
    public function test(EntityManagerInterface $entityManager, Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $response['data'] = null;
        $reqMethod = $request->getMethod();

        $reqData = null;
        
        if ($reqMethod != 'GET') {
            $reqData = json_decode($request->getContent(), true);
        }
        $response['data'] = $reqData;
        switch ($reqMethod) {
            // Register
            case 'PATCH':
                $user = $entityManager->getRepository(User::class);
                
                $userCheck = $user->findOneBy(['email' => $reqData['email']]);
                
                if ($userCheck) {
                    $response['data'] = 'User ' . $userCheck . ' already exist';

                } else {
                    $canRegister = true;

                    if (strlen($reqData['pass']) < 7) {
                        $canRegister = false;
                        $response['errorPass'] = 'password is too short';
                    }

                    if ($reqData['pass'] != $reqData['passRep']) {
                        $canRegister = false;
                        $response['errorPassRep'] = 'passwords are different';
                    } 
                    
                    if ($reqData['inputAcc'] == false) {
                        $canRegister = false;
                        $response['errorAcc'] = 'You must accept the rules';
                    } 
                    
                    if ($canRegister == true) {
                        $newUser = new User;
                        $newUser -> setEmail($reqData['email']);

                        $hashedPassword = $passwordHasher->hashPassword(
                            $newUser,
                            $reqData['pass']
                        );

                        $newUser -> setPassword($hashedPassword);
                        $entityManager->persist($newUser);
                        $entityManager->flush();
    
                        $response['data'] = 'User ' . $reqData['email'] . ' has been registered';
                    }
                }
                /* $response['test-enconding-special-chars'] = ' asdasd asd<>?>?>?>'; */
                $response['test-request-method'] = $reqMethod;
                // $response['test-data'] = json_decode($reqData);
                break;
            
            // Login
            case 'POST':
                $user = $entityManager->getRepository(User::class);
                $userCheck = $user->findOneBy(['email' => $reqData['email']]);

                if ($userCheck) {

                    $plaintextPassword = $reqData['pass'];

                    if ($plaintextPassword == '') {
                        $response['passinfo'] = 'password cant be empty';
                    } elseif (!$passwordHasher->isPasswordValid($userCheck, $plaintextPassword)) {
                        $response['passinfo'] = 'wrong-password';
                    } else {
                        $response['passinfo'] = 'logged';
                    }

                } else {
                    $response['userInfo'] = "User " . $reqData['email'] . " doesn't exist";
                }
                break;

            case 'GET':

                break;

            case 'DELETE':
                
                break;

        }
        
        $data = json_encode($response);

        return new Response($data);
    }
}