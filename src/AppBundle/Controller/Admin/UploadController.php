<?php


namespace AppBundle\Controller\Admin;


use AppBundle\Service\UploadRequest;
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
    /** @var UploadRequest */
    private $uploadRequest;

    /** @var UploadValidator */
    private $validator;

    /**
     * @Route("/validation", name="admin_validation_action", methods={"POST"})
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function validationAction(Request $request)
    {
        $mimes = array('application/vnd.ms-excel','text/plain','text/csv','text/tsv');

        if ( in_array($_FILES['upload']['type'], $mimes) ) {
            return new Response("File of this type cannot be handled!", 500);
        }

        $file = $request->files->get('upload')->getPathname();
        try {
            $data = $this->uploadRequest->getData($file);
        }catch (\Exception $exception) {
            return new Response($exception->getMessage(), $exception->getCode());
        }
        $errors = $this->validator->validate($data);

        return new JsonResponse($errors);
    }

    /**
     * @Route("/upload", name="admin_upload_action", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function uploadAction(Request $request)
    {
        if ($_FILES['upload']['type'] != 'text/csv') {
            return new Response("File of this type cannot be handled!", 500);
        }

        $file = $request->files->get('upload')->getPathname();
        try {
            $data = $this->uploadRequest->getData($file);
        }catch (\Exception $exception) {
            return new Response($exception->getMessage(), $exception->getCode());
        }

        return new Response("Merge");
    }

    /**
     * @param UploadRequest $uploadRequest
     * @required
     */
    public function setUploadRequestHandler(UploadRequest $uploadRequest)
    {
        $this->uploadRequest = $uploadRequest;
    }

    /**
     * @param UploadValidator $validator
     * @required
     */
    public function setValidator(UploadValidator $validator)
    {
        $this->validator = $validator;
    }
}