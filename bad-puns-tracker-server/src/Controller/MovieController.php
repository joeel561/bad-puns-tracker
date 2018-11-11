<?php
namespace App\Controller;

use App\Entity\Movie;
use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class MovieController extends ApiController
{
    /**
    * @Route("/movies")
    * @Method("GET")
    */
    public function index(MovieRepository $movieRepository)
    {

        $movies = $movieRepository->transformAll();

        return $this->respond($movies);
    }

    /** 
     * @Route("/movies/{id}")
     * @Method("GET")
     */

     public function show($id, MovieRepository $movieRepository)
     {
         $movie = $movieRepository->find($id);

         if (! $movie) {
             return $this->respondNotFound();
         }

         $movie = $movieRepository->transform($movie);

         return $this->respond($movie);
     }


     /** 
      * @Route("/movies")
      * @Method("POST")
      */

      public function create(Request $request, MovieRepository $movieRepository, EntityManagerInterface $em)
      {

          $request = $this->transformJsonBody($request);

          if (! $request) {
              return $this->respondValidationError('Please provide a valid request');
          }

          // title validate 

          if (! $request->get('title')) {
              return $this->respondValidationError('Please provide a title');
          }

          $movie = new Movie;
          $movie->setTitle($request->get('title'));
          $movie->setCount(0);
          $em->persist($movie);
          $em->flush();

          return $this->respondCreated($movieRepository->transform($movie));
      }


      /**
       * @Route("/movies/{id}/count")
       * @Method("POST")
       */

       public function increaseCount($id, EntityManagerInterface $em, MovieRepository $movieRepository)
       {

           $movie = $movieRepository->find($id);

           if (! $movie) {
                return $this->respondNotFound();
           }

           $movie->setCount($movie->getCount() +1);
           $em->persist($movie);
           $em->flush();

           return $this->respond([
               'count' => $movie->getCount()
           ]);
       }
    }