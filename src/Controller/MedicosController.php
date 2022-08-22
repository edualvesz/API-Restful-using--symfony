<?php 

namespace App\Controller;

use App\Entity\Medico;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class MedicosController 
{
    /**
     * @var EntityMenagerInterface
     */

    public function __construct(EntityMenagerInterface $entityMenager)
    {
      $this->entityManager = $entityMenager;
    }


    /**
     * @Route("/medicos", methods={"POST"})
     */
    public function novo(Request $request): Response
    {
      $corpoRequisicao = $request->getContent();
      $dadosEmJson = json_decode($corpoRequisicao);

      $medico = new Medico();
      $medico->crm = $dadosEmJson->crm;
      $medico->nome = $dadosEmJson->nome;

      $this->entityManager->persist($medico);
      $this->entityMenager->flush();
      // does a lot of database operations

      return new JsonResponse($medico);
    }
}
?>