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
use App\Models\Scratchcard;
use App\Models\Country;
use Mail;
use App\Mail\SendMailable;

class ScratchcardsController extends Controller {

    public function __construct() {
        $this->middleware('is_adminlogin');
    }

    public function index(Request $request) {
        $pageTitle = 'Manage Scratchcards';
        $activetab = 'actscratchcards';
        $query = new Scratchcard();
        $query = $query->sortable();

        if ($request->has('chkRecordId') && $request->has('action')) {
            $idList = $request->get('chkRecordId');
            $action = $request->get('action');

            if ($action == "Activate") {
                Scratchcard::whereIn('id', $idList)->update(array('status' => 1));
                Session::flash('success_message', "Records are activated successfully.");
            } else if ($action == "Deactivate") {
                Scratchcard::whereIn('id', $idList)->update(array('status' => 0));
                Session::flash('success_message', "Records are deactivated successfully.");
            } else if ($action == "Delete") {
                Scratchcard::whereIn('id', $idList)->delete();
                Session::flash('success_message', "Records are deleted successfully.");
            }
        }

        if ($request->has('keyword')) {
            $keyword = $request->get('keyword');
            $query = $query->where(function($q) use ($keyword) {
                $q->where('card_number', 'like', '%' . $keyword . '%');
            });
        }

        $scratchcards = $query->orderBy('id', 'DESC')->paginate(20);
        if ($request->ajax()) {
            return view('elements.admin.scratchcards.index', ['allrecords' => $scratchcards]);
        }
        return view('admin.scratchcards.index', ['title' => $pageTitle, $activetab => 1, 'allrecords' => $scratchcards]);
    }

    public function add() {
        $pageTitle = 'Add Scratchcard';
        $activetab = 'actscratchcards';

        $bytes = random_bytes(8);
        $uniqueCardNumber = strtoupper(bin2hex($bytes));
        $chkCard = Scratchcard::where('card_number', $uniqueCardNumber)->first();
        if($chkCard){
            $bytes = random_bytes(8);
            $uniqueCardNumber = strtoupper(bin2hex($bytes));
        }        

        $input = Input::all();
        if (!empty($input)) {
            $rules = array(
                'card_value' => 'required',
                'expiry_date' => 'required',
            );
            $validator = Validator::make($input, $rules);
            if ($validator->fails()) {
                return Redirect::to('/admin/scratchcards/add')->withErrors($validator)->withInput();
            } else {


                $serialisedData = $this->serialiseFormData($input);
                $serialisedData['card_number'] = $uniqueCardNumber;
                $serialisedData['status'] = 1;
                Scratchcard::insert($serialisedData);

                Session::flash('success_message', "Scratch card details saved successfully.");
                return Redirect::to('admin/scratchcards');
            }
        }
        return view('admin.scratchcards.add', ['title' => $pageTitle, $activetab => 1,'uniqueCardNumber' => $uniqueCardNumber]);
    }

    public function edit($id = null) {
        $pageTitle = 'Edit Scratchcard';
        $activetab = 'actscratchcards';

        $recordInfo = Scratchcard::where('id', $id)->first();
        if (empty($recordInfo)) {
            return Redirect::to('admin/scratchcards');
        }

        $input = Input::all();
        if (!empty($input)) {

            $rules = array(
                'card_value' => 'required',
                'expiry_date' => 'required',
            );
            $validator = Validator::make($input, $rules);
            if ($validator->fails()) {
                return Redirect::to('/admin/scratchcards/edit/' . $id)->withErrors($validator)->withInput();
            } else {

                $serialisedData = $this->serialiseFormData($input, 1); //send 1 for edit
                Scratchcard::where('id', $recordInfo->id)->update($serialisedData);
                Session::flash('success_message', "Scratch card details updated successfully.");
                return Redirect::to('admin/scratchcards');
            }
        }
        return view('admin.scratchcards.edit', ['title' => $pageTitle, $activetab => 1, 'recordInfo' => $recordInfo]);
    }

    public function activate($slug = null) {
        if ($slug) {
            Scratchcard::where('id', $slug)->update(array('status' => '1'));
            return view('elements.admin.active_status', ['action' => 'admin/scratchcards/deactivate/' . $slug, 'status' => 1, 'id' => $slug]);
        }
    }

    public function deactivate($slug = null) {
        if ($slug) {
            Scratchcard::where('id', $slug)->update(array('status' => '0'));
            return view('elements.admin.active_status', ['action' => 'admin/scratchcards/activate/' . $slug, 'status' => 0, 'id' => $slug]);
        }
    }

    public function delete($slug = null) {
        if ($slug) {
            Scratchcard::where('id', $slug)->delete();
            Session::flash('success_message', "Scratch card details deleted successfully.");
            return Redirect::to('admin/scratchcards');
        }
    }

}

?>