<?php

namespace App\Controller;

use App\Entity\Screenshot;
use App\Form\ScreenshotType;
use App\Repository\ScreenshotRepository;
use App\Service\TakerManager;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ScreenshotController extends AbstractController
{
    #[Route('/', name: 'screenshot')]
    public function index(ScreenshotRepository $repository): Response
    {
        $screenshots = [];
        foreach ($repository->findAll() as $screenshot) {
            $screenshots[] = [
                'id' => $screenshot->getId(),
                'url' => $screenshot->getUrl(),
                'success' => (int)$screenshot->getSuccess(),
                'datetime' => $screenshot->getCreatedOn()->format('Y-i-d H:m:s'),
                'params' => $screenshot->fetchParams()
            ];
        }

        return $this->render('screenshot/index.html.twig', [
            'screenshots' => $screenshots,
        ]);
    }

    #[Route('/screenshot/new', name: 'create_screenshot')]
    public function createScreenshot(ManagerRegistry $doctrine, Request $request, TakerManager $takerManager): Response
    {
        $entityManager = $doctrine->getManager();

        $screenshot = new Screenshot();
        $form = $this->createForm(ScreenshotType::class, $screenshot);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $screenshot = $form->getData();

            $takeScreenshot = $takerManager->takeScreenshot($screenshot);
            if ($takeScreenshot->success) {
                $entityManager->persist($screenshot);

                $screenshot->setFilename($takeScreenshot->filename);
                $screenshot->setSuccess(true);
                $screenshot->setCreatedOn($takeScreenshot->time);

                $entityManager->flush();

                return $this->redirectToRoute('screenshot');
            } else {
                return $this->redirectToRoute('screenshot_fail');
            }
        }

        return $this->renderForm('screenshot/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/screenshot/{id}', name: 'show_screenshot')]
    public function show(Screenshot $post): Response
    {
        $content = (object)['url' => ''];
        $status = 200;

        if ($post->getSuccess()) {
            if (empty($post->getFilename())) {
                $content->errorMessage = 'This screenshot is not available to present';
            } else {
                $file = $post->getFilename();
                if (str_starts_with($file, 'https')) {
                    $content->url = $file;
                } else {
                    // Clear trash from filename, maybe in future fix this on saving the image
                    $content->url = explode('public/', $file)[1];
                }
            }
        } else {
            $status = 400;
            $content->errorMessage = 'This screenshot as failed to be taken';
        }

        return new Response(json_encode($content), $status, ['content-type' => 'application/json']);
    }

    #[Route('screenshot/fail', name: 'screenshot_fail')]
    public function fail(string $message)
    {
        return $this->render('screenshot/fail.html.twig', ['message' => $message]);
    }
}
