<?php

namespace App\Http\Controllers;

use App\Models\LibRegion;
use App\Models\LibClubName;
use App\Models\GlobalKeyword;
use App\Models\LibHelp;
use App\Models\LibPosition;
use App\Models\LibRegionalPosition;
use App\Models\LibNationalOfficer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class LibraryController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            function ($request, $next) {
                $user = auth()->user();
                
                // Map methods to required actions
                $method = $request->route()->getActionMethod();
                $action = 'view'; // default
                
                if (str_starts_with($method, 'store')) $action = 'add';
                if (str_starts_with($method, 'update') || str_starts_with($method, 'edit') || str_starts_with($method, 'assign')) $action = 'edit';
                if (str_starts_with($method, 'destroy')) $action = 'delete';
                
                abort_if(!$user->hasPermission('libraries', $action), 403, 'Unauthorized action.');
                
                return $next($request);
            }
        ];
    }
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'regions');
        $regions = LibRegion::all();
        $clubs = LibClubName::with('region')->get();
        $global_keywords = GlobalKeyword::with('creator')->orderBy('desc')->get();
        $help_types = LibHelp::all();
        $positions = LibPosition::all();
        $regional_positions = LibRegionalPosition::all();
        $national_officers = LibNationalOfficer::orderBy('id')->get();
        
        return view('libraries.index', compact('regions', 'clubs', 'global_keywords', 'help_types', 'positions', 'regional_positions', 'national_officers', 'tab'));
    }

    public function updateGlobalKeyword(Request $request)
    {
        abort_unless(auth()->user()?->is_admin, 403, 'Unauthorized action.');

        $request->validate([
            'keywords' => 'nullable|array',
            'keywords.*' => 'nullable|string|max:255',
        ]);

        foreach ((array) $request->input('keywords', []) as $desc => $value) {
            $data = ['desc' => $desc, 'created_by' => auth()->id()];
            $data['keyword'] = $value ?: ($desc === 'agila_help' ? 'ALALAYANG-AGILA-TFOE-PE-2026' : null);

            GlobalKeyword::updateOrCreate(['desc' => $desc], $data);
        }

        return redirect()->route('libraries.index', ['tab' => 'global'])->with('status', 'Global keywords updated successfully!');
    }

    // Region CRUD
    public function storeRegion(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        LibRegion::create($request->only('name'));
        return redirect()->route('libraries.index', ['tab' => 'regions'])->with('status', 'Region added successfully!');
    }

    public function editRegion(LibRegion $region)
    {
        $regional_positions = LibRegionalPosition::all();
        $available_users = User::with('club')->where('status', 1)->whereNull('lib_regional_position_id')->orderBy('last_name')->get();
        // Also need to fetch the users who are ALREADY assigned to this region's positions to include them in the dropdowns.
        $current_officers = User::with('club')->where('lib_region_id', $region->id)->whereNotNull('lib_regional_position_id')->get();
        
        return view('libraries.regions.edit', compact('region', 'regional_positions', 'available_users', 'current_officers'));
    }

    public function updateRegion(Request $request, LibRegion $region)
    {
        $user = auth()->user();
        abort_if(!$user, 403, 'Unauthorized action.');
        abort_if(!$user->is_admin && $request->has('notification_keyword'), 403, 'Unauthorized action.');

        $request->validate([
            'name' => 'required|string|max:255',
            'notification_keyword' => 'nullable|string|max:100',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp'
        ]);

        $data = $request->only('name');
        if ($user->is_admin) {
            $data['notification_keyword'] = $request->input('notification_keyword');
        }

        if ($request->hasFile('logo')) {
            if ($region->logo) {
                Storage::disk('public')->delete($region->logo);
            }
            $data['logo'] = $this->resizeAndStoreLogo($request->file('logo'));
        }

        $region->update($data);

        return redirect()->route('libraries.index', ['tab' => 'regions'])->with('status', 'Region Name & Logo updated successfully!');
    }

    public function assignOfficer(Request $request, LibRegion $region)
    {
        $request->validate([
            'position_id' => 'required|exists:lib_regional_positions,id',
            'user_id' => 'nullable|exists:users,id'
        ]);

        // Remove the current user assigned to this region for this specific position
        User::where('lib_region_id', $region->id)
            ->where('lib_regional_position_id', $request->position_id)
            ->update(['lib_regional_position_id' => null]);

        if ($request->user_id) {
            // Assign the new user to this position for the region
            User::where('id', $request->user_id)->update([
                'lib_regional_position_id' => $request->position_id,
                'lib_region_id' => $region->id
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function destroyRegion(LibRegion $region)
    {
        $region->delete();
        return redirect()->route('libraries.index', ['tab' => 'regions'])->with('status', 'Region deleted successfully!');
    }

    // Club CRUD
    public function storeClub(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'lib_region_id' => 'required|exists:lib_region,id'
        ]);
        LibClubName::create($request->only('name', 'lib_region_id'));
        return redirect()->route('libraries.index', ['tab' => 'clubs'])->with('status', 'Club added successfully!');
    }

    public function updateClub(Request $request, LibClubName $club)
    {
        $user = auth()->user();
        abort_if(!$user, 403, 'Unauthorized action.');
        abort_if(!$user->is_admin && $request->has('notification_keyword'), 403, 'Unauthorized action.');

        $request->validate([
            'name' => 'required|string|max:255',
            'lib_region_id' => 'required|exists:lib_region,id',
            'notification_keyword' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp'
        ]);
        
        $data = $request->only('name', 'lib_region_id');
        if ($user->is_admin) {
            $data['notification_keyword'] = $request->input('notification_keyword');
        }

        if ($request->hasFile('logo')) {
            if ($club->logo) {
                Storage::disk('public')->delete($club->logo);
            }
            $data['logo'] = $this->resizeAndStoreLogo($request->file('logo'));
        }

        $club->update($data);
        return redirect()->route('libraries.index', ['tab' => 'clubs'])->with('status', 'Club Details & Logo updated successfully!');
    }

    public function editClub(LibClubName $club)
    {
        $positions = LibPosition::all(); // Query all standard generic positions.
        // Fetch active users not currently attached to any lib_position.
        $available_users = User::with('club')->where('status', 1)->whereNull('lib_position_id')->orderBy('last_name')->get();
        // Fetch users actively representing this specific club.
        $current_officers = User::with('club')->where('lib_club_name_id', $club->id)->whereNotNull('lib_position_id')->get();
        
        $regions = LibRegion::all(); // Provide region list for standard edit identification
        return view('libraries.clubs.edit', compact('club', 'positions', 'available_users', 'current_officers', 'regions'));
    }

    public function assignClubOfficer(Request $request, LibClubName $club)
    {
        $request->validate([
            'position_id' => 'required|exists:lib_positions,id',
            'user_id' => 'nullable|exists:users,id'
        ]);

        // Nullify whoever holds this exact position in this specific club originally
        User::where('lib_club_name_id', $club->id)
            ->where('lib_position_id', $request->position_id)
            ->update(['lib_position_id' => null]);

        // Securely re-assign new user
        if ($request->user_id) {
            User::where('id', $request->user_id)->update([
                'lib_position_id' => $request->position_id,
                'lib_club_name_id' => $club->id // Automatically pivot them underneath that explicitly specified club as its parent.
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function destroyClub(LibClubName $club)
    {
        $club->delete();
        return redirect()->route('libraries.index', ['tab' => 'clubs'])->with('status', 'Club deleted successfully!');
    }

    // Help Type CRUD
    public function storeHelp(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        LibHelp::create($request->only('name'));
        return redirect()->route('libraries.index', ['tab' => 'help'])->with('status', 'Help type added successfully!');
    }

    public function updateHelp(Request $request, LibHelp $help)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $help->update($request->only('name'));
        return redirect()->route('libraries.index', ['tab' => 'help'])->with('status', 'Help type updated successfully!');
    }

    public function destroyHelp(LibHelp $help)
    {
        $help->delete();
        return redirect()->route('libraries.index', ['tab' => 'help'])->with('status', 'Help type deleted successfully!');
    }

    // Position CRUD
    public function storePosition(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        LibPosition::create($request->only('name'));
        return redirect()->route('libraries.index', ['tab' => 'positions'])->with('status', 'Position added successfully!');
    }

    public function updatePosition(Request $request, LibPosition $position)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $position->update($request->only('name'));
        return redirect()->route('libraries.index', ['tab' => 'positions'])->with('status', 'Position updated successfully!');
    }

    public function destroyPosition(LibPosition $position)
    {
        $position->delete();
        return redirect()->route('libraries.index', ['tab' => 'positions'])->with('status', 'Position deleted successfully!');
    }

    // Regional Position CRUD
    public function storeRegionalPosition(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        LibRegionalPosition::create($request->only('name'));
        return redirect()->route('libraries.index', ['tab' => 'regional-positions'])->with('status', 'Regional Position added successfully!');
    }

    public function updateRegionalPosition(Request $request, LibRegionalPosition $position)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $position->update($request->only('name'));
        return redirect()->route('libraries.index', ['tab' => 'regional-positions'])->with('status', 'Regional Position updated successfully!');
    }

    public function destroyRegionalPosition(LibRegionalPosition $position)
    {
        $position->delete();
        return redirect()->route('libraries.index', ['tab' => 'regional-positions'])->with('status', 'Regional Position deleted successfully!');
    }

    // National Officers CRUD
    public function storeNationalOfficer(Request $request)
    {
        $request->validate([
            'position' => 'required|string|max:255',
            'fullname' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp'
        ]);

        $data = $request->only('position', 'fullname');

        if ($request->hasFile('photo')) {
            $data['photo'] = $this->resizeAndStoreLogo($request->file('photo'));
        }

        LibNationalOfficer::create($data);

        return redirect()->route('libraries.index', ['tab' => 'national-officers'])->with('status', 'National Officer added successfully!');
    }

    public function updateNationalOfficer(Request $request, LibNationalOfficer $officer)
    {
        $request->validate([
            'position' => 'required|string|max:255',
            'fullname' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp'
        ]);

        $data = $request->only('position', 'fullname');

        if ($request->hasFile('photo')) {
            if ($officer->photo) {
                Storage::disk('public')->delete($officer->photo);
            }
            $data['photo'] = $this->resizeAndStoreLogo($request->file('photo'));
        }

        $officer->update($data);
        return redirect()->route('libraries.index', ['tab' => 'national-officers'])->with('status', 'National Officer updated successfully!');
    }

    public function destroyNationalOfficer(LibNationalOfficer $officer)
    {
        if ($officer->photo) {
            Storage::disk('public')->delete($officer->photo);
        }
        $officer->delete();
        return redirect()->route('libraries.index', ['tab' => 'national-officers'])->with('status', 'National Officer deleted successfully!');
    }

    public function getClubsByRegion($region_id)
    {
        if ($region_id === 'all') {
            $clubs = LibClubName::orderBy('name')->get();
        } else {
            $clubs = LibClubName::where('lib_region_id', $region_id)->orderBy('name')->get();
        }
        return response()->json($clubs);
    }

    private function resizeAndStoreLogo($file)
    {
        $maxWidth = 300;
        $maxHeight = 300;

        $sourcePath = $file->getPathname();
        $mime = $file->getMimeType();

        switch ($mime) {
            case 'image/jpeg':
                $sourceImage = @imagecreatefromjpeg($sourcePath);
                break;
            case 'image/png':
                $sourceImage = @imagecreatefrompng($sourcePath);
                break;
            case 'image/webp':
                $sourceImage = @imagecreatefromwebp($sourcePath);
                break;
            case 'image/gif':
                $sourceImage = @imagecreatefromgif($sourcePath);
                break;
            default:
                return $file->store('logos', 'public');
        }

        if (!$sourceImage) {
            return $file->store('logos', 'public');
        }

        $width = imagesx($sourceImage);
        $height = imagesy($sourceImage);

        if ($width > $maxWidth || $height > $maxHeight) {
            $ratio = min($maxWidth / $width, $maxHeight / $height);
            $newWidth = intval($width * $ratio);
            $newHeight = intval($height * $ratio);
        } else {
            $newWidth = $width;
            $newHeight = $height;
        }

        $destinationImage = imagecreatetruecolor($newWidth, $newHeight);
        
        if ($mime == 'image/png' || $mime == 'image/webp' || $mime == 'image/gif') {
            imagealphablending($destinationImage, false);
            imagesavealpha($destinationImage, true);
            $transparent = imagecolorallocatealpha($destinationImage, 255, 255, 255, 127);
            imagefilledrectangle($destinationImage, 0, 0, $newWidth, $newHeight, $transparent);
        }

        imagecopyresampled($destinationImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        $filename = uniqid() . '.webp';
        $logosDir = storage_path('app/public/logos');
        
        if (!file_exists($logosDir)) {
            mkdir($logosDir, 0755, true);
        }

        imagewebp($destinationImage, $logosDir . '/' . $filename, 80);

        imagedestroy($sourceImage);
        imagedestroy($destinationImage);

        return 'logos/' . $filename;
    }
}
