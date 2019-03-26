<?php

namespace App\Controller;

use App\Entity\Process;
use App\Form\ProcessType;
use App\Repository\ProcessRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/process")
 */
class ProcessController extends AbstractController
{
    private function getNewCaseNumber()
    {
        $year = date('y');
        $casesInYear = $this->getDoctrine()->getRepository(Process::class)->findBy([]);

        $caseNumber = str_pad(count($casesInYear), 4, "0", STR_PAD_LEFT);

        return printf("%s-%s", $year, $caseNumber);
    }

    /**
     * @Route("/", name="process_index", methods={"GET"})
     */
    public function index(ProcessRepository $processRepository): Response
    {
        return $this->render('process/index.html.twig', [
            'processes' => $processRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="process_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $process = new Process();
        $form = $this->createForm(ProcessType::class, $process);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $process->setCaseNumber($this->getNewCaseNumber());
            $process->setCreatedAt(new \DateTime());
            $process->setUpdatedAt(new \DateTime());
            $process->setCreatedBy($this->getUser());
            $process->setUpdatedBy($this->getUser());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($process);
            $entityManager->flush();

            return $this->redirectToRoute('process_index');
        }

        return $this->render('process/new.html.twig', [
            'process' => $process,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="process_show", methods={"GET"})
     */
    public function show(Process $process): Response
    {
        return $this->render('process/show.html.twig', [
            'process' => $process,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="process_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Process $process): Response
    {
        $form = $this->createForm(ProcessType::class, $process);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('process_index', [
                'id' => $process->getId(),
            ]);
        }

        return $this->render('process/edit.html.twig', [
            'process' => $process,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="process_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Process $process): Response
    {
        if ($this->isCsrfTokenValid('delete'.$process->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($process);
            $entityManager->flush();
        }

        return $this->redirectToRoute('process_index');
    }
}
