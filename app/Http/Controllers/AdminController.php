<?php

namespace App\Http\Controllers;

use App\Tag;
use App\Category;
use App\User;
use App\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    // tags
    public function getTag() {
        return Tag::orderBy('id', 'desc')->get();
    }

    public function addTag(Request $request) {
        $this->validate($request, [
            'tagName' => 'required'
        ]);
        return Tag::create([
            'tagName' => $request->tagName
        ]);
    }

    public function editTag(Request $request)
    {
        $this->validate($request, [
            'tagName' => 'required',
            'id' => 'required'
        ]);
        return Tag::where('id', $request->id)->update([
            'tagName' => $request->tagName
        ]);
    }

    public function deleteTag(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
        ]);
        return Tag::where('id', $request->id)->delete();
    }

    // upload file
    public function upload(Request $request) {
        $this->validate($request, [
            'file' => 'required|mimes:jpeg,jpg,png',
        ]);
        $picName = time() . '.' . $request->file->extension();
        $request->file->move(public_path('uploads'), $picName);
        return $picName;
    }

    public function deleteImage(Request $request)
    {
        $fileName = $request->imageName;
        $this->deleteFileFromServer($fileName, false);
        return 'done';
    }
    
    public function deleteFileFromServer($fileName, $hasFullPath = false)
    {
        if (!$hasFullPath) {
            $filePath = public_path() . '/uploads/' . $fileName;
        }
        if (file_exists($filePath)) {
            @unlink($filePath);
        }
        return;
    }

    // category
    public function getCategory()
    {
        return Category::orderBy('id', 'desc')->get();
    }

    public function addCategory(Request $request)
    {
        $this->validate($request, [
            'categoryName' => 'required',
            'iconImage' => 'required',
        ]);
        return Category::create([
            'categoryName' => $request->categoryName,
            'iconImage' => $request->iconImage,
        ]);
    }

    public function editCategory(Request $request)
    {
        $this->validate($request, [
            'categoryName' => 'required',
            'iconImage' => 'required',
        ]);
        return Category::where('id', $request->id)->update([
            'categoryName' => $request->categoryName,
            'iconImage' => $request->iconImage,
        ]);
    }

    public function deleteCategory(Request $request)
    {
        $this->deleteFileFromServer($request->iconImage);
        $this->validate($request, [
            'id' => 'required',
        ]);
        return Category::where('id', $request->id)->delete();
    }

    // admin
    public function getUsers()
    {
        // return User::where('userType', '!=', 'User')->get();
        return User::get();
    }

    public function createUser(Request $request)
    {
        $this->validate($request, [
            'fullName' => 'required',
            'email' => 'bail|required|email|unique:users',
            'password' => 'bail|required|min:6',
            // 'userType' => 'required',
            'role_id' => 'required',
        ]);
        $password = bcrypt($request->password);
        $user = User::create([
            'fullName' => $request->fullName,
            'email' => $request->email,
            'password' => $password,
            // 'userType' => $request->userType,
            'role_id' => $request->role_id,
        ]);
        return $user;
    }

    public function editUser(Request $request)
    {
        $this->validate($request, [
            'fullName' => 'required',
            'email' => "bail|required|email|unique:users,email,$request->id",
            'password' => 'min:6',
            // 'userType' => 'required',
            'role_id' => 'required',
        ]);
        $data = [
            'fullName' => $request->fullName,
            'email' => $request->email,
            // 'userType' => $request->userType,
            'role_id' => $request->role_id,
        ];
        if ($request->password) {
            $password = bcrypt($request->password);
            $data['password'] = $password;
        }
        $user = User::where('id', $request->id)->update($data);
        return $user;
    }

    // login
    public function adminLogin(Request $request)
    {
        $this->validate($request, [
            'email' => 'bail|required|email',
            'password' => 'bail|required|min:6',
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            // if ($user->userType == 'User') {
            //     Auth::logout();
            //     return response()->json([
            //         'msg' => 'Incorrect login details',
            //     ], 401);
            // }
            if ($user->role->isAdmin == 0) {
                Auth::logout();
                return response()->json([
                    'msg' => 'Incorrect login details',
                ], 401);
            }
            return response()->json([
                'msg' => 'You are logged in',
                'user' => $user,
            ]);
        } else {
            return response()->json([
                'msg' => 'Incorrect login details',
            ], 401);
        }
    }

    public function index(Request $request)
    {
        // first check if you are logged in and admin user ...
        if (!Auth::check() && $request->path() != 'login') {
            return redirect('/login');
        }
        if (!Auth::check() && $request->path() == 'login') {
            return view('welcome');
        }

        // you are already logged in... so check for if you are an admin user..
        $user = Auth::user();
        if ($user->userType == 'User') {
            return redirect('/login');
        }
        if ($request->path() == 'login') {
            return redirect('/');
        }

        // return view('welcome');

        return $this->checkForPermission($user, $request);
    }

    public function checkForPermission($user, $request)
    {
        $permissions = json_decode($user->role->permission);
        $hasPermission = false;
        
        if (!$permissions) {
            return view('welcome');
        }

        foreach ($permissions as $permission) {
            if ($permission->name == $request->path()) {
                if ($permission->read) {
                    $hasPermission = true;
                }
            }
        }

        if ($hasPermission) {
            return view('welcome');
        }

        return view('notFound');
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }

    // role
    public function getRoles()
    {
        return Role::all();
    }

    public function addRole(Request $request)
    {
        $this->validate($request, [
            'roleName' => 'required',
        ]);
        return Role::create([
            'roleName' => $request->roleName,
        ]);
    }

    public function editRole(Request $request)
    {
        $this->validate($request, [
            'roleName' => 'required',
        ]);
        return Role::where('id', $request->id)->update([
            'roleName' => $request->roleName,
        ]);
    }

    public function assignRole(Request $request)
    {
        $this->validate($request, [
            'permission' => 'required',
            'id' => 'required',
        ]);
        return Role::where('id', $request->id)->update([
            'permission' => $request->permission,
        ]);
    }

    // blog
    public function uploadEditorImage(Request $request)
    {
        $this->validate($request, [
            'image' => 'required|mimes:jpeg,jpg,png',
        ]);
        $picName = time() . '.' . $request->image->extension();
        $request->image->move(public_path('uploads'), $picName);
        return response()->json([
            'success' => 1,
            'file' => [
                'url' => "http://127.0.0.1:8000/uploads/$picName",
            ],
        ]);
        return $picName;
    }

    public function createBlog(Request $request)
    {
        
    }

}
