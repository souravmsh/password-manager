<?php

namespace Souravmsh\PasswordManager\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Souravmsh\PasswordManager\Models\PasswordManagerRules;
use Souravmsh\PasswordManager\Http\Traits\PasswordManager;
use Souravmsh\PasswordManager\Http\Traits\ApiResponse;

class RulesController extends Controller
{    
    use PasswordManager, ApiResponse;
    /**
     * Request instance
     *
     * @var Request
     */
    private $request;
    private $passwordManagerRules;
    private $viewPath;
    private $page;
    private $redirect;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->passwordManagerRules = new PasswordManagerRules;
        $this->viewPath = 'password-manager::rules.';
        $this->page     = __('Rules');
        $this->redirect = route('password-manager.rules');
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $page   = $this->page;
        $result = $this->getQuery()->paginate(10); 

        $rules = array_combine(array_keys($this->passwordRulesAttributeValue), array_keys($this->passwordRulesAttributeValue));

        return view($this->viewPath.'index', compact('page','result', 'rules'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $data = (object)[
            'page'   => $this->page,
            'method' => 'Update',
            'action' => route('password-manager.rules.update'),
            'item'   => [],
            'attributes' => $this->passwordRulesAttributeValue
        ];

        return view($this->viewPath . 'form', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function update(Request $request)
    {
        $request->validate([
            'rules.*'   => 'required',
            'rules.*.*' => 'required'
        ]);
  
        try
        {
            $exists = $this->passwordManagerRules->pluck('attribute')->toArray();

            foreach ($request->only('rules')['rules'] as $key => $input) 
            {
                $input = (is_array($input) || is_object($input) ? json_encode($input): $input);
                if(in_array($key, $exists))
                {
                    // update
                    $this->passwordManagerRules
                        ->where('attribute', $key)
                        ->update(['value' => $input]);
                }
                else
                {
                    // insert
                    $this->passwordManagerRules 
                        ->insert(['attribute' => $key, 'value' => $input]);
                }
            }  
            
            // delete all cache
            $this->passwordForgetCache();

            return $this->ajaxSuccess($request, __('Update successfully'), $this->redirect);
        }
        catch (\Exception $e)
        {
            return $this->ajaxError($e, __('Something went wrong, Internal Server Error'), $this->redirect); 
        }
    }

    private function getQuery()
    {
        return $this->passwordManagerRules
            ->where(function($q){
                if (!empty($this->request->attribute)) {
                    $q->where('attribute', $this->request->attribute);
                } 
                if (!empty($this->request->value)) {
                    $q->where('value', $this->request->value);
                } 
            });
    } 
}
