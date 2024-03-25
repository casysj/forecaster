<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\HighlanderDTO;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/weather')]
class WeatherController extends AbstractController {



    #[Route('/highlander-says/api')]
    public function highlanderSaysApi(#[MapRequestPayload] ?HighlanderDTO $dto = null): Response {

        if(!$dto){
            $dto = new HighlanderDTO();
            $dto->threshold = 50;
            $dto->trials = 1;
        }

        $forecasts = [];
        for($i = 0; $i < $dto->trials; $i++){
            $draw = random_int(0,100);
            $forecasts[] = $draw < $dto->threshold ? "it's goin to rain" : "it's goin to Sunny";
        }

        $json = [
            'forecasts' => $forecasts,
            'threshold' => $dto->threshold,
        ];
        return new JsonResponse($json);
    }
    #[Route('/highlander-says/{threshold<\d+>?50}')]
    public function highlanderSays(
        Request $request,
        RequestStack $requestStack,
        ?int $threshold = null
    ): Response {

        $session = $requestStack->getSession();
        if($threshold){
            $session->set('threshold', $threshold);
        } else {
            $threshold = $session->get('threshold',50);
        }

        $trials = $request->get('trial', 1);
        $forecasts = [];
        for($i = 0; $i < $trials; $i++){
            $draw = random_int(0,100);
            $forecasts[] = $draw < $threshold ? "it's goin to rain" : "Sunny";
        }

        return $this->render('weather/highlander_says.html.twig', [
            'forecasts' => $forecasts,
            'threshold' => $threshold
        ]);
    }


    #[Route('/highlander-says/{guess}')]
    public function highlanderSaysGuess(string $guess): Response {

        $forecast = "it's goin to $guess";
        $availableGuesses = ['snow', 'rain','sunny'];

        if(!in_array($guess, $availableGuesses)){
            throw $this->createNotFoundException('no wayyy');
        }

        return $this->render('weather/highlander_says.html.twig', [
            'forecasts' => [$forecast]
        ]);
    }



    #[Route('/lorem')]
    public function loremIpsum() : Response{
        $lorem = "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.";


        return $this->render('loremIpsum.html.twig',[
            'text' => $lorem
        ]);
    }
}