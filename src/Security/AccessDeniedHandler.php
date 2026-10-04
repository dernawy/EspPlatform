<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;


class AccessDeniedHandler implements AccessDeniedHandlerInterface
{

    /**
     * @inheritDoc
     */
    public function handle(Request $request, AccessDeniedException $accessDeniedException): ?Response
    {


        $accessDeniedException->setSubject('hello');
        $content = '
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=0.8">
            <link href="/library/bootstrap/5.3.8/css/bootstrap.css" rel="stylesheet">
            <script  type="text/javascript" src="/library/bootstrap/5.3.8/js/bootstrap.js"></script>
            <div class="container-fluid">
                <div class="row m-20 p-5 d-flex justify-content-center align-items-center">
                    <div class="col-lg-6">
                        <div class="alert alert-danger page-alert" role="alert">You did\'nt have the permissions to access this page</div>
                    </div>
                </div>
                
                <div class="row m-20 p-5 d-flex justify-content-center align-items-center">
                    <div class="col-lg-6 d-flex justify-content-center align-items-center">
                        <div class="card" style="width: 60rem;">
                              <img src="/media/images/site/icons_and_style/access_denied.png" class="card-img-top" alt="Access Denied">
                              <div class="card-body">
                                    <h5 class="card-title">Please click the button below to go to home page </h5>
                                    
                                    <a href="/" class="btn btn-primary">Go To Home Page</a>
                              </div>
                        </div>
                    </div>
                </div>
                 
            </div>
        ';

        return new Response($content, 403);
        // TODO: Implement handle() method.
    }
}