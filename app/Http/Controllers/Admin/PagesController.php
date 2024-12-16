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
use App\Models\Page;
use App\Blog;
use App\Faq;
use App\Category;
use Mail;
use App\Mail\SendMailable;

class PagesController extends Controller {

    public function __construct() {
        $this->middleware('is_adminlogin');
    }

    public function index(Request $request) {
        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'list-pages');
        if ($isPermitted == false) {
            $pageTitle = 'Not Permitted';
            $activetab = 'actchangeusername';
            return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
        }
        $pageTitle = 'Manage Pages';
        $activetab = 'actpages';
        $query = new Page();
        $query = $query->sortable();

        if ($request->has('chkRecordId') && $request->has('action')) {
            $idList = $request->get('chkRecordId');
            $action = $request->get('action');
            if ($action == "Activate") {
                Page::whereIn('id', $idList)->update(array('status' => 1, 'activation_status' => 1));
                Session::flash('success_message', "Records are activated successfully.");
            } else if ($action == "Deactivate") {
                Page::whereIn('id', $idList)->update(array('status' => 0));
                Session::flash('success_message', "Records are deactivated successfully.");
            } else if ($action == "Delete") {
                Page::whereIn('id', $idList)->delete();
                Session::flash('success_message', "Records are deleted successfully.");
            }
        }

        if ($request->has('keyword')) {
            $keyword = $request->get('keyword');
            $query = $query->where(function($q) use ($keyword) {
                $q->where('first_name', 'like', '%' . $keyword . '%')
                        ->orWhere('last_name', 'like', '%' . $keyword . '%');
            });
        }

        $pages = $query->orderBy('id', 'DESC')->paginate(20);
        if ($request->ajax()) {
            return view('elements.admin.pages.index', ['allrecords' => $pages]);
        }
        return view('admin.pages.index', ['title' => $pageTitle, $activetab => 1, 'allrecords' => $pages]);
    }

    public function edit($slug = null) {
        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'edit-pages');
        if ($isPermitted == false) {
            $pageTitle = 'Not Permitted';
            $activetab = 'actchangeusername';
            return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
        }
        $pageTitle = 'Edit Page';
        $activetab = 'actpages';
        $countrList = array('1' => 'India', '2' => 'USA', '3' => 'AUS');

        $recordInfo = Page::where('slug', $slug)->first();
        if (empty($recordInfo)) {
            return Redirect::to('admin/pages');
        }

        $input = Input::all();
        if (!empty($input)) {
            $rules = array(
                'title' => 'required|unique:pages,title,' . $recordInfo->id,
            );
            $validator = Validator::make($input, $rules);
            if ($validator->fails()) {
                return Redirect::to('/admin/pages/edit/' . $slug)->withErrors($validator)->withInput();
            } else {
                $input['edited_by'] = Session::get('adminid');
                $serialisedData = $this->serialiseFormData($input, 1); //send 1 for edit
                Page::where('id', $recordInfo->id)->update($serialisedData);
                Session::flash('success_message', "Page details updated successfully.");
                return Redirect::to('admin/pages');
            }
        }
        return view('admin.pages.edit', ['title' => $pageTitle, $activetab => 1, 'countrList' => $countrList, 'recordInfo' => $recordInfo]);
    }

    public function pageimages() {
        $file = Input::file('upload');
        $uploadedFileName = $this->uploadImage($file, CK_IMAGE_UPLOAD_PATH);
        echo "<span style='font-size: 12px; color: #f00; font-weight: bold;'>Copy below URL and Paste it in Image Info tab and than click OK button:</span> <span style='float: left; font-size: 13px; margin: 2px 0 0; width: 100%;'>" . CK_IMAGE_DISPLAY_PATH . $uploadedFileName . '</span>';
        exit;
    }

    public function blogs(Request $request) {
        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'list-blogs');
        if ($isPermitted == false) {
            $pageTitle = 'Not Permitted';
            $activetab = 'actchangeusername';
            return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
        }

        $pageTitle = 'Manage Blogs';
        $activetab = 'actblogs';
        $query = new Blog();
        $query = $query->sortable();

        $blogs = $query->orderBy('id', 'DESC')->paginate(20);
        if ($request->ajax()) {
            return view('elements.admin.pages.blogs', ['allrecords' => $blogs]);
        }
        return view('admin.pages.blogs', ['title' => $pageTitle, $activetab => 1, 'allrecords' => $blogs]);
    }

    public function addBlog() {
        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'add-blog');
        if ($isPermitted == false) {
            $pageTitle = 'Not Permitted';
            $activetab = 'actchangeusername';
            return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
        }
        $pageTitle = 'Add Blog';
        $activetab = 'actblogs';

        $input = Input::all();
        if (!empty($input)) {
            $rules = array(
                'category_id' => 'required',
                'title' => 'required',
                'description' => 'required',
                'blogImage' => 'required|mimes:jpeg,png,jpg',
                'blogReadTm' => 'required',
            );

            $validator = Validator::make($input, $rules);
            if ($validator->fails()) {
                return Redirect::to('/admin/blogs/add')->withErrors($validator)->withInput();
            } else {
                if (Input::hasFile('blogImage')) {
                    $file = Input::file('blogImage');
                    $uploadedFileName = $this->uploadImage($file, BLOG_FULL_UPLOAD_PATH);
//                    $this->resizeImage($uploadedFileName, BLOG_FULL_UPLOAD_PATH, BLOG_SMALL_UPLOAD_PATH, BLOG_MW, BLOG_MH);
                    $input['blogImage'] = $uploadedFileName;
                } else {
                    $uploadedFileName = 'na';
                }

                $blogLink = strtolower(str_replace(" ", "-", $input['title']));

                $blog = new Blog([
                    'category_id' => $input['category_id'],
                    'title' => $input['title'],
                    'description' => $input['description'],
                    'image' => $uploadedFileName,
                    'read_time' => $input['blogReadTm'],
                    'slug' => $blogLink,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $blog->save();
                $blogId = $blog->id;

                Blog::where('id', $blogId)->update(['edited_by' => Session::get('adminid'), 'updated_at' => date('Y-m-d H:i:s')]);

                Session::flash('success_message', "Blog Successfully Added.");
                return Redirect::to('admin/blogs');

                /* $serialisedData = $this->serialiseFormData($input, 1); //send 1 for edit
                  Page::where('id', $recordInfo->id)->update($serialisedData);
                  Session::flash('success_message', "Page details updated successfully.");
                  return Redirect::to('admin/pages'); */
            }
        }
        $category = Category::getCategoryList();
        //print_r($category); die;
        return view('admin.pages.addBlog', ['title' => $pageTitle, $activetab => 1,'category'=>$category]);
    }

    public function editBlog($slug = null) {
        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'edit-blog');
        if ($isPermitted == false) {
            $pageTitle = 'Not Permitted';
            $activetab = 'actchangeusername';
            return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
        }

        $pageTitle = 'Edit Blog';
        $activetab = 'actblogs';

        $recordInfo = Blog::where('id', $slug)->first();
        if (empty($recordInfo)) {
            return Redirect::to('admin/blogs');
        }

        $input = Input::all();
        if (!empty($input)) {
            $rules = array(
                'category_id' => 'required',
                'title' => 'required',
                'description' => 'required',
                'read_time' => 'required',
            );
            $validator = Validator::make($input, $rules);
            if ($validator->fails()) {
                return Redirect::to('/admin/blogs/edit/' . $slug)->withErrors($validator)->withInput();
            } else {
                if (Input::hasFile('blogImage')) {
                    $file = Input::file('blogImage');
                    $uploadedFileName = $this->uploadImage($file, BLOG_FULL_UPLOAD_PATH);
//                    $this->resizeImage($uploadedFileName, BLOG_FULL_UPLOAD_PATH, BLOG_SMALL_UPLOAD_PATH, BLOG_MW, BLOG_MH);
                    $input['blogImage'] = $uploadedFileName;
                    Blog::where('id', $slug)->update(['category_id' => $input['category_id'],'title' => $input['title'], 'description' => $input['description'], 'image' => $uploadedFileName, 'read_time' => $input['read_time'], 'edited_by' => Session::get('adminid'), 'updated_at' => date('Y-m-d H:i:s')]);
                } else {
                    Blog::where('id', $slug)->update(['category_id' => $input['category_id'],'title' => $input['title'], 'description' => $input['description'], 'read_time' => $input['read_time'], 'edited_by' => Session::get('adminid'), 'updated_at' => date('Y-m-d H:i:s')]);
                }

                Session::flash('success_message', "Blog details updated successfully.");
                return Redirect::to('admin/blogs');
            }
        }
        $category = Category::getCategoryList();
        return view('admin.pages.editBlog', ['title' => $pageTitle, $activetab => 1, 'recordInfo' => $recordInfo,'category'=>$category]);
    }

    public function deleteBlog($slug) {
        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'delete-blog');
        if ($isPermitted == false) {
            $pageTitle = 'Not Permitted';
            $activetab = 'actchangeusername';
            return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
        }

        if ($slug) {
            Blog::where('id', $slug)->delete();
            Session::flash('success_message', "Blog successfully removed.");
            return Redirect::to('admin/blogs');
        }
        return Redirect::to('admin/blogs');
    }

    public function faqList(Request $request) {
        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'list-faq');
        if ($isPermitted == false) {
            $pageTitle = 'Not Permitted';
            $activetab = 'actchangeusername';
            return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
        }

        $pageTitle = 'Frequently Asked Questions List';
        $activetab = 'actpagesfaq';
        $query = new Faq();
        $query = $query->sortable();


        $faqs = $query->orderBy('id', 'DESC')->paginate(20);
        if ($request->ajax()) {
            return view('elements.admin.pages.listFaq', ['allrecords' => $faqs]);
        }
        return view('admin.pages.faqList', ['title' => $pageTitle, $activetab => 1, 'allrecords' => $faqs]);
    }

    public function addFaq() {
        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'add-faq');
        if ($isPermitted == false) {
            $pageTitle = 'Not Permitted';
            $activetab = 'actchangeusername';
            return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
        }

        $pageTitle = 'Add Frequently Asked Question';
        $activetab = 'actpagesfaq';

        $input = Input::all();
        if (!empty($input)) {
            $rules = array(
                'ques' => 'required',
                'ans' => 'required',
                'sort_order' => 'required|numeric',
            );
            $customMessages = [
                'ques.required' => 'Question field can\'t be left blank.',
                'ans.required' => 'Answer field can\'t be left blank.',
                'sort_order.required' => 'Sort order field can\'t be left blank.',
                'sort_order.numeric' => 'Invalid Sort Order! Use number only.',
            ];
            $validator = Validator::make($input, $rules, $customMessages);
            if ($validator->fails()) {
                return Redirect::to('/admin/pages/addFaq')->withErrors($validator)->withInput();
            } else {

                $faq = new Faq([
                    'faq_ques' => $input['ques'],
                    'faq_ans' => $input['ans'],
                    'sort_order' => $input['sort_order'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $faq->save();
                $faqID = $faq->id;

                Faq::where('id', $faqID)->update(['edited_by' => Session::get('adminid')]);

                Session::flash('success_message', "Question added successfully.");
                return Redirect::to('admin/pages/faq-list');
            }
        }
        return view('admin.pages.addFaq', ['title' => $pageTitle, $activetab => 1]);
    }

    public function editFaq($slug) {
        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'edit-faq');
        if ($isPermitted == false) {
            $pageTitle = 'Not Permitted';
            $activetab = 'actchangeusername';
            return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
        }

        $pageTitle = 'Edit Frequently Asked Question';
        $activetab = 'actpagesfaq';

        $recordInfo = Faq::where('id', $slug)->first();
        if (empty($recordInfo)) {
            return Redirect::to('admin/pages/editFaq');
        }

        $input = Input::all();
        if (!empty($input)) {
            $rules = array(
                'faq_ques' => 'required',
                'faq_ans' => 'required',
                'sort_order' => 'required|numeric',
            );
            $customMessages = [
                'faq_ques.required' => 'Question field can\'t be left blank.',
                'faq_ans.required' => 'Answer field can\'t be left blank.',
                'sort_order.required' => 'Sort order field can\'t be left blank.',
                'sort_order.numeric' => 'Invalid Sort Order! Use number only.',
            ];
            $validator = Validator::make($input, $rules, $customMessages);
            if ($validator->fails()) {
                return Redirect::to('/admin/pages/editFaq/' . $slug)->withErrors($validator)->withInput();
            } else {

                Faq::where('id', $slug)->update(['faq_ques' => $input['faq_ques'], 'faq_ans' => $input['faq_ans'], 'sort_order' => $input['sort_order'], 'edited_by' => Session::get('adminid'), 'updated_at' => date('Y-m-d H:i:s')]);


                Session::flash('success_message', "FAQ details updated successfully.");
                return Redirect::to('admin/pages/faq-list');
            }
        }
        return view('admin.pages.editFaq', ['title' => $pageTitle, $activetab => 1, 'recordInfo' => $recordInfo]);
    }

    public function deleteFaq($slug) {
        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'delete-faq');
        if ($isPermitted == false) {
            $pageTitle = 'Not Permitted';
            $activetab = 'actchangeusername';
            return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
        }

        if ($slug) {
            Faq::where('id', $slug)->delete();
            Session::flash('success_message', "FAQ successfully removed from list.");
            return Redirect::to('admin/pages/faq-list');
        }
        return Redirect::to('admin/pages/faq-list');
    }

}

?>