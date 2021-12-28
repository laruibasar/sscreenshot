<?php

namespace App\Service;

use App\Entity\Screenshot;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validation;

class TakerManager
{
    private $entityManager;

    private $http;

    const ERR_VALIDATION = 1;
    const ERR_CONNECTION = 2;

    public function __construct(ManagerRegistry $doctrine, HttperService $http)
    {
        $this->entityManager = $doctrine->getManager();
        $this->http = $http;
    }

    public function takeScreenshot(Screenshot $screenshot): object
    {
        // Prepare the request to take and save the screenshot
        $timestamp = new DateTime('now');
        $request = $this->http->fetchScreenshot($screenshot, $timestamp);

        if (!$request->success) {
            return (object)[
                'success' => false,
                'errorType' => self::ERR_CONNECTION,
                'errorMessage' => $request->errorMessage
            ];
        }

        return (object)[
            'success' => true,
            'filename' => $request->filename,
            'size' => $request->size,
            'time' => $timestamp
        ];
    }

    /**
     * Receive values in an array, to use from cli command
     * @param string $url
     * @param array $options
     * @return object
     */
    public function take(string $url, array $options): object
    {
        $isValid = $this->validator($url, $options);
        if (count($isValid) > 0) {
            return (object)[
                'success' => false,
                'errorType' => self::ERR_VALIDATION,
                'errorMessage' => (string) $isValid
            ];
        }

        $screenshot = new Screenshot();
        $screenshot->setUrl($url);
        $screenshot->setWidth((int)$options['width']);
        $screenshot->setHeight((int)$options['height']);
        $screenshot->setOutput($options['output']);
        $screenshot->setFileType($options['file_type']);
        $screenshot->setLazyLoad($options['lazy_load']);
        $screenshot->setDarkMode($options['dark_mode']);
        $screenshot->setGrayscale((int)$options['grayscale']);
        $screenshot->setDelay((int)$options['delay']);
        $screenshot->setUserAgent($options['user_agent'] ?: '');
        $screenshot->setFullPage($options['full_page']);
        $screenshot->setFailOnError($options['fail_on_error']);

        $clipx = is_null($options['clip_x']) ? null : (int)$options['clip_x'];
        $screenshot->setClipX($clipx);
        $clipy = is_null($options['clip_y']) ? null : (int)$options['clip_y'];
        $screenshot->setClipY($clipy);
        $clipw = is_null($options['clip_w']) ? null : (int)$options['clip_w'];
        $screenshot->setClipW($clipw);
        $cliph = is_null($options['clip_h']) ? null : (int)$options['clip_h'];
        $screenshot->setClipH($cliph);

       // Persist the entity
        $this->entityManager->persist($screenshot);

        // Prepare the request to take and save the screenshot
        $timestamp = new DateTime('now');
        $request = $this->http->fetchScreenshot($screenshot, $timestamp);
        $screenshot->setCreatedOn($request->timestamp);
        $screenshot->setSuccess($request->success);

        if (!$request->success) {
            $this->entityManager->flush();
            return (object)[
                'success' => false,
                'errorType' => self::ERR_CONNECTION,
                'errorMessage' => $request->errorMessage
            ];
        }
        $screenshot->setFilename($request->filename);
        $size = $request->size;

        $this->entityManager->flush();
        return (object)[
            'success' => true,
            'filename' => $request->filename,
            'size' => $size,
            'time' => $screenshot->getCreatedOn()
        ];
    }

    /**
     * Prepare custom validation on input from user.
     *
     * @param string $url
     * @param array $input
     * @return ConstraintViolationList
     */
    private function validator(string $url, array $input): ConstraintViolationList
    {
        $validator = Validation::createValidator();
        $data = [
            'width' => (int)$input['width'],
            'height' => (int)$input['height'],
            'output' => $input['output'],
            'file_type' => $input['file_type'],
            'grayscale' => (int)$input['grayscale'],
            'delay' => (int)$input['delay'],
            'user_agent' => $input['user_agent'] ?: ''
        ];

        $violations = $validator->validate($url, new Assert\Optional([
            new Assert\Type(['type' => 'string']),
            new Assert\Length(['max' => 255]),
            new Assert\NotBlank()
        ]));
        if (count($violations) > 0) {
            return $violations;
        }

        $constraints = new Assert\Collection([
            'width' => new Assert\Optional([
                new Assert\Type(['type' => 'integer']),
                new Assert\PositiveOrZero()
            ]),
            'height' => new Assert\Optional([
                new Assert\Type(['type' => 'integer']),
                new Assert\PositiveOrZero()
            ]),
            'output' => new Assert\Choice(['image', 'json']),
            'file_type' => new Assert\Choice(['png', 'jpeg', 'webp', 'pdf']),
            'grayscale' => new Assert\Optional([
                new Assert\Type(['type' => 'integer']),
                new Assert\Range(['min' => 0, 'max' => '100'])
            ]),
            'delay' => new Assert\Optional([
                new Assert\Type(['type' => 'integer']),
                new Assert\PositiveOrZero()
            ]),
            'user_agent' => new Assert\Length(['max' => 255])
        ]);

        return $validator->validate($data, $constraints);
    }
}
