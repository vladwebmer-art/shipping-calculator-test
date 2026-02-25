<?php

namespace App\Controller\Api;

use App\Carriers\CarrierProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class ShippingController extends AbstractController
{
    /**
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param carrierProvider $carrierProvider
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    #[Route('/api/shipping/calculate', methods: ['POST'])]
    public function calculate(
        Request $request,
        SerializerInterface $serializer,
        CarrierProvider $carrierProvider,
        ValidatorInterface $validator
    ): JsonResponse
    {
        $requestContent = $request->getContent();

        if(empty($requestContent)) {
            // Log error or return an appropriate problem/error response
            return $this->getBadRequestError('The request shouldn\'t be empty.');
        }

        if (!json_validate($request->getContent())) {
            // Log error or return an appropriate problem/error response
            return $this->getBadRequestError('Invalid JSON body format.');
        }

        $content = $serializer->decode($request->getContent(), 'json');

        if(!isset($content['carrier']) || empty($content['carrier'])) {
            // Log error or return an appropriate problem/error response
            return $this->getBadRequestError('Required fields missing.');
        }

        if(!isset($content['weightKg']) || empty($content['weightKg'])) {
            // Log error or return an appropriate problem/error response
            return $this->getBadRequestError('Required fields missing.');
        }

        $violations = $validator->validate($content['weightKg'], [
            new Assert\Type('numeric'),
            new Assert\Positive(),
        ]);

        if (count($violations) > 0) {
            // Handle errors
            $violationsErrors = [];
            foreach ($violations as $violation) {
                $violationsErrors[] = 'weightKg: ' . $violation->getMessage();
            }
            $violationsMessage = implode(', ', $violationsErrors);
            return $this->getBadRequestError($violationsMessage);
        }

        $carrier = $carrierProvider->getByName($content['carrier']);

        if($carrier) {
            return $this->json($carrier->calculate($content['weightKg']));
        }

        return $this->getBadRequestError('Unsupported carrier.');
    }

    /**
     * @param string $message
     * @return JsonResponse
     */
    protected function getBadRequestError(string $message): JsonResponse
    {
        return $this->json([
            'status' => 'error',
            'message' => $message
        ], Response::HTTP_BAD_REQUEST);
    }
}
