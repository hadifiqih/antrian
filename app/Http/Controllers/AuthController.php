<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Employee;
use App\Models\Sales;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cookie;


class AuthController extends Controller
{
    public function __construct()
    {
        if(Auth::viaRemember()){
            return view('page.dashboard');
        }
    }
    //Menampilkan halaman login
    public function index() {
        return view('auth.login');
    }

    public function create()
    {
        $sales = Sales::all();
        return view('auth.register', compact('sales'));
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        // Cek apakah email dan password benar
        if (Auth::attempt($credentials)) {
            // Mengambil data pengguna
            $user = Auth::user();

            // Cek apakah akun pengguna aktif
            if ($user->employee->is_active == 0) {
                // Logout pengguna yang tidak aktif
                Auth::logout();
                return redirect()->route('auth.login')->with('error', 'Akun anda telah di non-aktifkan !');
            } else if ($user->employee->is_active == 1) {
                // Menyimpan data pengguna ke dalam session
                $request->session()->put('user', $user);

                if ($remember) {
                    $cookie = Cookie::make('user', $user, 1440);
                    return view('page.dashboard')->withCookie($cookie);
                }

                // Check if user already has an active token
                $existingToken = $user->tokens()->where('name', 'api-token')->first();

                if ($existingToken) {
                    // Use the existing token
                    $token = $existingToken->plainTextToken;
                } else {
                    // Generate new token
                    $token = $user->createToken('api-token')->plainTextToken;
                }

                // Jika email dan password benar
                return view('page.dashboard', ['token' => $token]);
            }
        }

        // Jika email dan password salah
        return redirect()->route('auth.login')->with('error', 'Email atau password salah!');
    }

    public function logout(){
        //logout user
        Auth::logout();

        //kembalikan ke halaman login
        return redirect()->route('auth.login')->with('message', 'Logout berhasil !');
    }

    public function store(Request $request)
    {
        // Membuat rules validasi
        $rules = [
            'nama' => 'required|min:5|max:50',
            'email' => 'required|email|unique:users',
            'telepon' => 'required|min:10|max:13',
            'password' => 'required|min:8|max:35',
            'tahunMasuk' => 'required',
            'divisi' => 'required',
            'lokasi' => 'required',
            'terms' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->route('auth.register')->withErrors($validator)->withInput();
        }

        // Menentukan role
        $roles = ['roleProduksi', 'roleSales', 'roleDesain', 'roleKeuangan', 'roleLogistik', 'roleManajemen'];
        $role = null;
        foreach ($roles as $r) {
            if ($request->$r) {
                $role = $request->$r;
                break;
            }
        }

        // Menentukan lokasi kerja
        $lokasiMapping = [
            "Surabaya" => "1",
            "Malang" => "2",
            "Kediri" => "3",
            "Sidoarjo" => "4"
        ];
        $tempatKerja = $lokasiMapping[$request->lokasi] ?? null;

        // Tahun masuk
        $tahunMasuk = substr($request->tahunMasuk, -2);

        // Membuat user baru
        $user = User::create([
            'name' => ucwords(strtolower($request->nama)),
            'email' => $request->email,
            'phone' => $request->telepon,
            'password' => bcrypt($request->password),
            'role_id' => $role,
            'divisi' => $request->divisi
        ]);

        // Membuat NIP baru
        $nip = $tempatKerja . $tahunMasuk . $user->id;

        // Membuat employee baru
        Employee::create([
            'nip' => $nip,
            'name' => ucwords(strtolower($request->nama)),
            'email' => $request->email,
            'phone' => $request->telepon,
            'division' => ucwords($request->divisi),
            'office' => $request->lokasi,
            'user_id' => $user->id
        ]);

        // Mengubah user_id pada tabel sales jika roleSales ada
        if ($request->roleSales) {
            Sales::where('id', $request->salesApa)->update(['user_id' => $user->id]);
        }

        // Jika user berhasil dibuat
        return redirect()->route('auth.login')->with('success-register', 'Registrasi berhasil, silahkan login');
    }

    public function generateToken()
    {
        $beamsClient = new \Pusher\PushNotifications\PushNotifications(array(
            "instanceId" => "0958376f-0b36-4f59-adae-c1e55ff3b848",
            "secretKey" => "9F1455F4576C09A1DE06CBD4E9B3804F9184EF91978F3A9A92D7AD4B71656109",
        ));

        $userId = "user-" . Auth::user()->id;
        $token = $beamsClient->generateToken($userId);

        $user = User::find(Auth::user()->id);
        $user->beams_token = $token;
        $user->save();

        //Return the token to the client
        return response()->json($token);
    }

}
