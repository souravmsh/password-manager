<?php
 
namespace Souravmsh\PasswordManager\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Souravmsh\PasswordManager\Models\PasswordManagerChecklist; 
use Souravmsh\PasswordManager\Http\Traits\ApiResponse;
use Souravmsh\PasswordManager\Http\Traits\PasswordManager;

class ChecklistController extends Controller
{    
    use PasswordManager, ApiResponse;

    private $request;
    private $passwordManagerChecklist;
    private $viewPath;
    private $page;
    private $redirect;

    public function __construct(Request $request)
    {
        $this->passwordManagerChecklist = new PasswordManagerChecklist;
        $this->request  = $request;
        $this->viewPath = 'password-manager::checklist.';
        $this->page     = __('Password Checklist');
        $this->redirect = route('password-manager.checklist');
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $page   = $this->page;
        $result = $this->getQuery()->paginate(10);

        return view($this->viewPath.'index', compact('page','result'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $data = (object)[
            'page'   => $this->page,
            'method' => 'Create',
            'action' => route('password-manager.checklist.save'),
            'item'   => [],
        ];

        return view($this->viewPath.'form', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'password' => 'required|between:1,255|unique:password_manager_checklist,password',
            'status'   => 'required|in:0,1'
        ]);

        try
        {
            $data = $this->passwordManagerChecklist;
            $data->password = $request->password; 
            $data->status   = $request->status;
            $data->save();
            
            // delete all cache
            $this->passwordForgetCache();

            return $this->ajaxSuccess($data, __('Saved successfully'), $this->redirect);
        }
        catch (\Exception $e)
        {
            return $this->ajaxError($e, __('Something went wrong, Internal Server Error'), $this->redirect); 
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $data = (object)[
            'page'   => $this->page,
            'method' => 'Update',
            'action' => route('password-manager.checklist.update', ['id' => $id]),
            'item'   => $this->passwordManagerChecklist->findOrFail($id)
        ];
        
        return view($this->viewPath.'form', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|between:1,255|unique:password_manager_checklist,password,'.$id,
            'status'   => 'required',
        ]);

        try
        {
            $data = $this->passwordManagerChecklist->find($id);
            $data->password  = $request->password;
            $data->status    = $request->status;
            $data->save(); 
            
            // delete all cache
            $this->passwordForgetCache();
     
            return $this->ajaxSuccess($data, __('Update successfully'), $this->redirect);
        }
        catch (\Exception $e)
        {
            return $this->ajaxError($e, __('Something went wrong, Internal Server Error'), $this->redirect); 
        }
    }

    private function getQuery()
    {
        return $this->passwordManagerChecklist
            ->where(function($q){
                if (!empty($this->request->password)) {
                    $q->where('password', $this->request->password);
                }
                if (!empty($this->request->status)) {
                    $q->where('status', ($this->request->status=='Active')?'1':'0');
                }
            })
            ->orderBy('password', 'asc');
    } 
}
