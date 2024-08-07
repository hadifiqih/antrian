<?php

namespace App\Http\Controllers;

use App\Models\Sales;
use Illuminate\Http\Request;
use App\Models\SocialAccount;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Yajra\DataTables\Facades\DataTables;

class SocialAccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // $this->middleware('role:sales');
        // $this->middleware('role:marol');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('page.social-media.index');
    }

    public function indexJson()
    {
        $sales = auth()->user()->sales->id;
        $socialMediaAccounts = SocialAccount::with('sales')->where('sales_id', $sales)->get();

        return Datatables::of($socialMediaAccounts)
            ->addIndexColumn()
            ->addColumn('sales', function ($row) {
                return $row->sales->name;
            })
            ->addColumn('password', function ($row) {
                $password = $row->password;
                return '<div class="password-container">
                    <input type="password" value="'.$password.'" class="password-field form-control" disabled>
                    <i class="toggle-password copy-password fas fa-copy"></i>
                </div>';
            })
            ->addColumn('action', function ($row) {
                $actionBtn = '<div class="btn-group" role="group">
                    <a href="'.route('social.edit', $row->id).'" class="edit btn btn-primary btn-sm"><i class="fas fa-pen"></i></a>
                    <a href="#" data-id="'.$row->id.'" class="delete btn btn-danger btn-sm"><i class="fas fa-trash"></i></a>
                </div>';
                return $actionBtn;
            })
            ->rawColumns(['action', 'password'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $sales = Sales::all();
        return view('page.social-media.create', compact('sales'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'sales' => 'required|exists:sales,id',
            'platform' => 'required|in:Facebook,Instagram,YouTube,TikTok,Shopee,Tokopedia',
            'username' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:6',
        ]);

        try {
            // Create a new SocialMediaAccount instance
            $socialMediaAccount = new SocialAccount();
            $socialMediaAccount->sales_id = $validatedData['sales'];
            $socialMediaAccount->platform = $validatedData['platform'];
            $socialMediaAccount->username = $validatedData['username'];
            $socialMediaAccount->email = $validatedData['email'];
            $socialMediaAccount->phone = $validatedData['phone'];
            $socialMediaAccount->password = $validatedData['password']; // Encrypt the password

            // Save the new account
            $socialMediaAccount->save();

            // Return a success response
            return response()->json([
                'success' => true,
                'message' => 'Social media account created successfully',
                'data' => $socialMediaAccount
            ], 201);

        } catch (\Exception $e) {
            // Return an error response if something goes wrong
            return response()->json([
                'success' => false,
                'message' => 'Failed to create social media account',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $sosmed = SocialAccount::find($id);
        $sales = Sales::all();
        return view('page.social-media.edit', compact('sosmed', 'sales'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'sales' => 'required|exists:sales,id',
            'platform' => 'required|in:Facebook,Instagram,YouTube,TikTok,Shopee,Tokopedia',
            'username' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
        ]);

        try {
            // Find the SocialMediaAccount instance
            $socialMediaAccount = SocialAccount::find($id);
            $socialMediaAccount->sales_id = $validatedData['sales'];
            $socialMediaAccount->platform = $validatedData['platform'];
            $socialMediaAccount->username = $validatedData['username'];
            $socialMediaAccount->email = $validatedData['email'];
            $socialMediaAccount->phone = $validatedData['phone'];
            if($request->filled('password')) {
                $socialMediaAccount->password = $validatedData['password']; // Encrypt the password
            }

            // Save the updated account
            $socialMediaAccount->save();

            // Return a success response
            return response()->json([
                'success' => true,
                'message' => 'Social media account updated successfully',
                'data' => $socialMediaAccount
            ], 200);

        } catch (\Exception $e) {
            // Return an error response if something goes wrong
            return response()->json([
                'success' => false,
                'message' => 'Failed to update social media account',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            // Find the SocialMediaAccount instance
            $socialMediaAccount = SocialAccount::find($id);

            // Delete the account
            $socialMediaAccount->delete();

            // Return a success response
            return response()->json([
                'success' => true,
                'message' => 'Social media account deleted successfully',
                'data' => $socialMediaAccount
            ], 200);

        } catch (\Exception $e) {
            // Return an error response if something goes wrong
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete social media account',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
