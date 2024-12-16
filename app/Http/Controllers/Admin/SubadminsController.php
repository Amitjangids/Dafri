<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Cookie;
use Session;
use Redirect;
use Input;
use Validator;
use DB;
use IsAdmin;
use App\Models\Admin;
use App\Models\Country;
use Mail;
use App\Mail\SendMailable;

class SubadminsController extends Controller {

    public function __construct() {
        $this->middleware('is_adminlogin');
        $this->middleware('is_subadminlogin');
    }

    public function index(Request $request) {
        $pageTitle = 'Manage Sub Admins';
        $activetab = 'actsubadmins';
        $query = new Admin();
        $query = $query->sortable();
        $query = $query->where('id','!=','1');

        if ($request->has('chkRecordId') && $request->has('action')) {
            $idList = $request->get('chkRecordId');
            $action = $request->get('action');

            if ($action == "Activate") {
                Admin::whereIn('id', $idList)->update(array('status' => 1));
                Session::flash('success_message', "Records are activate successfully.");
            } else if ($action == "Deactivate") {
                Admin::whereIn('id', $idList)->update(array('status' => 0));
                Session::flash('success_message', "Records are deactivate successfully.");
            } else if ($action == "Delete") {
                Admin::whereIn('id', $idList)->delete();
                Session::flash('success_message', "Records are deleted successfully.");
            }
        }

        if ($request->has('keyword')) {
            $keyword = $request->get('keyword');
            $query = $query->where(function($q) use ($keyword) {
                $q->where('name', 'like', '%' . $keyword . '%')
                        ->orWhere('email', 'like', '%' . $keyword . '%');
            });
        }

        $subadmins = $query->orderBy('id', 'DESC')->paginate(20);
        if ($request->ajax()) {
            return view('elements.admin.subadmins.index', ['allrecords' => $subadmins]);
        }
        return view('admin.subadmins.index', ['title' => $pageTitle, $activetab => 1, 'allrecords' => $subadmins]);
    }

    public function add() {
        $pageTitle = 'Add Sub Admin';
        $activetab = 'actsubadmins';

        $input = Input::all();
        if (!empty($input)) {
            $rules = array(
                'username' => 'required|max:50|unique:admins',
                'password' => 'required|min:8',
                'confirm_password' => 'required|same:password',
            );
            $validator = Validator::make($input, $rules);
            if ($validator->fails()) {
                return Redirect::to('/admin/subadmins/add')->withErrors($validator)->withInput();
            } else {
                
                $slug = $this->createSlug($input['username'], 'admins');
                $admin = new Admin([
                    'username' => $input['username'],
                    'email' => $input['email'],
                    'password' => $this->encpassword($input['password']),
                    'status' => 1,
                    'activation_status' => 1,
                    'slug' => $slug,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $admin->save();

                $name = $input['username'];
                $emailId = $input['email'];
                $new_password = $input['password'];

                $emailTemplate = DB::table('emailtemplates')->where('id', 2)->first();
                $toRepArray = array('[!email!]', '[!name!]', '[!username!]', '[!password!]', '[!HTTP_PATH!]', '[!SITE_TITLE!]');
                $fromRepArray = array($emailId, $name, $name, $new_password, HTTP_PATH, SITE_TITLE);
                $emailSubject = str_replace($toRepArray, $fromRepArray, $emailTemplate->subject);
                $emailBody = str_replace($toRepArray, $fromRepArray, $emailTemplate->template);
                //Mail::to($emailId)->send(new SendMailable($emailBody,$emailSubject));

                Session::flash('success_message', "Subadmin user details saved successfully.");
                return Redirect::to('admin/subadmins');
            }
        }
        return view('admin.subadmins.add', ['title' => $pageTitle, $activetab => 1]);
    }

    public function edit($slug = null) {
        $pageTitle = 'Edit Sub Admin';
        $activetab = 'actsubadmins';
        $countrList = Country::getCountryList();

        $recordInfo = Admin::where('slug', $slug)->first();
        if (empty($recordInfo)) {
            return Redirect::to('admin/subadmins');
        }

        $input = Input::all();
        if (!empty($input)) {

            $rules = array(
                'username' => 'required|max:50|unique:admins',
            );
            $validator = Validator::make($input, $rules);
            if ($validator->fails()) {
                return Redirect::to('/admin/subadmins/edit/' . $slug)->withErrors($validator)->withInput();
            } else {

                if ($input['password']) {
                    $input['password'] = $this->encpassword($input['password']);
                } else {
                    unset($input['password']);
                }
                $serialisedData = $this->serialiseFormData($input, 1); //send 1 for edit
                Admin::where('id', $recordInfo->id)->update($serialisedData);
                
                Session::flash('success_message', "Subadmin user details updated successfully.");
                return Redirect::to('admin/subadmins');
            }
        }
        return view('admin.subadmins.edit', ['title' => $pageTitle, $activetab => 1, 'countrList' => $countrList, 'recordInfo' => $recordInfo]);
    }

    public function activate($slug = null) {
        if ($slug) {
            Admin::where('slug', $slug)->update(array('status' => '1'));
            return view('elements.admin.update_status', ['action' => 'admin/subadmins/deactivate/' . $slug, 'status' => 1, 'id' => $slug]);
        }
    }

    public function deactivate($slug = null) {
        if ($slug) {
            Admin::where('slug', $slug)->update(array('status' => '0'));
            return view('elements.admin.update_status', ['action' => 'admin/subadmins/activate/' . $slug, 'status' => 0, 'id' => $slug]);
        }
    }

    public function delete($slug = null) {
        if ($slug) {
            Admin::where('slug', $slug)->delete();
            Session::flash('success_message', "Subadmin user details deleted successfully.");
            return Redirect::to('admin/subadmins');
        }
    }

}

?>