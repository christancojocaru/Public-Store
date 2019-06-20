<?php


namespace AppBundle\Controller\Admin;


use AppBundle\Service\UploadRequestHandler;
use AppBundle\Validator\UploadValidator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UploadController
 * @package AppBundle\Controller\Admin
 * @Route("/admin")
 */
class UploadController extends Controller
{
    /**
     * @Route("/upload", name="admin_upload_action", methods={"POST"})
     * @param Request $request
     * @param UploadRequestHandler $requestHandler
     * @param UploadValidator $validator
     * @return JsonResponse|Response
     */
    public function uploadAction(Request $request, UploadRequestHandler $requestHandler, UploadValidator $validator)
    {
        if ($_FILES['upload']['type'] != 'text/csv') {
            return new Response("File of this type cannot be handled!", 500);
        }

        $file = $request->files->get('upload')->getPathname();
        try {
            $data = $requestHandler->getData($file);
        }catch (\Exception $exception) {
            return new Response($exception->getMessage(), $exception->getCode());
        }
        $errors = $validator->validate($data);

        return new JsonResponse($errors);
    }
}