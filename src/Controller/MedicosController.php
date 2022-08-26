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
  
  public function __construct(EntityManagerInterface $entityMenager, private ManagerRegistry $doctrine)
  {
    $this->entityManager = $entityMenager;
    $this->doctrine = $doctrine;
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
   * @Route("/medicos/{id}", methods={"GET"})
   */
  public function buscarUm(int $id): Response
  {
      $repositorioDeMedicos = $this->doctrine->getRepository(Medico::class);
      $medico = $repositorioDeMedicos->find($id);
      $codigoRetorno = is_null($medico) ? Response::HTTP_NO_CONTENT : 200; //you can use ternary way or using "if" just like commented below 
      // $codigoRetorno = 200;
      // if (is_null($medico)) {
      //   $codigoRetorno = Response :: HTTP_NO_CONTENT;
      // }
      return new JsonResponse($medico, $codigoRetorno);
  }

  /**
   * @Route("/medicos/{id}", methods={"PUT"})
   */
  public function atualiza(int $id, Request $request): Response
  { 
      $corpoRequisicao = $request->getContent();
      $dadosEmJson = json_decode($corpoRequisicao);
      
      $medicoEnviado = new Medico();
      $medicoEnviado->crm = $dadosEmJson->crm;
      $medicoEnviado->nome = $dadosEmJson->nome;

      $repositorioDeMedicos = $this->doctrine->getRepository(Medico::class);
      $medicoExistente = $repositorioDeMedicos->find($id);
      if(is_null($medicoExistente)){
        return new Response (Response::HTTP_NOT_FOUND);
      }

      $medicoExistente->crm = $medicoEnviado->crm;
      $medicoExistente->nome = $medicoEnviado->nome;

      $this->entityManager->flush();

      return new JsonResponse($medicoExistente);
  }
}
?>