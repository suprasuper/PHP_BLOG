<?php 

namespace Controllers;

use Core\Controller;

class AdminController extends Controller
{
   

    public function dashboard()
    {
        $this->requireAdmin();

        $this->render('admin/dashboard.html.twig');
    }
}
