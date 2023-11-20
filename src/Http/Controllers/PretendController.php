<?php

namespace Souravmsh\PasswordManager\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Souravmsh\PasswordManager\Http\Traits\ApiResponse; 
use Auth;

class PretendController extends Controller
{    
    use ApiResponse;
    /**
     * instance 
     */
    private $user;
    private $request;
    private $viewPath;
    private $page;
    private $redirect;

    public function __construct(Request $request)
    { 
        $this->request  = $request;
        $this->user     = new (config('password-manager.user_model'));
        $this->viewPath = 'password-manager::pretend.';
        $this->page     = __('Pretend Login');
        $this->redirect = route('password-manager.expiry');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function show()
    {
        $data = (object)[
            'page'   => $this->page,
            'method' => 'Login',
            'action' => route('password-manager.pretend.login'),
            'item'   => [],
            'users'  => $this->user
                ->selectRaw('CONCAT(name, "  <", email, ">") AS name, id')
                ->pluck('name', 'id'),
        ]; 

        return view($this->viewPath . 'form', compact('data'));
    }

    /**
     * pretend login
     * @param Request $request
     * @return Response
     */
    public function login(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        try
        { 
            if(Auth::loginUsingId($request->user_id))
            {
                return $this->ajaxSuccess($request, __('Login successfully'), $this->redirect);
            }
            else
            {
                return $this->ajaxError($request, __('Unable to login'), $this->redirect); 
            } 
        }
        catch (\Exception $e)
        {
            return $this->ajaxError($e, __('Something went wrong, Internal Server Error'), $this->redirect); 
        }
    }  
}
