<?php

namespace Souravmsh\PasswordManager\Http\Traits;

use Illuminate\Contracts\Validation\ImplicitRule;
use Illuminate\Support\Facades\Hash;

/*
|--------------------------------------------------------------------------------
| HOW TO USE
|_________________________________________________________________________________
|
| PASSWORD VALIDATION
|----------------------------
| use App\Traits\PasswordManager;
| # use trait in class
| use PasswordManager;
|
| # default fields - password, old_password, password_confirmation
| $this->passwordValidate(); 
|
| # custom fields 
| $this->passwordValidate('password_custom_name', 'old_password_custom_name'); 
|
| # combine password rules with other existing rules
| $this->validate($request, $this->passwordValidate('password', '', '', [
|    'name'  => 'required',
|    'email' => 'required',
| ]));
|
|
| PASSWORD EXPIRY CHECK
|----------------------------
| # Middleware - add it to the Kernel.php inside the web guard middleware group
| \Modules\PasswordManager\Http\Middleware\PasswordExpiryCheck::class,
|
| # Add the method to Auth\LoginController.php (optional)
|   protected function authenticated(Request $request, $user)
|   { 
|       # paramets (optional):- $redirect(true/false), $redirectRoute
|       $isExpired = $this->passwordLoginIsExpired();
|       if ($isExpired && !in_array($request->route()->getName(), $this->passwordLoginIsExpiredRouteAllowed))
|       {
|           return redirect($isExpired);
|       }
|   } 
|
| CLEAR CACHE
|----------------------------
| passwordForgetCache();
|_________________________________________________________________________________
*/

trait PasswordManager
{
    private $userTableAndEmailAndPasswordField = 'users,email,password';
    private $passwordCacheMinutes = 720; // set -1 minutes to disabled the cache
    private $passwordRequest;
    private $passwordDBCheckList;
    private $passwordRules;
    private $passwordDBOldPasswordCheck  = true;
    private $passwordLoginIsExpiredRoute = 'password-manager.password';
    private $passwordLoginIsExpiredRouteAllowed = ['password-manager.password', 'password-manager.password.reset'];
    public  $passwordRulesAttributeValue = [
        'min'         => 4,
        'max'         => 32,
        'password_strength' => [
            'poor'   => 4,
            'normal' => 6,
            'medium' => 8,
            'strong' => 10,
        ], 
        'ignore_weak_password' => ['true', 'false'],
        'ignore_old_password'  => ['true', 'false'],
        'password_expiry_days' => 30,
    ];

    public function __config()
    {
        $this->passwordCacheMinutes = config('password-manager.cache_minutes');
        $this->passwordDBOldPasswordCheck = config('password-manager.check_old_password');
    }

    
    public function __init() 
    {
        $this->__config();

        $this->passwordRequest     = request(); 
        $this->passwordDBCheckList = $this->passwordDBCheckList(); 
        $this->passwordDBRules     = $this->passwordDBRules();
    }

    public function passwordValidate($fieldPwd = 'password', $fieldOldPwd = 'old_password', $fieldConfPwd = 'password_confirmation',  $combineRules = [])
    {  
        $this->__init();
        
        $fieldPwd     = !empty($fieldPwd)?$fieldPwd:'password';
        $fieldOldPwd  = !empty($fieldOldPwd)?$fieldOldPwd:'old_password';
        $fieldConfPwd = !empty($fieldOldPwd)?$fieldOldPwd:'password_confirmation';

        // manage db rules
        $this->passwordRulesManager($this->passwordDBRules, $fieldPwd, $fieldOldPwd, $fieldConfPwd); 

        // return combine rules
        if (!empty($combineRules) && is_array($combineRules)) 
        {
            return array_merge($combineRules, $this->passwordRules); 
        }

        request()->validate($this->passwordRules);
    }


    public function passwordLoginIsExpired($redirect=true, $redirectRoute = '')
    {   
        if (auth()->hasUser())
        {
            $this->__config();

            $isExpired = true;
            $redirectRoute = !empty($redirectRoute)?$redirectRoute:$this->passwordLoginIsExpiredRoute;
            $data = cache()->remember('passwordLoginIsExpired', $this->passwordCacheMinutes, function () {
                return \DB::table('password_manager_expiry')
                    ->where('user_id', auth()->user()->id)
                    ->first(); 
            });

            if (empty($data))
            {  
                \DB::table('password_manager_expiry')->insert([
                    'user_id'     => auth()->user()->id,
                    'expiry_days' => 30,
                    'updated_at'  => date('Y-m-d')
                ]);

                // if expired and redirect is true then return redirect to password reset page
                if ($redirect && $isExpired)
                {
                    return route($redirectRoute);
                }
                
                // return boolean(true/false)
                return $isExpired;
            }


            $today       = strtotime(date('Y-m-d'));
            $expiry_date = !empty($data->updated_at)?strtotime($data->updated_at. " +{$data->expiry_days} days"):0;

            if ($expiry_date > $today)
            { 
                $isExpired = false;
            } 

            // delete old cache
            if (!empty($data) && auth()->user()->id != $data->user_id)
            {
                cache()->forget('passwordLoginIsExpired');
            }

            // if expired and redirect is true then return redirect to password reset page
            if ($redirect && $isExpired)
            {
                return route($redirectRoute);
            }
            
            // return boolean(true/false)
            return $isExpired;
        }

        return false;
    }

    public function passwordForgetCache()
    {
        if (cache('passwordDBRules'))
        {
            cache()->forget('passwordDBRules');
        }

        if (cache('passwordDBCheckList'))
        {
            cache()->forget('passwordDBCheckList');
        }

        if (cache('passwordLoginIsExpired'))
        {
            cache()->forget('passwordLoginIsExpired');
        }
    }
 
    /*
    *
    * DATABASE RULES
    * -------------------------------------------------
    */ 
    private function passwordDBRules()
    {  
        return cache()->remember('passwordDBRules', $this->passwordCacheMinutes, function () {
            return \DB::table('password_manager_rules')
                ->where('value', '!=', 'false')
                ->pluck('value', 'attribute')
                ->toArray();
        }); 
    }

    private function passwordDBCheckList()
    {       
        return cache()->remember('passwordDBCheckList', $this->passwordCacheMinutes, function () {
            return \DB::table('password_manager_checklist')
                ->where('status', 1)
                ->pluck('password')
                ->toArray();
        }); 
    }

    /*
    *
    * MANAGE & PROCESS RULES 
    * -------------------------------------------------
    */   
    private function passwordRulesManager($data = [], $fieldPwd, $fieldOldPwd, $fieldConfPwd)
    { 
        $rules     = [];
        $rulesOld  = [];
        $rulesConf = [];
        $password     = $this->passwordRequest->{$fieldPwd};
        $old_password = $this->passwordRequest->{$fieldOldPwd};
        $password_confirmation = $this->passwordRequest->{$fieldConfPwd};

        $rules[] = 'required';

        foreach ($data as $attribute => $value) 
        {
            $attribute = trim($attribute);

            if (array_key_exists($attribute, $this->passwordRulesAttributeValue))
            {  
                switch ($attribute) 
                { 
                    case 'min':
                        $rules[] = 'min:'.$value.'';
                    break; 
                    case 'max':
                        $rules[] = 'max:'.$value.'';
                    break;
                    case 'password_strength':
                        $obj = $this->isJson($value);
                        if ($obj !== false)
                        {
                            $type = $obj->type ?? '';
                            switch ($type) 
                            {
                                case 'poor':
                                    $rules[] =  new PoorRule($attribute, $password);
                                break; 
                                case 'normal':
                                    $rules[] =  new NormalRule($attribute, $password);
                                break; 
                                case 'medium':
                                    $rules[] =  new MediumRule($attribute, $password);
                                break; 
                                case 'strong':
                                    $rules[] = 'min:'.($obj->min_length ?? 4).'';
                                    $rules[] =  new StrongRule($attribute, $password);
                                break; 
                            }
                        } 
                    break; 
                    case 'ignore_weak_password':
                        $rules[] =  new IgnoreWeakPassword($attribute, (in_array($password, $this->passwordDBCheckList)));
                    break;
                    case 'ignore_old_password':
                        if($this->passwordRequest->has($fieldOldPwd))
                        {
                            if (!empty($old_password))
                            {
                                // check auth user password
                                if ($this->passwordDBOldPasswordCheck && auth()->hasUser())
                                {
                                    $rules[] =  new MatchDatabaseOldPassword($attribute, $fieldOldPwd, Hash::check($old_password, (auth()->user()->{$fieldPwd})));
                                }

                                $rules[] =  new IgnoreOldPassword($attribute, 'old_password', ($password == $old_password));
                            }
                        } 
                        else if ($this->passwordDBOldPasswordCheck && $this->passwordRequest->filled('email') && $this->passwordRequest->filled('token'))
                        { 
                            // default reset token form data
                            $credentials = explode(',', $this->userTableAndEmailAndPasswordField);
                            $user = \DB::table($credentials[0])
                                ->where($credentials[1], $this->passwordRequest->email)
                                ->select($credentials[2])
                                ->first(); 

                            if ($user)
                            {
                                $rules[] =  new IgnoreOldPassword($attribute, 'old password', (Hash::check($password, $user->{$credentials[2]})));
                            }  
                        }
                    break;
                }  
            }
        }

        if ($this->passwordRequest->has($fieldConfPwd))
        {
            $rulesConf[] = 'required';
            $rules[]     = 'confirmed';
        }
        if ($this->passwordRequest->has($fieldOldPwd))
        {
            $rulesOld[] = 'required';
        }

        $this->passwordRules[$fieldPwd] = $rules; 
        $this->passwordRules[$fieldOldPwd] = $rulesOld;
        $this->passwordRules[$fieldConfPwd] = $rulesConf;
    } 

    private function isJson($string) 
    {
        $obj = json_decode($string);
        if (json_last_error() == JSON_ERROR_NONE)
        {
            return $obj;
        } 
        return false;
    }

}


/*
| PASSWORD STRENGTH : POOR - Any Type of Characters
|----------------------------------------------------------------------------------------
*/
class PoorRule implements ImplicitRule
{
    public function passes($attribute, $value)
    {
        return true;
    }
    public function message()
    {
        return 'The :attribute field is required.';
    }
}

/*
| PASSWORD STRENGTH : NORMAL - Minimum Letters and Digits
|----------------------------------------------------------------------------------------
*/
class NormalRule implements ImplicitRule
{
    public function passes($attribute, $value)
    {
        return preg_match('/^(?=.*[A-Za-z])(?=.*\d)/', $value); 
    }
    public function message()
    {
        return 'The :attribute field must contain at least one letter and one number.';
    }
}

/*
| PASSWORD STRENGTH : MEDIUM - Minimum Letters, Digits and Special Characters
|----------------------------------------------------------------------------------------
*/
class MediumRule implements ImplicitRule
{
    public function passes($attribute, $value)
    {
        return preg_match('/^(?=.*[A-Za-z])(?=.*[$&+,:;=?@#|<>._^*(){}\/%!-])(?=.*\d)[A-Za-z\d$&+,:;=?@#|<>._^*(){}\/%!-]/', $value);
    }
    public function message()
    {
        return 'The :attribute field must contain at least one letter and one number and one special character';
    }
}

/*
| PASSWORD STRENGTH : STRONG - Letters(UpperCase+LowerCase), Digits, Special Characters & Length
|----------------------------------------------------------------------------------------
*/
class StrongRule implements ImplicitRule
{
    public function passes($attribute, $value)
    { 
        return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[$&+,:;=?@#|<>._^*(){}\/%!-])(?=.*\d)[a-zA-Z\d$&+,:;=?@#|<>._^*(){}\/%!-]/', $value);
    }
    public function message()
    {
        return 'The :attribute field must contain at least one uppercase letter, one lowercase letter, one number and one special character';
    }
} 

/*
| IGNORE WEAK PASSWORD
|----------------------------------------------------------------------------------------
*/
class IgnoreWeakPassword implements ImplicitRule
{
    private $property;
    public function __construct($attribute, $value)
    {
        $this->property = $value;
    }
    public function passes($attribute, $value)
    {
        return $this->property == false;
    }
    public function message()
    {
        return 'The :attribute is very weak and not allowed.';
    }
}

/*
| MATCH DATABASE PASSWORD
|----------------------------------------------------------------------------------------
*/
class MatchDatabaseOldPassword implements ImplicitRule
{ 
    private $property;
    private $attribute2;
    public function __construct($attribute, $attribute2, $value)
    {
        $this->attribute2 = $attribute2;
        $this->property   = $value;
    }
    public function passes($attribute, $value)
    {
        return $this->property == true;
    }
    public function message()
    { 
        // fire if passes false
        return "The {$this->attribute2} do not match with the database records!";
    }
}
/*
| IGNORE OLD PASSWORD
|----------------------------------------------------------------------------------------
*/
class IgnoreOldPassword implements ImplicitRule
{
    private $property;
    private $attribute2;
    public function __construct($attribute, $attribute2, $value)
    {
        $this->attribute2 = $attribute2;
        $this->property   = $value;
    }
    public function passes($attribute, $value)
    {
        return $this->property == false;
    }
    public function message()
    { 
        // fire if passes false
        return "The :attribute and {$this->attribute2} cannot be same!";
    }
}
