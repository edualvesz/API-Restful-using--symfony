<?php 

namespace App\Controller;

use App\Entity\Medico;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
//use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class MedicosController extends AbstractController
{
  
  /**
   * @var EntityManagerInterface
   */
  
  public function __construct(EntityManagerInterface $entityMenager)
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
    $this->entityManager->flush();
    // does a lot of database operations
    
    return new JsonResponse($medico);
  }
  
  
  /**
   * @Route("/medicos", methods={"GET"})
   */
  public function buscarTodos(ManagerRegistry $doctrine): Response
  {
    $repositorioDeMedicos = $doctrine->getRepository(Medico::class); //getDoctrine() is deprecated, use ManagerRegistry instead
    $medicoList = $repositorioDeMedicos->findAll();

        return new JsonResponse($medicoList);
  }

  /**
   * @Route("/medicos/"(id)", methods={"GET"})
   */

  public function buscarUm(ManagerRegistry $doctrine, Request $request): Response
  {
    $id = $request->get('id');
    $repositorioDeMedicos = $doctrine->getRepository(Medico::class);
    $medico = $repositorioDeMedicos->find($id);

    return new JsonResponse($medico);

  }
}
?>