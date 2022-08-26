<?php 

namespace App\Controller;

use App\Entity\Medico;
use App\Helper\MedicoFactory;
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

   /**
    * @var MedicoFactory
    */

    private $medicoFactory;
  
  public function __construct(
    EntityManagerInterface $entityMenager, 
    MedicoFactory $medicoFactory,
    private ManagerRegistry $doctrine
    )

  {
    $this->entityManager = $entityMenager;
    $this->doctrine = $doctrine;
    $this->medicoFactory = $medicoFactory;
  }
  
  /**
   * @Route("/medicos", methods={"POST"})
   */
  public function novo(Request $request): Response
  {
    $corpoRequisicao = $request->getContent();
    $medico = $this->medicoFactory->criarMedico($corpoRequisicao);
    
    $this->entityManager->persist($medico);
    $this->entityManager->flush();              //entitymanager and flush sends what you want to do to the database
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
    $medico = $this->buscaMedico($id);
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
    $medicoEnviado = $this->medicoFactory->criarMedico($corpoRequisicao);

    $medicoExistente = $this->buscaMedico($id);

    if(is_null($medicoExistente)){
      return new Response (Response::HTTP_NOT_FOUND);
    }

    $medicoExistente->crm = $medicoEnviado->crm;
    $medicoExistente->nome = $medicoEnviado->nome;

    $this->entityManager->flush();

    return new JsonResponse($medicoExistente);
  }


  /**
   * @Route("/medicos/{id}", methods={"DELETE"})
   */
  public function remove(int $id): Response
  {
    $medico = $this->buscaMedico($id);
    $this->entityManager->remove($medico);
    $this->entityManager->flush();

    return new Response('', Response::HTTP_NO_CONTENT);
  }


  /**
   * @param int $id
   * @return object|null
   */
  public function buscaMedico(int $id)
  {
    $repositorioDeMedicos = $this->doctrine->getRepository(Medico::class);
    $medico = $repositorioDeMedicos->find($id);

    return $medico;
  }
}
?>