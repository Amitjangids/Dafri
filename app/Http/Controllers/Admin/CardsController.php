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
use App\Models\Card;
use App\Models\Carddetail;
use App\Models\Country;
use Mail;
use App\Mail\SendMailable;

class CardsController extends Controller {

    public function __construct() {
        $this->middleware('is_adminlogin');
    }

    public function index(Request $request) {
        $pageTitle = 'Manage Cards';
        $activetab = 'actcards';
        $query = new Card();
        $query = $query->sortable();

        if ($request->has('chkRecordId') && $request->has('action')) {
            $idList = $request->get('chkRecordId');
            $action = $request->get('action');

            if ($action == "Activate") {
                Card::whereIn('id', $idList)->update(array('status' => 1));
                Session::flash('success_message', "Records are activated successfully.");
            } else if ($action == "Deactivate") {
                Card::whereIn('id', $idList)->update(array('status' => 0));
                Session::flash('success_message', "Records are deactivated successfully.");
            } else if ($action == "Delete") {
                Card::whereIn('id', $idList)->delete();
                Session::flash('success_message', "Records are deleted successfully.");
            }
        }

        if ($request->has('keyword')) {
            $keyword = $request->get('keyword');
            $query = $query->where(function($q) use ($keyword) {
                $q->where('company_name', 'like', '%' . $keyword . '%');
            });
        }

        $cards = $query->orderBy('id', 'DESC')->paginate(20);
        if ($request->ajax()) {
            return view('elements.admin.cards.index', ['allrecords' => $cards]);
        }
        return view('admin.cards.index', ['title' => $pageTitle, $activetab => 1, 'allrecords' => $cards]);
    }

    public function add() {
        $pageTitle = 'Add Card';
        $activetab = 'actcards';

        $input = Input::all();
        if (!empty($input)) {
            $rules = array(
                'card_type' => 'required',
                'company_name' => 'required|max:50',
                'company_image' => 'required|mimes:jpeg,png,jpg',
            );
            $validator = Validator::make($input, $rules);
            if ($validator->fails()) {
                return Redirect::to('/admin/cards/add')->withErrors($validator)->withInput();
            } else {                

                if (Input::hasFile('company_image')) {
                    $file = Input::file('company_image');
                    $uploadedFileName = $this->uploadImage($file, COMPANY_FULL_UPLOAD_PATH);
//                    $this->resizeImage($uploadedFileName, COMPANY_FULL_UPLOAD_PATH, COMPANY_SMALL_UPLOAD_PATH, COMPANY_MW, COMPANY_MH);
                    $input['company_image'] = $uploadedFileName;
                } else {
                    unset($input['company_image']);
                }                

                $serialisedData = $this->serialiseFormData($input);
                $serialisedData['slug'] = $this->createSlug($input['company_name'], 'cards');
                $serialisedData['status'] = 1;
                Card::insert($serialisedData);

                Session::flash('success_message', "Card details saved successfully.");
                return Redirect::to('admin/cards');
            }
        }
        return view('admin.cards.add', ['title' => $pageTitle, $activetab => 1]);
    }

    public function edit($slug = null) {
        $pageTitle = 'Edit Card';
        $activetab = 'actcards';

        $recordInfo = Card::where('slug', $slug)->first();
        if (empty($recordInfo)) {
            return Redirect::to('admin/cards');
        }

        $input = Input::all();
        if (!empty($input)) {
            
            $rules = array(
                'card_type' => 'required',
                'company_name' => 'required|max:50',
            );
            $validator = Validator::make($input, $rules);
            if ($validator->fails()) {
                return Redirect::to('/admin/cards/edit/' . $slug)->withErrors($validator)->withInput();
            } else {

                if (Input::hasFile('company_image')) {
                    $file = Input::file('company_image');
                    $uploadedFileName = $this->uploadImage($file, COMPANY_FULL_UPLOAD_PATH);
//                    $this->resizeImage($uploadedFileName, COMPANY_FULL_UPLOAD_PATH, COMPANY_SMALL_UPLOAD_PATH, COMPANY_MW, COMPANY_MH);
                    $input['company_image'] = $uploadedFileName;
                    @unlink(COMPANY_FULL_UPLOAD_PATH . $recordInfo->company_image);
                } else {
                    unset($input['company_image']);
                }    
                
                $serialisedData = $this->serialiseFormData($input, 1); //send 1 for edit
                Card::where('id', $recordInfo->id)->update($serialisedData);
                Session::flash('success_message', "card details updated successfully.");
                return Redirect::to('admin/cards');
            }
        }
        return view('admin.cards.edit', ['title' => $pageTitle, $activetab => 1, 'recordInfo' => $recordInfo]);
    }    

    public function activate($slug = null) {
        if ($slug) {
            Card::where('slug', $slug)->update(array('status' => '1'));
            return view('elements.admin.active_status', ['action' => 'admin/cards/deactivate/' . $slug, 'status' => 1, 'id' => $slug]);
        }
    }

    public function deactivate($slug = null) {
        if ($slug) {
            Card::where('slug', $slug)->update(array('status' => '0'));
            return view('elements.admin.active_status', ['action' => 'admin/cards/activate/' . $slug, 'status' => 0, 'id' => $slug]);
        }
    }

    public function delete($slug = null) {
        if ($slug) {
            Card::where('slug', $slug)->delete();
            Session::flash('success_message', "Card details deleted successfully.");
            return Redirect::to('admin/cards');
        }
    }
    
    public function carddetail($cslug = null,Request $request) {
        $pageTitle = 'Manage Cards Details';
        $activetab = 'actcards';
        
        $cardInfo = Card::where('slug', $cslug)->first();
        
        $query = new Carddetail();
        $query = $query->sortable();
        $query = $query->where('card_id',$cardInfo->id);

        if ($request->has('chkRecordId') && $request->has('action')) {
            $idList = $request->get('chkRecordId');
            $action = $request->get('action');

            if ($action == "Activate") {
                Carddetail::whereIn('id', $idList)->update(array('status' => 1));
                Session::flash('success_message', "Records are activated successfully.");
            } else if ($action == "Deactivate") {
                Carddetail::whereIn('id', $idList)->update(array('status' => 0));
                Session::flash('success_message', "Records are deactivated successfully.");
            } else if ($action == "Delete") {
                Carddetail::whereIn('id', $idList)->delete();
                Session::flash('success_message', "Records are deleted successfully.");
            }
        }

        if ($request->has('keyword')) {
            $keyword = $request->get('keyword');
            $query = $query->where(function($q) use ($keyword) {
                $q->where('serial_number', 'like', '%' . $keyword . '%');
            });
        }

        $cards = $query->orderBy('id', 'DESC')->paginate(20);
        if ($request->ajax()) {
            return view('elements.admin.cards.carddetail', ['allrecords' => $cards,'cslug' => $cslug]);
        }
        return view('admin.cards.carddetail', ['title' => $pageTitle, $activetab => 1, 'allrecords' => $cards,'cslug' => $cslug]);
    }

    public function addcarddetail($cslug = null) {
        $pageTitle = 'Add Card Detail';
        $activetab = 'actcards';
        
        $cardInfo = Card::where('slug', $cslug)->first();

        $input = Input::all();
        if (!empty($input)) { 
            $rules = array(
                'serial_number' => 'required',
                'pin_number' => 'required',
                'card_value' => 'required',
                'instruction' => 'required',
            );
            $validator = Validator::make($input, $rules);
            if ($validator->fails()) {
                return Redirect::to('/admin/cards/addcarddetail')->withErrors($validator)->withInput();
            } else {                 

                $serialisedData = $this->serialiseFormData($input);
                $serialisedData['card_id'] = $cardInfo->id;
                $serialisedData['status'] = 1;
                Carddetail::insert($serialisedData);

                Session::flash('success_message', "Card details saved successfully.");
                return Redirect::to('admin/cards/carddetail/'.$cslug);
            }
        }
        return view('admin.cards.addcarddetail', ['title' => $pageTitle, $activetab => 1]);
    }

    public function editcarddetail($cslug = null,$id = null) {
        $pageTitle = 'Edit Card';
        $activetab = 'actcards';

        $recordInfo = Carddetail::where('id', $id)->first();
        if (empty($recordInfo)) {
            return Redirect::to('admin/cards/carddetail/'.$cslug);
        }

        $input = Input::all();
        if (!empty($input)) {
            
            $rules = array(
                'serial_number' => 'required',
                'pin_number' => 'required',
                'card_value' => 'required',
                'instruction' => 'required',
            );
            $validator = Validator::make($input, $rules);
            if ($validator->fails()) {
                return Redirect::to('/admin/cards/editcarddetail/' . $cslug .'/'. $id)->withErrors($validator)->withInput();
            } else {    
                
                $serialisedData = $this->serialiseFormData($input, 1); //send 1 for edit
                Carddetail::where('id', $recordInfo->id)->update($serialisedData);
                Session::flash('success_message', "Card details updated successfully.");
                return Redirect::to('admin/cards/carddetail/'.$cslug);
            }
        }
        return view('admin.cards.editcarddetail', ['title' => $pageTitle, $activetab => 1, 'recordInfo' => $recordInfo,'cslug' => $cslug]);
    }    

    public function activatecarddetail($slug = null) {
        if ($slug) {
            Carddetail::where('id', $slug)->update(array('status' => '1'));
            return view('elements.admin.active_status', ['action' => 'admin/cards/deactivatecarddetail/' . $slug, 'status' => 1, 'id' => $slug]);
        }
    }

    public function deactivatecarddetail($slug = null) {
        if ($slug) {
            Carddetail::where('id', $slug)->update(array('status' => '0'));
            return view('elements.admin.active_status', ['action' => 'admin/cards/activatecarddetail/' . $slug, 'status' => 0, 'id' => $slug]);
        }
    }

    public function deletecarddetail($cslug = null,$slug = null) {
        if ($slug) {
            Carddetail::where('id', $slug)->delete();
            Session::flash('success_message', "Card details deleted successfully.");
            return Redirect::to('admin/cards/carddetail/'.$cslug);
        }
    }

}

?>