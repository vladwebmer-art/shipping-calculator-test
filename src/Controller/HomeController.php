<?php

namespace App\Controller;

use App\Form\CalculateShippingRateType;
use App\Carriers\CarrierProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class HomeController extends AbstractController
{
    /**
     * @return Response
     */
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        $form = $this->createForm(CalculateShippingRateType::class);
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'form' => $form,
        ]);
    }

    /**
     * @param Request $request
     * @param carrierProvider $carrierProvider
     * @param ValidatorInterface $validator
     * @return Response
     */
    #[Route('/calculate', name: 'calculate')]
    public function calculate(
        Request $request,
        CarrierProvider $carrierProvider,
        ValidatorInterface $validator): Response
    {
        $request = $request->request->all();
        if(empty($request['calculate_shipping_rate'])) {
            $this->addFlash('error', 'Please fill the form.');
            return $this->redirectToRoute('home');
        }

        $formData = $request['calculate_shipping_rate'];

        if(!isset($formData['carrier']) || empty($formData['carrier'])) {
            // Log error or return an appropriate problem/error response
            $this->addFlash('error', 'Required fields missing.');
            return $this->redirectToRoute('home');
        }

        if(!isset($formData['weightKg']) || empty($formData['weightKg'])) {
            // Log error or return an appropriate problem/error response
            $this->addFlash('error', 'Required fields missing.');
            return $this->redirectToRoute('home');
        }

        $violations = $validator->validate($formData['weightKg'], [
            new Assert\Type('numeric'),
            new Assert\Positive(),
        ]);

        if (count($violations) > 0) {
            // Handle errors
            foreach ($violations as $violation) {
                $this->addFlash('error', 'weightKg: ' . $violation->getMessage());
            }

            return $this->redirectToRoute('home');
        }

        $carrier = $carrierProvider->getByName($formData['carrier']);

        if($carrier) {
            $rate = $carrier->calculate($formData['weightKg']);
            $this->addFlash('success', 'Your shipping rate is ' . $rate['price'] . ' ' . $rate['currency']);
            return $this->redirectToRoute('home');
        }

        $this->addFlash('error', 'Unsupported carrier.');
        return $this->redirectToRoute('home');
    }
}
