<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\BudgetForm;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Custom\Calculations;

class BudgetController extends AbstractController
{
    
    
    
    #[Route('/budget', name: 'budget', methods: ['GET','POST'])]
    public function index(Request $request): Response
    {

        $form = $this->createForm(BudgetForm::class);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $budget =  $form->get('budget')->getData();
            if( !$budget ) {
                $request->getSession()->getFlashBag()->add('error', 'You need to provide the budget.' );
            }

            $calculate = new Calculations();
            $result = $calculate->calculateMaxValueVehicle( $budget );
            if( ! $result ) {
                $result = [];
                $request->getSession()->getFlashBag()->add('error', 'Something unexpected happened and the calculation could not be performed.' );
            }

            return $this->render('budget/result.html.twig', $result );
        }

        return $this->render('budget/index.html.twig', [
            'form' => $form->createView()
        ]);

    }

    



}