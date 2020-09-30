<?php

namespace Manuel90\DummySettings\Http;

use Illuminate\Http\Request;

use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Testing\MimeType;

use Illuminate\Support\Facades\Auth;

use Intervention\Image\Facades\Image;

use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Models\Setting;

use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use Manuel90\DummySettings\DummySettings;


use App\Http\Controllers\Controller as BaseController;

class Controller extends BaseController
{

    public function __construct() {
        if( class_exists('Voyager') ) {
            $this->middleware('admin.user')->only('index');
        }
    }

    public function index(Request $request) {

        $list = DummySettings::getListAvailableSettings();
        if( class_exists('Voyager') ) {
            return view('dummysettings::indexvoyager',['listSettings' => $list]);
        }

        return view('dummysettings::index',['listSettings' => $list]);
    }

    public function saveGeneralSetting(Request $request) {
        try {
            
            $key = $request->input('setting', null);

            if( !auth()->user()->hasPermission('edit_settings') ) {
                throw new DummySettingsException( __('dummysettings::general.error_permission') );
            }

            if( !$key ) {
                throw new DummySettingsException( __('dummysettings::general.error_saving') );
            }

            $val = $request->input('valset', '');

            $setting = Setting::where('key', $key)->firstOrFail();

            if($setting->type !== 'text') {
                throw new DummySettingsException( __('dummysettings::general.error_permission') );
            }

            $setting->value = $val;

            if( !$setting->save() ) {
                throw new DummySettingsException( __('dummysettings::general.error_saving') );
            }

            return response()->json([
                'success' => true,
                'message' => __('dummysettings::general.setting_saved_successfully', ['name' => $setting->display_name]),
            ]);
        } catch (ModelNotFoundException $nt) {
            return response()->json([
                'success' => false,
                'message' => __('dummysettings::general.setting_not_found'),
            ]);
        } catch (DummySettingsException $de) {
            return response()->json([
                'success' => false,
                'message' => $de->getMessage(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('dummysettings::general.error_saving'),
            ]);
        }
    }
    
    
    public function assets(Request $request) {
        try {
            $path = $request->get('path','');
            if(!$path) {
                return response()->json(null,Response::HTTP_NOT_FOUND);
            }
            $pathToFile = __DIR__."/../../publishable/assets/$path";

            if( !file_exists($pathToFile) ) {
                return response()->json($pathToFile,Response::HTTP_NOT_FOUND);
            }
            
            $mimeType = MimeType::from(basename($pathToFile));

            return response()->file($pathToFile,array(
                'Content-Type' => $mimeType,
            ));
        } catch (\Exception $e) {
            return response()->json(null,Response::HTTP_NOT_FOUND);
        }
    }

    public function settings(Request $request) {
        try {
            $filterSettings = $request->get('only', null);
            if(!empty($filterSettings)) {
                $filterSettings = array_map(function($setting){
                    return trim("admin.$setting");
                },explode(',',$filterSettings));
                $settings = Setting::whereIn('key', $filterSettings)->get();
            } else {
                $settings = Setting::all();
            }

            $retunListSettings = [];
            foreach($settings as $setting) {
                $key = \str_replace(\strtolower($setting->group).'.','',$setting->key);
                $retunListSettings[$key] = $setting->value;
            }

            return response()->json($retunListSettings);
        } catch (\Exception $e) {
            throw new DummySettingsException($e->getMessage());
        }
      }

    
}


class DummySettingsException extends \Exception  {

}
