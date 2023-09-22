<?php

namespace App\Controller;

use App\Entity\Beer;
use App\Entity\Comment;
use App\Entity\User;
use App\Repository\BeerRepository;
use App\Repository\CommentRepository;
use App\Services\CallApiService;
use App\Services\HelloWorldService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HelloWorldController extends AbstractController
{
    private CallApiService $callApiService;
    private CommentRepository $commentRepository;
    private BeerRepository $beerRepository;
    private HelloWorldService $helloWorldService;

    public function __construct(CallApiService $callApiService,
                                CommentRepository $commentRepository,
                                BeerRepository $beerRepository,
                                HelloWorldService $helloWorldService,
    )
    {
        $this->callApiService = $callApiService;
        $this->commentRepository = $commentRepository;
        $this->beerRepository = $beerRepository;
        $this->helloWorldService = $helloWorldService;
    }

    #[Route('/', name: 'app_hello_world')]
    public function index(Request $request): Response
    {
        //$utc_time = date(\DateTimeInterface::ATOM);
        $utc_time = gmdate("H:i:s");

       // $commentRepository = $this->entityManager->getRepository(CommentRepository::class);
        $all = $this->commentRepository->findAll();

        return $this->render('hello_world/index.html.twig', [
            'controller_name' => 'Hello World !',
            'utc_time' => $utc_time,
            'client_ip' => $request->getClientIp(),
            'all' => $all,
        ]);
    }

    #[Route('/create', name: 'app_comment_create', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $error_message_user = "";
        $error_message_beer= "";
        $req = Request::createFromGlobals();
        $beers = $this->beerRepository->findAll();

        if ($request->isMethod('POST')){

            if($req->request->has('username') && $req->request->has('beername')) {

                $username = $req->request->get('username');
                $beer_name = htmlspecialchars($req->request->get('beername'));
                $beerExist = $this->helloWorldService->searchBeerInfosByName($beers, $beer_name);

                if ($username === '' && $beer_name === ''){
                    $error_message = "This fiels is mandatory !";
                    return $this->render('hello_world/create.html.twig', [
                        'error_message_user' => ($username === '') ? $error_message : "",
                        'error_message_bear' => ($beer_name === '') ? $error_message : "",
                        'beers' => $beers,
                    ]);
                }

                if ($username === '' && !$beerExist) {
                    return $this->render('hello_world/create.html.twig', [
                        'error_message_user' => "This fiels is mandatory !",
                        'error_message_bear' => "Please, choose a existing beer !",
                        'beers' => $beers,
                    ]);
                }

                if ($username !== '' && $beer_name === '') {
                    return $this->render('hello_world/create.html.twig', [
                        'error_message_user' => "",
                        'error_message_bear' => "This fiels is mandatory !",
                        'beers' => $beers,
                    ]);
                }

                if ($username === '' && $beer_name !== '' && $beerExist) {
                    return $this->render('hello_world/create.html.twig', [
                        'error_message_user' => "This fiels is mandatory !",
                        'error_message_bear' => "",
                        'beers' => $beers,
                    ]);
                }

                if (!$beerExist && $beer_name !== '') {

                    return $this->render('hello_world/create.html.twig', [
                        'error_message_user' => "This fiels is mandatory !",
                        'error_message_bear' => ($beerExist) ? "This fiels is mandatory !" : "Please, choose a existing beer !",
                        'beers' => $beers,
                    ]);
                }

                $user = (new User)
                    ->setName(htmlspecialchars($username));
                $entityManager->persist($user);

                $beer = (new Beer)
                    ->setName($beer_name)
                    ->setUrl($beerExist->getUrl())
                    ->setPunkId($beerExist->getPunkId());

                $entityManager->persist($beer);

                $comment = (new Comment())
                    ->setUser($user)
                    ->setBeer($beer)
                    ->setTextComment(htmlspecialchars($req->request->get('comment')));

                $entityManager->persist($comment);
                $entityManager->flush();

                return $this->redirectToRoute('app_hello_world');
            }
        }

        return $this->render('hello_world/create.html.twig', [
            'error_message_user' => $error_message_user,
            'error_message_bear' => $error_message_beer,
            'beers' => $beers,
        ]);
    }

    #[Route('/search-beer', name: 'search_beer', methods: ['POST'])]
    public function searchUser(Request $request): Response
    {
        $search = $request->get('q'); // avec resquet httpfondation

        if ( strlen($search) < 3 ) {
            return new Response(json_encode(null));
        }

        $beersName = $this->beerRepository->searchBearName($search);

        $jsonBeersName = json_encode($beersName);

        $response = new Response($jsonBeersName);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

}
