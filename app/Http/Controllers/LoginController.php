<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Employee;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use App\Mail\TwoFactorOTP;
use App\Mail\PasswordResetMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('login');
    }

    public function showRegisterForm()
    {
        return view('register');
    }

    public function loginadmin(Request $request)
    {
        $employee = Employee::where('email', $request->email)->first();
        if ($employee) {
            // Check if password is hashed or plain text (for backward compatibility)
            $passwordMatch = false;
            if (Hash::needsRehash($employee->password) || strlen($employee->password) < 60) {
                // Plain text password (old data)
                $passwordMatch = ($employee->password == $request->password);
            } else {
                // Hashed password
                $passwordMatch = Hash::check($request->password, $employee->password);
            }
            
            if ($passwordMatch) {
                Session::put('user', $employee);
                if ($employee->role === 'gudang') {
                    return redirect('/admin/dashboard-gudang');
                }
                if ($employee->role === 'driver') {
                    return redirect('/admin/dashboard-driver');
                }
                return redirect('/admin');
            } else {
                return back()->with('error', 'Password yang Anda masukkan salah. Silakan coba lagi.');
            }
        }

        //login customer
        $credentials = $request->only('email', 'password');

        $customer = Customer::where('email', $credentials['email'])->first();

        if (!$customer) {
            return back()->withErrors(['email' => 'Email tidak terdaftar. Silakan periksa kembali email Anda atau daftar akun baru.'])->withInput($request->only('email'));
        }

        // Check if password is hashed or plain text (for backward compatibility)
        $passwordMatch = false;
        if (Hash::needsRehash($customer->password) || strlen($customer->password) < 60) {
            // Plain text password (old data)
            $passwordMatch = ($credentials['password'] === $customer->password);
            // Auto-upgrade to hashed password
            if ($passwordMatch) {
                $customer->password = Hash::make($credentials['password']);
                $customer->save();
            }
        } else {
            // Hashed password
            $passwordMatch = Hash::check($credentials['password'], $customer->password);
        }

        if ($passwordMatch) {
            // Login langsung tanpa 2FA
            session([
                'user' => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'role' => 'customer',
                ],
                'isLoggedIn' => true,
                'customer_id' => $customer->id,
                'customer_address' => $customer->address ?? '',
            ]);
            return redirect('/produk');
        } else {
            return back()->withErrors(['password' => 'Password yang Anda masukkan salah. Silakan coba lagi.'])->withInput($request->only('email'));
        }
    }

    public function logout(Request $request)
    {
        Session::flush();
        return redirect('/login');
    }

    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:customers,email',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:6',
            'password_confirmation' => 'required|same:password',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'gender' => 'nullable|in:male,female',
        ]);

        // Generate OTP for 2FA verification BEFORE creating account
        $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $otpHash = Hash::make($otp);

        // Store registration data in session (temporary, not saved to DB yet)
        Session::put('register_data', [
            'email' => $request->email,
            'name' => $request->name,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'address' => $request->address,
            'city' => $request->city,
            'province' => $request->province,
            'postal_code' => $request->postal_code,
            'gender' => $request->gender,
            'otp_hash' => $otpHash,
            'otp_expires_at' => Carbon::now()->addMinutes(10)->toDateTimeString(),
        ]);

        // Send OTP via email
        try {
            Mail::to($request->email)->send(new TwoFactorOTP($otp, $request->name));
        } catch (\Exception $e) {
            \Log::error('Failed to send 2FA OTP email: ' . $e->getMessage());
            Session::forget('register_data');
            return back()->with('error', 'Gagal mengirim email verifikasi. Silakan coba lagi.')->withInput();
        }

        return redirect('/verify-2fa')->with('success', 'Kode verifikasi telah dikirim ke email Anda. Silakan masukkan kode untuk menyelesaikan registrasi.');
    }

    public function showVerify2FA()
    {
        // Check if this is for registration or login
        $registerData = Session::get('register_data');
        $customerId = Session::get('2fa_customer_id');
        
        if (!$registerData && !$customerId) {
            return redirect('/login')->with('error', 'Sesi verifikasi tidak valid. Silakan mulai dari awal.');
        }

        return view('verify-2fa');
    }

    public function verify2FA(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        // Check if this is registration flow
        $registerData = Session::get('register_data');
        
        if ($registerData) {
            // Registration flow: verify OTP then create account
            // Check if OTP is expired
            if (Carbon::now()->gt(Carbon::parse($registerData['otp_expires_at']))) {
                Session::forget('register_data');
                return redirect('/register')->with('error', 'Kode OTP telah kedaluwarsa. Silakan daftar kembali.');
            }

            // Verify OTP
            if (!Hash::check($request->otp, $registerData['otp_hash'])) {
                return back()->withErrors(['otp' => 'Kode OTP yang Anda masukkan salah. Silakan coba lagi.']);
            }

            // OTP verified, now create the account
            $customer = new Customer();
            $customer->email = $registerData['email'];
            $customer->name = $registerData['name'];
            $customer->phone = $registerData['phone'];
            $customer->password = $registerData['password'];
            $customer->address = $registerData['address'];
            $customer->city = $registerData['city'];
            $customer->province = $registerData['province'];
            $customer->postal_code = $registerData['postal_code'];
            $customer->gender = $registerData['gender'];
            $customer->two_factor_enabled = true;
            $customer->save();

            // Clear registration data
            Session::forget('register_data');

            // Set session
            session([
                'user' => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'role' => 'customer',
                ],
                'isLoggedIn' => true,
                'customer_id' => $customer->id,
                'customer_address' => $customer->address ?? '',
            ]);

            return redirect('/')->with('success', 'Registrasi berhasil! Selamat datang di CHASTE.');
        }

        // This should not happen for login (login doesn't use 2FA anymore)
        // But keep this for backward compatibility
        $customerId = Session::get('2fa_customer_id');
        $email = Session::get('2fa_email');

        if (!$customerId || !$email) {
            return redirect('/login')->with('error', 'Sesi verifikasi tidak valid.');
        }

        $customer = Customer::find($customerId);
        if (!$customer || $customer->email !== $email) {
            Session::forget(['2fa_customer_id', '2fa_email']);
            return redirect('/login')->with('error', 'Data tidak valid.');
        }

        // Set session
        session([
            'user' => [
                'id' => $customer->id,
                'name' => $customer->name,
                'email' => $customer->email,
                'role' => 'customer',
            ],
            'isLoggedIn' => true,
            'customer_id' => $customer->id,
            'customer_address' => $customer->address ?? '',
        ]);

        Session::forget(['2fa_customer_id', '2fa_email']);
        return redirect('/produk')->with('success', 'Login berhasil! Selamat datang kembali.');
    }

    public function resendOTP()
    {
        // Check if this is registration flow
        $registerData = Session::get('register_data');
        
        if ($registerData) {
            // Registration flow: resend OTP
            $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $registerData['otp_hash'] = Hash::make($otp);
            $registerData['otp_expires_at'] = Carbon::now()->addMinutes(10)->toDateTimeString();
            Session::put('register_data', $registerData);

            // Send OTP via email
            try {
                Mail::to($registerData['email'])->send(new TwoFactorOTP($otp, $registerData['name']));
                return back()->with('success', 'Kode OTP baru telah dikirim ke email Anda.');
            } catch (\Exception $e) {
                \Log::error('Failed to resend 2FA OTP email: ' . $e->getMessage());
                return back()->with('error', 'Gagal mengirim email. Silakan coba lagi.');
            }
        }

        // Legacy login flow (should not happen, but keep for compatibility)
        $customerId = Session::get('2fa_customer_id');
        $email = Session::get('2fa_email');

        if (!$customerId || !$email) {
            return redirect('/login')->with('error', 'Sesi verifikasi tidak valid.');
        }

        $customer = Customer::find($customerId);
        if (!$customer || $customer->email !== $email) {
            return redirect('/login')->with('error', 'Data tidak valid.');
        }

        // Generate new OTP
        $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $customer->two_factor_code = Hash::make($otp);
        $customer->two_factor_expires_at = Carbon::now()->addMinutes(10);
        $customer->save();

        // Send OTP via email
        try {
            Mail::to($customer->email)->send(new TwoFactorOTP($otp, $customer->name));
            return back()->with('success', 'Kode OTP baru telah dikirim ke email Anda.');
        } catch (\Exception $e) {
            \Log::error('Failed to resend 2FA OTP email: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengirim email. Silakan coba lagi.');
        }
    }

    public function showForgotPassword()
    {
        return view('forgot-password');
    }

    public function sendPasswordReset(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $customer = Customer::where('email', $request->email)->first();

        if (!$customer) {
            // Don't reveal if email exists for security
            return back()->with('success', 'Jika email terdaftar, kode verifikasi telah dikirim ke email Anda.');
        }

        // Generate OTP for 2FA verification
        $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $customer->two_factor_code = Hash::make($otp);
        $customer->two_factor_expires_at = Carbon::now()->addMinutes(10);
        $customer->save();

        // Send OTP via email
        try {
            Mail::to($customer->email)->send(new TwoFactorOTP($otp, $customer->name));
            
            // Store email in session for password reset flow
            Session::put('reset_password_email', $customer->email);
            
            return redirect('/verify-reset-password')->with('success', 'Kode verifikasi telah dikirim ke email Anda. Silakan masukkan kode untuk melanjutkan.');
        } catch (\Exception $e) {
            \Log::error('Failed to send 2FA OTP email: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengirim email. Silakan coba lagi.');
        }
    }

    /**
     * Show 2FA verification page for password reset
     */
    public function showVerifyResetPassword()
    {
        $email = Session::get('reset_password_email');
        
        if (!$email) {
            return redirect('/forgot-password')->with('error', 'Sesi verifikasi tidak valid. Silakan mulai dari awal.');
        }

        return view('verify-reset-password', ['email' => $email]);
    }

    /**
     * Verify OTP for password reset
     */
    public function verifyResetPasswordOTP(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $email = Session::get('reset_password_email');
        
        if (!$email) {
            return redirect('/forgot-password')->with('error', 'Sesi verifikasi tidak valid.');
        }

        $customer = Customer::where('email', $email)->first();

        if (!$customer) {
            Session::forget('reset_password_email');
            return redirect('/forgot-password')->with('error', 'Email tidak ditemukan.');
        }

        // Check if OTP is expired
        if (!$customer->two_factor_expires_at || Carbon::now()->gt($customer->two_factor_expires_at)) {
            Session::forget('reset_password_email');
            return back()->withErrors(['otp' => 'Kode OTP telah kedaluwarsa. Silakan request kode baru.']);
        }

        // Verify OTP
        if (!Hash::check($request->otp, $customer->two_factor_code)) {
            return back()->withErrors(['otp' => 'Kode OTP yang Anda masukkan salah. Silakan coba lagi.']);
        }

        // Clear OTP
        $customer->two_factor_code = null;
        $customer->two_factor_expires_at = null;
        $customer->save();

        // Generate reset token
        $token = Str::random(64);

        // Delete existing tokens for this email
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        // Insert new token
        DB::table('password_reset_tokens')->insert([
            'email' => $email,
            'token' => Hash::make($token),
            'created_at' => Carbon::now(),
        ]);

        // Send reset email
        try {
            Mail::to($customer->email)->send(new PasswordResetMail($token, $customer->name, $customer->email));
            
            // Clear session
            Session::forget('reset_password_email');
            
            return redirect('/forgot-password')->with('success', 'Link reset password telah dikirim ke email Anda. Silakan cek email Anda.');
        } catch (\Exception $e) {
            \Log::error('Failed to send password reset email: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengirim email reset password. Silakan coba lagi.');
        }
    }

    /**
     * Resend OTP for password reset
     */
    public function resendResetPasswordOTP()
    {
        $email = Session::get('reset_password_email');
        
        if (!$email) {
            return redirect('/forgot-password')->with('error', 'Sesi verifikasi tidak valid.');
        }

        $customer = Customer::where('email', $email)->first();

        if (!$customer) {
            return redirect('/forgot-password')->with('error', 'Email tidak ditemukan.');
        }

        // Generate new OTP
        $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $customer->two_factor_code = Hash::make($otp);
        $customer->two_factor_expires_at = Carbon::now()->addMinutes(10);
        $customer->save();

        // Send OTP via email
        try {
            Mail::to($customer->email)->send(new TwoFactorOTP($otp, $customer->name));
            return back()->with('success', 'Kode OTP baru telah dikirim ke email Anda.');
        } catch (\Exception $e) {
            \Log::error('Failed to resend 2FA OTP email: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengirim email. Silakan coba lagi.');
        }
    }

    public function showResetPassword(Request $request)
    {
        $token = $request->query('token');
        $email = $request->query('email');

        if (!$token || !$email) {
            return redirect('/login')->with('error', 'Link reset password tidak valid.');
        }

        // Verify token
        $resetToken = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$resetToken) {
            return redirect('/login')->with('error', 'Link reset password tidak valid atau telah kedaluwarsa.');
        }

        // Check if token is expired (60 minutes)
        if (Carbon::parse($resetToken->created_at)->addMinutes(60)->lt(Carbon::now())) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            return redirect('/login')->with('error', 'Link reset password telah kedaluwarsa. Silakan request link baru.');
        }

        // Verify token hash
        if (!Hash::check($token, $resetToken->token)) {
            return redirect('/login')->with('error', 'Link reset password tidak valid.');
        }

        return view('reset-password', ['token' => $token, 'email' => $email]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:6',
            'password_confirmation' => 'required|same:password',
        ]);

        // Verify token
        $resetToken = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$resetToken) {
            return back()->withErrors(['email' => 'Link reset password tidak valid atau telah kedaluwarsa.'])->withInput();
        }

        // Check if token is expired (60 minutes)
        if (Carbon::parse($resetToken->created_at)->addMinutes(60)->lt(Carbon::now())) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors(['email' => 'Link reset password telah kedaluwarsa. Silakan request link baru.'])->withInput();
        }

        // Verify token hash
        if (!Hash::check($request->token, $resetToken->token)) {
            return back()->withErrors(['email' => 'Link reset password tidak valid.'])->withInput();
        }

        // Update password
        $customer = Customer::where('email', $request->email)->first();
        if (!$customer) {
            return back()->withErrors(['email' => 'Email tidak ditemukan.'])->withInput();
        }

        $customer->password = Hash::make($request->password);
        $customer->save();

        // Delete token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect('/login')->with('success', 'Password berhasil direset. Silakan login dengan password baru Anda.');
    }
}
