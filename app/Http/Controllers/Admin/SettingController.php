<?php

namespace App\Http\Controllers\Admin;

use DateTimeZone;
use App\Models\Config;
use App\Models\Setting;
use App\Models\Currency;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Theme;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;
use Spatie\ResponseCache\Facades\ResponseCache;

class SettingController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    // Settings
    public function index()
    {
        // Queries
        $timezonelist = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
        $themes = Theme::get();
        $currencies = Currency::get();
        $settings = Setting::first();
        $config = Config::get();

        // Get email configuration details
        $email_configuration = [
            'driver' => env('MAIL_MAILER', 'smtp'),
            'host' => env('MAIL_HOST', 'smtp.mailgun.org'),
            'port' => env('MAIL_PORT', 587),
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
            'encryption' => env('MAIL_ENCRYPTION', 'tls'),
            'address' => env('MAIL_FROM_ADDRESS'),
            'name' => env('MAIL_FROM_NAME', $settings->site_name),
        ];

        // Get google configuration details
        $google_configuration = [
            'GOOGLE_ENABLE' => env('GOOGLE_ENABLE', 'off'),
            'GOOGLE_CLIENT_ID' => env('GOOGLE_CLIENT_ID', ''),
            'GOOGLE_CLIENT_SECRET' => env('GOOGLE_CLIENT_SECRET', ''),
            'GOOGLE_REDIRECT' => env('GOOGLE_REDIRECT', '')
        ];

        // Get image limit
        $image_limit = [
            'SIZE_LIMIT' => env('SIZE_LIMIT', '')
        ];

        // Get Recaptcha configuration details
        $recaptcha_configuration = [
            'RECAPTCHA_ENABLE' => env('RECAPTCHA_ENABLE', 'off'),
            'RECAPTCHA_SITE_KEY' => env('RECAPTCHA_SITE_KEY', ''),
            'RECAPTCHA_SECRET_KEY' => env('RECAPTCHA_SECRET_KEY', '')
        ];

        $settings['email_configuration'] = $email_configuration;
        $settings['google_configuration'] = $google_configuration;
        $settings['recaptcha_configuration'] = $recaptcha_configuration;
        $settings['image_limit'] = $image_limit;

        return view('admin.pages.settings.index', compact('settings', 'themes', 'timezonelist', 'currencies', 'config'));
    }

    // Update General Setting
    public function changeGeneralSettings(Request $request)
    {
        Config::where('config_key', 'show_website')->update([
            'config_value' => $request->show_website,
        ]);

        Config::where('config_key', 'timezone')->update([
            'config_value' => $request->timezone,
        ]);

        Config::where('config_key', 'currency')->update([
            'config_value' => $request->currency,
        ]);

        Config::where('config_key', 'term')->update([
            'config_value' => $request->term,
        ]);

        Setting::where('id', '1')->update([
            'tawk_chat_key' => $request->tawk_chat_bot_key
        ]);

        // Set new values using putenv
        $this->updateEnvFile('TIMEZONE', $request->timezone);
        $this->updateEnvFile('COOKIE_CONSENT_ENABLED', $request->cookie);
        $this->updateEnvFile('SIZE_LIMIT', $request->image_limit);

        // Page redirect
        return redirect()->route('admin.settings')->with('success', trans('General Settings Updated Successfully!'));
    }

    // Update Website Setting
    public function changeWebsiteSettings(Request $request)
    {
        Setting::where('id', '1')->update([
            'site_name' => $request->site_name
        ]);

        Config::where('config_key', 'site_name')->update([
            'config_value' => $request->site_name
        ]);

        // App name
        $appName = str_replace('"', "", $request->app_name);
        $appName = str_replace("'", "", $appName);

        // Set new values using putenv
        $this->updateEnvFile('APP_NAME', '"'.$appName.'"');

        Config::where('config_key', 'app_theme')->update([
            'config_value' => $request->app_theme
        ]);

        Config::where('config_key', 'default_theme')->update([
            'config_value' => $request->theme_id
        ]);

        // Check website logo
        if (isset($request->site_logo)) {
            $validator = $request->validate([
                'site_logo' => 'mimes:jpeg,png,jpg,gif,svg|max:' . env("SIZE_LIMIT") . '',
            ]);

            $site_logo = '/images/web/elements/' . uniqid() . '.' . $request->site_logo->extension();
            $request->site_logo->move(public_path('images/web/elements'), $site_logo);

            // Update details
            Setting::where('id', '1')->update([
                'analytics_id' => $request->google_analytics_id, 'google_tag' => $request->google_tag, 'adsense_code' => $request->adsense_code,
                'site_name' => $request->site_name, 'site_logo' => $site_logo, 'tawk_chat_key' => $request->tawk_chat_bot_key,
            ]);
        }

        // Check favicon
        if (isset($request->favi_icon)) {
            $validator = $request->validate([
                'favi_icon' => 'mimes:jpeg,png,jpg,gif,svg|max:' . env("SIZE_LIMIT") . '',
            ]);

            $favi_icon = '/images/web/elements/' . uniqid() . '.' . $request->favi_icon->extension();
            $request->favi_icon->move(public_path('images/web/elements'), $favi_icon);

            // Update details
            Setting::where('id', '1')->update([
                'analytics_id' => $request->google_analytics_id, 'google_tag' => $request->google_tag, 'adsense_code' => $request->adsense_code,
                'site_name' => $request->site_name, 'favicon' => $favi_icon, 'tawk_chat_key' => $request->tawk_chat_bot_key,
            ]);
        }

        // Check primary image for website banner
        if (isset($request->primary_image)) {
            $validator = $request->validate([
                'primary_image' => 'mimes:jpeg,png,jpg,gif,svg|max:' . env("SIZE_LIMIT") . '',
            ]);

            $primary_image = '/images/web/elements/' . uniqid() . '.' . $request->primary_image->extension();
            $request->primary_image->move(public_path('/images/web/elements'), $primary_image);

            // Update image
            Config::where('config_key', 'primary_image')->update([
                'config_value' => $primary_image,
            ]);
        }

        // Page redirect
        return redirect()->route('admin.settings')->with('success', trans('Website Settings Updated Successfully!'));
    }

    // Update Payments Setting
    public function changePaymentsSettings(Request $request)
    {
        // Paypal
        Config::where('config_key', 'paypal_mode')->update([
            'config_value' => $request->paypal_mode
        ]);

        Config::where('config_key', 'paypal_client_id')->update([
            'config_value' => $request->paypal_client_key
        ]);

        Config::where('config_key', 'paypal_secret')->update([
            'config_value' => $request->paypal_secret
        ]);

        // Razorpay
        Config::where('config_key', 'razorpay_key')->update([
            'config_value' => $request->razorpay_client_key
        ]);

        Config::where('config_key', 'razorpay_secret')->update([
            'config_value' => $request->razorpay_secret
        ]);

        // Stripe
        Config::where('config_key', 'stripe_publishable_key')->update([
            'config_value' => $request->stripe_publishable_key
        ]);

        Config::where('config_key', 'stripe_secret')->update([
            'config_value' => $request->stripe_secret
        ]);

        // Paystack
        Config::where('config_key', 'paystack_public_key')->update([
            'config_value' => $request->paystack_public_key
        ]);

        Config::where('config_key', 'paystack_secret_key')->update([
            'config_value' => $request->paystack_secret
        ]);

        Config::where('config_key', 'merchant_email')->update([
            'config_value' => $request->merchant_email
        ]);

        // Mollie
        Config::where('config_key', 'mollie_key')->update([
            'config_value' => $request->mollie_key
        ]);

        // Transaction Cloud
        Config::where('config_key', 'transaction_cloud_api_key')->update([
            'config_value' => $request->transaction_cloud_login
        ]);

        Config::where('config_key', 'transaction_cloud_api_password')->update([
            'config_value' => $request->transaction_cloud_password
        ]);

        // Offline
        Config::where('config_key', 'bank_transfer')->update([
            'config_value' => $request->bank_transfer
        ]);

        // Page redirect
        return redirect()->route('admin.settings')->with('success', trans('Payment Settings Updated Successfully!'));
    }

    // Update Google Setting
    public function changeGoogleSettings(Request $request)
    {
        Setting::where('id', '1')->update([
            'analytics_id' => $request->google_analytics_id, 'google_tag' => $request->google_tag, 'adsense_code' => $request->adsense_code
        ]);

        // Set new values using putenv (google login)
        $this->updateEnvFile('GOOGLE_ENABLE', $request->google_auth_enable);
        $this->updateEnvFile('GOOGLE_CLIENT_ID', '"'.str_replace('"', "'", $request->google_client_id).'"');
        $this->updateEnvFile('GOOGLE_CLIENT_SECRET', '"'.str_replace('"', "'", $request->google_client_secret).'"');
        $this->updateEnvFile('GOOGLE_REDIRECT', '"'.str_replace('"', "'", $request->google_redirect).'"');

        // Set new values using putenv (google recaptcha)
        $this->updateEnvFile('RECAPTCHA_ENABLE', $request->recaptcha_enable);
        $this->updateEnvFile('RECAPTCHA_SITE_KEY', '"'.str_replace('"', "'", $request->recaptcha_site_key).'"');
        $this->updateEnvFile('RECAPTCHA_SECRET_KEY', '"'.str_replace('"', "'", $request->recaptcha_secret_key).'"');

        // Page redirect
        return redirect()->route('admin.settings')->with('success', trans('Google Settings Updated Successfully!'));
    }

    // Update Email Setting
    public function changeEmailSettings(Request $request)
    {
        // Mail username
        $mailDriver = str_replace('"', "", $request->mail_driver);
        $mailDriver = str_replace("'", "", $mailDriver);

        // Mail host
        $mailHost = str_replace('"', "", $request->mail_host);
        $mailHost = str_replace("'", "", $mailHost);

        // Mail port
        $mailPort = str_replace('"', "", $request->mail_port);
        $mailPort = str_replace("'", "", $mailPort);

        // Mail username
        $userName = str_replace('"', "", $request->mail_username);
        $userName = str_replace("'", "", $userName);

        // Mail password
        $password = str_replace('"', "", $request->mail_password);
        $password = str_replace("'", "", $password);

        // Mail password
        $mailEncryption = str_replace('"', "", $request->mail_encryption);
        $mailEncryption = str_replace("'", "", $mailEncryption);

        // Mail email
        $senderEmail = str_replace('"', "", $request->mail_address);
        $senderEmail = str_replace("'", "", $senderEmail);

        // Mail sender name
        $mailSenderName = str_replace('"', "", $request->mail_sender);
        $mailSenderName = str_replace("'", "", $mailSenderName);

        // Set new values using putenv (google login)
        $this->updateEnvFile('MAIL_DRIVER', $mailDriver);
        $this->updateEnvFile('MAIL_HOST', $mailHost);
        $this->updateEnvFile('MAIL_PORT', $mailPort);
        $this->updateEnvFile('MAIL_USERNAME', $userName);
        $this->updateEnvFile('MAIL_PASSWORD', $password);
        $this->updateEnvFile('MAIL_ENCRYPTION', $mailEncryption);
        $this->updateEnvFile('MAIL_FROM_ADDRESS', $senderEmail);
        $this->updateEnvFile('MAIL_FROM_NAME', '"'.$mailSenderName.'"');

        // Page redirect
        return redirect()->route('admin.settings')->with('success', trans('Email configuration settings updated successfully!'));
    }

    // Update AI Tools Setting
    public function changeAISettings(Request $request)
    {
        Config::where('config_key', 'openai_model')->update([
            'config_value' => $request->ai_model,
        ]);

        Config::where('config_key', 'image_model')->update([
            'config_value' => $request->image_model,
        ]);

        Config::where('config_key', 'text_speech_model')->update([
            'config_value' => $request->text_speech_model,
        ]);

        Config::where('config_key', 'openai_api_key')->update([
            'config_value' => $request->openai_api_key,
        ]);

        Config::where('config_key', 'share_content')->update([
            'config_value' => $request->word_length,
        ]);

        Config::where('config_key', 'image_length')->update([
            'config_value' => $request->image_length,
        ]);

        Config::where('config_key', 'tiny_api_key')->update([
            'config_value' => $request->tiny_api_key,
        ]);

        // Page redirect
        return redirect()->route('admin.settings')->with('success', trans('AI Settings Updated Successfully!'));
    }

    // Update S3 Setting
    public function changeS3Settings(Request $request)
    {
        // Access Key
        $awsAccessKey = str_replace('"', "", $request->access_key);
        $awsAccessKey = str_replace("'", "", $awsAccessKey);

        // Secret Key
        $awsSecretKey = str_replace('"', "", $request->secret_key);
        $awsSecretKey = str_replace("'", "", $awsSecretKey);

        // Region
        $awsDefaultRegion = str_replace('"', "", $request->default_region);
        $awsDefaultRegion = str_replace("'", "", $awsDefaultRegion);

        // Bucket
        $bucket = str_replace('"', "", $request->bucket);
        $bucket = str_replace("'", "", $bucket);

        // Set new values using putenv (AWS S3)
        $this->updateEnvFile('AWS_ENABLE', $request->aws_enable);
        $this->updateEnvFile('AWS_ACCESS_KEY_ID', $awsAccessKey);
        $this->updateEnvFile('AWS_SECRET_ACCESS_KEY', $awsSecretKey);
        $this->updateEnvFile('AWS_DEFAULT_REGION', $awsDefaultRegion);
        $this->updateEnvFile('AWS_BUCKET', $bucket);
        $this->updateEnvFile('AWS_USE_PATH_STYLE_ENDPOINT', $request->end_point);

        // Page redirect
        return redirect()->route('admin.settings')->with('success', trans('AWS configuration settings updated successfully!'));
    }

    // Tax settings
    public function taxSetting()
    {
        // Queries
        $config = Config::get();
        $settings = Setting::first();

        // Page view
        return view('admin.pages.tax.index', compact('config', 'settings'));
    }

    // Update tax setting
    public function updateTaxSetting(Request $request)
    {
        // Update
        Config::where('config_key', 'invoice_prefix')->update([
            'config_value' => $request->invoice_prefix,
        ]);

        Config::where('config_key', 'invoice_name')->update([
            'config_value' => $request->invoice_name,
        ]);

        Config::where('config_key', 'invoice_email')->update([
            'config_value' => $request->invoice_email,
        ]);

        Config::where('config_key', 'invoice_phone')->update([
            'config_value' => $request->invoice_phone,
        ]);

        Config::where('config_key', 'invoice_address')->update([
            'config_value' => $request->invoice_address,
        ]);

        Config::where('config_key', 'invoice_city')->update([
            'config_value' => $request->invoice_city,
        ]);

        Config::where('config_key', 'invoice_state')->update([
            'config_value' => $request->invoice_state,
        ]);

        Config::where('config_key', 'invoice_zipcode')->update([
            'config_value' => $request->invoice_zipcode,
        ]);

        Config::where('config_key', 'invoice_country')->update([
            'config_value' => $request->invoice_country,
        ]);

        Config::where('config_key', 'tax_name')->update([
            'config_value' => $request->tax_name,
        ]);

        Config::where('config_key', 'tax_number')->update([
            'config_value' => $request->tax_number,
        ]);

        Config::where('config_key', 'tax_value')->update([
            'config_value' => $request->tax_value,
        ]);

        Config::where('config_key', 'invoice_footer')->update([
            'config_value' => $request->invoice_footer,
        ]);

        // Page redirect
        return redirect()->route('admin.tax.setting')->with('success', trans('Invoice Setting Updated Successfully!'));
    }

    // Update email setting
    public function updateEmailSetting(Request $request)
    {
        // Update
        Config::where('config_key', 'email_heading')->update([
            'config_value' => $request->email_heading,
        ]);

        Config::where('config_key', 'email_footer')->update([
            'config_value' => $request->email_footer,
        ]);

        // Page redirect
        return redirect()->route('admin.tax.setting')->with('success', trans('Email Setting Updated Successfully!'));
    }

    // Clear cache
    public function clear()
    {
        // Laravel cache commend
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('view:clear');
        Artisan::call('route:clear');
        ResponseCache::clear();

        // Page redirect
        return redirect()->route('admin.settings')->with('success', trans('Application Cache Cleared Successfully!'));
    }

    // Test email
    public function testEmail()
    {
        $message = [
            'msg' => 'Test mail'
        ];
        $mail = false;
        try {
            Mail::to(ENV('MAIL_FROM_ADDRESS'))->send(new \App\Mail\TestMail($message));
            $mail = true;
        } catch (\Exception $e) {
            // Page redirect
            return redirect()->route('admin.settings')->with('failed', trans('Email configuration wrong.'));
        }
        // Check email
        if ($mail == true) {
            // Page redirect
            return redirect()->route('admin.settings')->with('success', trans('Test mail send successfully.'));
        }
    }

    // Change .env file
    public function updateEnvFile($key, $value)
    {
        $envPath = base_path('.env');

        // Check if the .env file exists
        if (file_exists($envPath)) {

            // Read the .env file
            $contentArray = file($envPath);

            // Loop through each line to find the key and update its value
            foreach ($contentArray as &$line) {

                // Split the line by '=' to get key and value
                $parts = explode('=', $line, 2);

                // Check if the key matches and update its value
                if (isset($parts[0]) && $parts[0] === $key) {
                    $line = $key . '=' . $value . PHP_EOL;
                }
            }

            // Implode the array back to a string and write it to the .env file
            $newContent = implode('', $contentArray);
            file_put_contents($envPath, $newContent);

            // Reload the environment variables
            putenv($key . '=' . $value);
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }
}
